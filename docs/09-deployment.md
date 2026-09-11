# Deployment Production

Deployment adalah proses memindahkan aplikasi ke server yang melayani pengguna nyata. Contoh ini ditujukan untuk VPS dengan CyberPanel/OpenLiteSpeed, tetapi prinsipnya berlaku untuk web server lain.

## Arsitektur production minimum

- Domain dengan HTTPS.
- PHP 8.3+ dan extension Laravel.
- MySQL/MariaDB.
- Composer 2.
- Node.js 22.13+ hanya diperlukan pada tahap build.
- Web server dengan document root menuju folder `public`.
- Queue worker yang selalu hidup.
- Cron yang menjalankan scheduler setiap menit.

Misalnya source code berada di:

```text
/home/example.com/public_html/current
```

Document root harus:

```text
/home/example.com/public_html/current/public
```

Jangan arahkan document root ke root repository karena file `.env` dan source code dapat terekspos.

## 1. Siapkan source dan environment

Di server:

```bash
cd /home/example.com/public_html/current
cp .env.example .env
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan key:generate
```

Isi `.env` production:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com
LOG_CHANNEL=daily
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

Isi juga database, mode project, serta credential integrasi. Lihat [Referensi Environment](12-environment-reference.md).

## 2. Build frontend

Jika build dilakukan di server:

```bash
npm ci
npm run build
```

`npm ci` memasang dependency persis dari `package-lock.json`. Hasil build berada di `public/build`.

Jika menggunakan GitHub Actions bawaan, build dilakukan oleh workflow dan hasilnya dikirim sebagai release artifact.

## 3. Siapkan Laravel

```bash
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan pbm:create-admin
```

Option `--force` mengizinkan migration berjalan pada production. Pastikan folder `storage` dan `bootstrap/cache` dapat ditulis oleh user web server.

## 4. Queue worker dengan Supervisor

Supervisor menjaga worker tetap hidup dan menjalankannya kembali jika proses berhenti. Buat `/etc/supervisor/conf.d/pbm-lp-worker.conf`:

```ini
[program:pbm-lp-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/example.com/public_html/current/artisan queue:work database --sleep=3 --tries=3 --timeout=60
directory=/home/example.com/public_html/current
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=example
numprocs=2
redirect_stderr=true
stdout_logfile=/home/example.com/public_html/current/storage/logs/worker.log
stopwaitsecs=3600
```

Sesuaikan path dan user, lalu aktifkan:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart pbm-lp-worker:*
```

Periksa status:

```bash
sudo supervisorctl status
```

## 5. Scheduler dengan cron

Tambahkan cron pada user aplikasi:

```cron
* * * * * cd /home/example.com/public_html/current && php artisan schedule:run >> /dev/null 2>&1
```

Cron memanggil scheduler setiap menit. Laravel menentukan task yang benar-benar dijalankan. Project ini menjadwalkan `analytics:archive` setiap hari pukul 02:30.

## 6. GitHub Actions

Workflow `.github/workflows/deploy.yml` berjalan saat branch `main` menerima push atau ketika dijalankan manual. Workflow melakukan validasi, test, build, membuat archive release, mengirimnya ke VPS, menjalankan migration, mengoptimalkan Laravel, dan restart queue.

Buat GitHub Environment bernama `production`, lalu tambahkan secrets:

| Secret | Isi |
|---|---|
| `VPS_HOST` | Host/IP server |
| `VPS_PORT` | Port SSH, biasanya 22 |
| `VPS_USER` | User SSH untuk deployment |
| `VPS_SSH_KEY` | Private key SSH |
| `VPS_PROJECT_PATH` | Path absolut project di server |

File `.env` server tidak dikirim workflow dan tetap dipertahankan di server.

## 7. Update berikutnya

Urutan aman untuk deployment manual:

```bash
php artisan down
git pull origin main
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
php artisan queue:restart
php artisan up
```

Maintenance mode dari `artisan down` mencegah pengguna mengakses aplikasi saat file dan database belum sinkron.

## 8. Log rotation

Gunakan:

```dotenv
LOG_CHANNEL=daily
LOG_DAILY_DAYS=14
```

Untuk log Supervisor, tambahkan konfigurasi logrotate:

```text
/home/example.com/public_html/current/storage/logs/worker.log {
  daily
  rotate 14
  compress
  missingok
  copytruncate
}
```

## 9. Smoke test setelah deploy

Smoke test adalah pemeriksaan singkat bahwa fungsi utama hidup:

1. Buka `/up` dan pastikan HTTP 200.
2. Buka landing page melalui HTTPS.
3. Login dan buka `/admin` serta `/admin/labs`.
4. Klik satu CTA dan pastikan event muncul.
5. Submit form pada mode FORM.
6. Periksa status worker dan failed jobs.
7. Uji callback sandbox jika menggunakan Duitku.
8. Periksa Meta Test Events/GTM Preview/Clarity jika diaktifkan.

Selesaikan [Checklist QA](11-qa-checklist.md) sebelum membuka traffic campaign.
