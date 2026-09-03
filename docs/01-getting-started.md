# Getting Started

## Kebutuhan

- PHP 8.3+, Composer 2, Node.js 22.13+, npm, MySQL 8/MariaDB yang kompatibel generated column.
- Extension PHP umum Laravel (`pdo_mysql`, `mbstring`, `openssl`, `json`, `curl`).
- Worker queue dan scheduler pada production.

## Instalasi lokal

```bash
git clone https://github.com/pbmagency/boilerplate-lp.git nama-project
cd nama-project
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan pbm:create-admin
composer dev
```

Buka landing page di `http://localhost:8000`, login di `/login`, Analytics di `/admin`, dan A/B Labs di `/admin/labs`.

## Minimum environment

Isi database, lalu minimum konfigurasi project:

```dotenv
CLIENT_ID=nama-klien
PROJECT_MODE=ctwa
```

Semua ID integrasi boleh kosong. Aplikasi dan demo tetap berjalan tanpa Meta, GTM, GA4, Clarity, atau Duitku.

## Checklist klien baru

- [ ] Ganti `APP_NAME`, `APP_URL`, `CLIENT_ID`, dan kredensial database.
- [ ] Pilih `PROJECT_MODE=ctwa` atau `PROJECT_MODE=form`.
- [ ] Untuk CTWA, isi `WHATSAPP_NUMBER`, pesan default, dan opsional checkout eksternal.
- [ ] Untuk FORM, pilih `PAYMENT_MODE`; isi konfigurasi terkait.
- [ ] Ganti demo di `resources/js/pages/demo/` dengan visual landing page klien.
- [ ] Semua CTA memakai `TrackedCTA`; form memakai `TrackedForm`; setiap section memiliki `id` stabil.
- [ ] Buat admin, seed hanya bila data dummy diperlukan, dan hapus data dummy sebelum launch.
- [ ] Jalankan `composer test`, `npm run types:check`, dan `npm run build`.
- [ ] Ikuti `docs/11-qa-checklist.md` di staging.

## Command harian

```bash
php artisan queue:work --tries=3
php artisan schedule:work
php artisan analytics:archive
php artisan db:seed --class=AnalyticsDemoSeeder
```

`analytics:archive` aman dijalankan ulang. Baris live yang lebih tua dari 90 hari dipindahkan per batch ke tabel arsip.
