# IT Ticketing System

## Rules
- **DILARANG KERAS** menjalankan `php artisan migrate:fresh`, `php artisan migrate:reset`, atau `php artisan migrate:refresh`. Perintah ini menghapus semua data production. Selalu gunakan `php artisan migrate` saja.
- Never read, display, or commit `.env` files or any file containing secrets/credentials.

## Tech Stack
- Laravel 11, Tailwind CSS v4, Alpine.js, Chart.js, SQLite
- Auth: session-based (username/password), Sanctum for API

## Konvensi
- User model menggunakan UUID (`HasUuids` trait), bukan auto-increment ID.
- Role system: `admin`, `user`. Middleware `role:admin` untuk proteksi route admin-only.
- User hanya bisa dibuat oleh admin (CRUD + batch import CSV).
- Ticket can be manually created via dashboard or received from WhatsApp webhook.
- Ticket supports image & document attachments up to 5MB (JPG, PNG, PDF, DOC, DOCX).
- Views menggunakan `@extends('layouts.app')` + `@section('content')`, bukan Blade component layout.
- Login mengecek `is_active`, user nonaktif ditolak login.

## Deployment
- Gunakan `Makefile` untuk manajemen deployment di VPS:
  - `make setup` untuk install pertama kali di VPS.
  - `make deploy` untuk update full di production (pull git, build assets, cache).
  - `make update` untuk update cepat di production (pull & migrate).
  - `make optimize` untuk cache route & view.
- Nginx config: `docker/nginx/production.conf` (SSL/HTTPS, blokir file sensitif, cache static)
- Deployment guide lengkap: `DEPLOYMENT.md`

## Security Production
- Root folder Nginx HANYA `/public` - .env & Makefile tidak terekspos
- APP_DEBUG=false di .env production
- SSL/HTTPS wajib aktif
- File upload terbatas ekstensi & size (5MB max)
- Backup rutin dengan `make backup`

## Akun Default
- Username: `admin`, Password: `admin123`, Role: `admin`
