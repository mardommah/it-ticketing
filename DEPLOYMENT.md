# IT Ticketing System - Production Deployment Guide

## Prerequisites
- VPS dengan Ubuntu 20.04+ atau Debian 11+
- PHP 8.2 + PHP-FPM
- Nginx
- Composer
- Node.js 18+
- Git
- SSL Certificate (Let's Encrypt)

## Step 1: Setup VPS

```bash
# Clone repository
cd /var/www
git clone https://github.com/your-repo/it-ticketing.git
cd it-ticketing

# Copy environment
cp .env.example .env

# Edit .env dengan production values
# WAJIB: APP_KEY, DB_*, MAIL_*, APP_DEBUG=false
nano .env

# Setup project dengan Makefile
make setup
```

## Step 2: Setup SSL dengan Let's Encrypt

```bash
# Install Certbot
sudo apt update && sudo apt install certbot python3-certbot-nginx -y

# Generate certificate (ganti domain)
sudo certbot certonly --nginx -d ticketing.yourdomain.com

# Update Nginx config path SSL
sudo nano docker/nginx/production.conf
# Sesuaikan baris:
# ssl_certificate /etc/letsencrypt/live/ticketing.yourdomain.com/fullchain.pem;
# ssl_certificate_key /etc/letsencrypt/live/ticketing.yourdomain.com/privkey.pem;
```

## Step 3: Setup Nginx

```bash
# Copy config Nginx
sudo cp docker/nginx/production.conf /etc/nginx/sites-available/ticketing
sudo ln -s /etc/nginx/sites-available/ticketing /etc/nginx/sites-enabled/

# Test & reload nginx
sudo nginx -t
sudo systemctl reload nginx
```

## Step 4: Setup PHP-FPM

```bash
# Edit PHP-FPM pool (sesuaikan socket path)
sudo nano /etc/php/8.2/fpm/pool.d/www.conf

# Pastikan user = www-data, group = www-data
# Pastikan listen = /var/run/php/php8.2-fpm.sock

sudo systemctl restart php8.2-fpm
```

## Step 5: Setup Firewall & Security

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

## Step 6: Setup Cron & Queue (Optional)

```bash
# Tambahkan ke crontab
sudo crontab -e

# Tambahkan line:
* * * * * cd /var/www/it-ticketing && php artisan schedule:run >> /dev/null 2>&1

# Setup supervisor untuk queue (jika diperlukan)
# Ref: https://laravel.com/docs/11.x/queues
```

## Deployment Workflow

### First Time Deploy
```bash
make setup
```

### Update/Hotfix
```bash
make update
```

### Full Production Deploy
```bash
make deploy
```

### Clear Cache After Manual Changes
```bash
make cache-clear
```

## Monitoring & Maintenance

### Check Logs
```bash
tail -f storage/logs/laravel.log
sudo tail -f /var/log/nginx/error.log
```

### Backup Database
```bash
make backup
# Backup stored in ./backups/
```

### SSL Certificate Auto Renewal
```bash
# Certbot auto-renews every 12 hours via cron
# Verify: sudo certbot renew --dry-run
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 403 Forbidden | Check folder permissions: `chmod 755 storage bootstrap/cache` |
| 502 Bad Gateway | Restart PHP-FPM: `sudo systemctl restart php8.2-fpm` |
| Database locked | Wait 30s, atau check: `ps aux \| grep artisan` |
| SSL error | Renew cert: `sudo certbot renew --force-renewal` |

## Security Checklist

- [ ] `.env` file protected (not in web root)
- [ ] APP_DEBUG = false
- [ ] SSL/HTTPS enabled
- [ ] Firewall restricted (only 22, 80, 443)
- [ ] Storage folder outside public (✓ automatic)
- [ ] File upload extensions restricted (✓ automatic)
- [ ] Regular backups scheduled
- [ ] Admin credentials changed from default

## Support

Untuk troubleshooting lebih lanjut, lihat `.claude/CLAUDE.md` untuk konvensi project.
