# Deployment ke CyberPanel/OpenLiteSpeed

## Persiapan server

Document root harus menunjuk ke `current/public`, bukan root repository. Buat database, user deploy, PHP 8.3+, Composer, Node 22.13+, Supervisor, dan cron.

```bash
cd /home/example.com/public_html/current
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan pbm:create-admin
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Pastikan `storage` dan `bootstrap/cache` dapat ditulis user web server. Atur `APP_ENV=production`, `APP_DEBUG=false`, URL HTTPS, database production, `QUEUE_CONNECTION=database`, dan seluruh secret hanya di `.env` server.

## GitHub Actions

Workflow tersedia di `.github/workflows/deploy.yml`. Buat secrets:

- `VPS_HOST`, `VPS_PORT`, `VPS_USER`, `VPS_SSH_KEY`
- `VPS_PROJECT_PATH`, contoh `/home/example.com/public_html/current`

Workflow menjalankan test/build sebelum upload. File `.env`, storage runtime, dan dependency lokal tidak dikirim.

## Supervisor queue

Simpan `/etc/supervisor/conf.d/pbm-lp-worker.conf`:

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

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart pbm-lp-worker:*
```

## Scheduler dan arsip

Tambahkan cron user aplikasi:

```cron
* * * * * cd /home/example.com/public_html/current && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler menjalankan `analytics:archive` harian. Jangan arsipkan leads/orders.

## Log rotation

Gunakan `LOG_CHANNEL=daily` dan `LOG_DAILY_DAYS=14`. Untuk worker log Supervisor, tambahkan logrotate:

```text
/home/example.com/public_html/current/storage/logs/worker.log {
  daily
  rotate 14
  compress
  missingok
  copytruncate
}
```

Sesudah deploy jalankan smoke test `/up`, demo, login admin, endpoint tracking, queue, dan callback sandbox sebelum membuka traffic.
