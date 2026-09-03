# PBM Landing Page Boilerplate

Fondasi Laravel + Inertia + React untuk project landing page PBM Agency. Satu repository mendukung mode CTWA dan FORM, dashboard Analytics, A/B Labs, Meta Pixel + CAPI, GTM/GA4/Clarity, serta Duitku opsional.

## Quick start

1. Clone repository dan masuk ke direktorinya.
2. Jalankan `composer install && npm install`.
3. Salin `.env.example` menjadi `.env`, isi database, `CLIENT_ID`, dan `PROJECT_MODE`.
4. Jalankan `php artisan key:generate && php artisan migrate --seed`.
5. Buat admin dengan `php artisan pbm:create-admin`, lalu jalankan `composer dev`.

Gunakan Node.js 22.13+ dan PHP 8.3+. Worker queue wajib berjalan karena Meta CAPI dikirim asynchronous.

## Peta dokumentasi

- [Getting started](docs/01-getting-started.md)
- [Taxonomy analytics](docs/02-analytics-events.md)
- [Wiring frontend](docs/03-frontend-wiring.md)
- [Meta Pixel + CAPI](docs/04-meta-pixel-capi.md)
- [GTM, GA4, Clarity](docs/05-gtm-ga4-clarity.md)
- [Duitku](docs/06-duitku-payment.md)
- [Mode project](docs/07-project-modes.md)
- [Panduan dashboard](docs/08-dashboard-guide.md)
- [Deployment](docs/09-deployment.md)
- [Catatan migrasi](docs/10-migration-notes.md)
- [QA checklist](docs/11-qa-checklist.md)
- [Extraction notes](docs/EXTRACTION-NOTES.md)
- [Keputusan teknis](docs/DECISIONS.md)

## Prinsip utama

Nama event hanya didefinisikan di `app/Analytics/EventType.php` dan `resources/js/analytics/event-types.ts`. Jangan menulis sinonim atau membaca `event_data.type`. Untuk CTA gunakan `TrackedCTA`; untuk form gunakan `TrackedForm`; section biasa cukup memiliki `id`.
