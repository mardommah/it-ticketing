import {
    makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
    makeCacheableSignalKeyStore
} from '@whiskeysockets/baileys';
import { Boom } from '@hapi/boom';
import qrcode from 'qrcode-terminal';
import pino from 'pino';
import axios from 'axios';
import dotenv from 'dotenv';
import express from 'express';
import QRCode from 'qrcode';

dotenv.config();

const app = express();
const port = process.env.PORT || 3000;
let currentQR = null;

// Route to serve the QR code
app.get('/scan', async (req, res) => {
    const token = req.query.token;
    if (token !== process.env.SCAN_TOKEN) {
        return res.status(401).send('<h1>Unauthorized</h1><p>Please provide a valid scan token in the URL.</p>');
    }

    if (!currentQR) {
        return res.send('<h1>QR Code not generated yet or already connected.</h1><p>Check terminal logs for status.</p>');
    }
    
    try {
        const qrImage = await QRCode.toDataURL(currentQR);
        res.send(`
            <!DOCTYPE html>
            <html>
                <head>
                    <title>WhatsApp Bot Scan</title>
                    <meta http-equiv="refresh" content="30">
                    <style>
                        body { 
                            font-family: sans-serif; 
                            display: flex; 
                            flex-direction: column; 
                            align-items: center; 
                            justify-content: center; 
                            height: 100vh; 
                            margin: 0;
                            background-color: #f0f2f5;
                        }
                        .container {
                            background: white;
                            padding: 2rem;
                            border-radius: 1rem;
                            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                            text-align: center;
                        }
                        img { width: 300px; height: 300px; }
                        h1 { color: #128c7e; }
                        p { color: #667781; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>Scan WhatsApp Bot</h1>
                        <p>Open WhatsApp on your phone and scan the code below</p>
                        <img src="${qrImage}" alt="QR Code">
                        <p>Page refreshes automatically every 30 seconds</p>
                    </div>
                </body>
            </html>
        `);
    } catch (err) {
        res.status(500).send('Error generating QR code');
    }
});

app.listen(port, () => {
    console.log(`QR Scanner web interface running at http://localhost:${port}/scan`);
});

const logger = pino({ level: 'info' });

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');
    const { version, isLatest } = await fetchLatestBaileysVersion();

    console.log(`using WA v${version.join('.')}, isLatest: ${isLatest}`);

    const sock = makeWASocket({
        version,
        logger,
        printQRInTerminal: true,
        auth: {
            creds: state.creds,
            keys: makeCacheableSignalKeyStore(state.keys, logger),
        },
        generateHighQualityLinkPreview: true,
    });

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;
        if (qr) {
            currentQR = qr;
            qrcode.generate(qr, { small: true });
        }
        
        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect.error instanceof Boom)
                ? lastDisconnect.error.output.statusCode !== DisconnectReason.loggedOut
                : true;
            console.log('connection closed due to ', lastDisconnect.error, ', reconnecting ', shouldReconnect);
            if (shouldReconnect) {
                connectToWhatsApp();
            }
        } else if (connection === 'open') {
            currentQR = null; // Clear QR once connected
            console.log('opened connection');
        }
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('messages.upsert', async ({ messages, type }) => {
        if (type === 'notify') {
            for (const msg of messages) {
                if (!msg.key.fromMe && msg.message) {
                    const from = msg.key.remoteJid;
                    const isGroup = from.endsWith('@g.us');
                    const participant = msg.key.participant || from;
                    const pushName = msg.pushName || 'Unknown';
                    const messageText = msg.message.conversation ||
                        msg.message.extendedTextMessage?.text ||
                        '';

                    if (messageText) {
                        if (isGroup) {
                            // Group Filtering Logic
                            const allowedGroups = process.env.ALLOWED_GROUPS ? process.env.ALLOWED_GROUPS.split(',').map(g => g.trim()).filter(g => g.length > 0) : [];
                            if (allowedGroups.length > 0 && !allowedGroups.includes(from)) {
                                console.log(`Skipping message from unauthorized group: ${from}`);
                                continue;
                            }
                        }

                        // Refined Keyword Filtering Logic (ensures keywords are detected accurately)
                        const keywords = ['tolong', 'minta tolong', 'lapor', '#lapor', 'kendala', 'mohon bantu', 'mohon', 'error', 'gangguan', 'lemot', 'lambat'];
                        const regex = new RegExp(keywords.map(k => k.startsWith('#') ? k : `\\b${k}\\b`).join('|'), 'i');
                        const isProblem = regex.test(messageText);

                        if (!isProblem) {
                            console.log(`Skipping non-problem message: ${messageText}`);
                            continue;
                        }

                        const sourceType = isGroup ? 'group' : 'private chat';
                        console.log(`New problem detected in ${sourceType} ${from} from ${pushName}: ${messageText}`);

                        try {
                            // Forwarding to Laravel
                            await axios.post(`${process.env.LARAVEL_URL}/api/whatsapp/webhook`, {
                                whatsapp_id: msg.key.id,
                                from: from,
                                participant: participant,
                                pushName: pushName,
                                message: messageText,
                                timestamp: msg.messageTimestamp
                            }, {
                                headers: {
                                    'Authorization': `Bearer ${process.env.WEBHOOK_TOKEN}`
                                }
                            });
                            console.log('Successfully forwarded to Laravel');
                        } catch (error) {
                            console.error('Error forwarding to Laravel:', {
                                message: error.message,
                                status: error.response?.status,
                                data: error.response?.data
                            });
                        }
                    }
                }
            }
        }
    });
}

connectToWhatsApp();
