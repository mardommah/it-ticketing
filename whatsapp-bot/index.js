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

dotenv.config();

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

                        // Advanced Keyword Filtering Logic
                        const keywords = ['tolong', 'minta tolong', 'lapor', '#lapor', 'kendala', 'mohon bantu', 'mohon', 'error', 'gangguan', 'lemot', 'lambat'];
                        const regex = new RegExp(keywords.join('|'), 'i');
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
                            console.error('Error forwarding to Laravel:', error.message);
                        }
                    }
                }
            }
        }
    });
}

connectToWhatsApp();
