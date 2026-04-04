# IT E-Ticketing System

An event-driven IT ticketing application integrated with WhatsApp for real-time issue detection and management.

## Tech Stack
- **Backend:** Laravel (v11+)
- **Frontend:** Tailwind CSS (v4)
- **WhatsApp Integration:** Node.js + [Baileys](https://github.com/WhiskeySockets/Baileys)

---

## 1. Prerequisites
Ensure you have the following installed:
- PHP 8.2+ & Composer
- Node.js 18+ & NPM/Yarn
- MySQL or SQLite

---

## 2. Laravel Backend Setup

1. **Install Dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration:**
   Copy `.env.example` to `.env` and configure your database and webhook token:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edit `.env` and set:*
   - `DB_DATABASE=it_ticketing`
   - `WEBHOOK_TOKEN=your_secure_token_here`

3. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

4. **Build Frontend Assets:**
   ```bash
   npm run build
   ```

---

## 3. WhatsApp Bot Setup

1. **Navigate to Bot Directory:**
   ```bash
   cd whatsapp-bot
   ```

2. **Install Dependencies:**
   ```bash
   npm install
   ```

3. **Configure Bot Environment:**
   Create a `.env` file in the `whatsapp-bot` folder:
   ```bash
   WEBHOOK_URL=http://localhost:8000/api/whatsapp/webhook
   WEBHOOK_TOKEN=your_secure_token_here
   ```

---

## 4. Running the System

To run the full system, you need to start both the Laravel server and the WhatsApp bot service.

### Start Laravel Server:
```bash
php artisan serve
```
*Accessible at: [http://localhost:8000](http://localhost:8000)*

### Start WhatsApp Bot:
In a separate terminal:
```bash
cd whatsapp-bot
npm start
```
*Note: On the first run, scan the QR code displayed in the terminal with your target WhatsApp account.*

---

## 5. Usage
1. Join the IT Support group with the bot account.
2. Any message sent to the group will automatically create a ticket in the dashboard.
3. Access the dashboard at `/dashboard` to recap and assign tickets.
4. Access analytics at `/analytics` for issue reports.
## 6. Local Setup via XAMPP/LAMPP

If you prefer using XAMPP/LAMPP instead of `php artisan serve`:

1. **Move Project:** Place the `it-ticketing` folder inside your `htdocs` (XAMPP) or `/var/www/html` (LAMPP) directory.
2. **Virtual Host (Recommended):**
   - Configure a virtual host pointing to the `/public` directory of the project.
   - Example (Apache):
     ```apache
     <VirtualHost *:80>
         DocumentRoot "C:/xampp/htdocs/it-ticketing/public"
         ServerName it-ticketing.local
     </VirtualHost>
     ```
   - Update your `hosts` file to include `127.0.0.1 it-ticketing.local`.
3. **Database:** Create a database via phpMyAdmin and update your `.env` accordingly.
4. **Permissions (LAMPP):** Ensure the `storage` and `bootstrap/cache` directories are writable:
   ```bash
   sudo chmod -R 775 storage bootstrap/cache
   sudo chown -R www-data:www-data .
   ```
5. **Access:** Visit `http://it-ticketing.local` or `http://localhost/it-ticketing/public`.
