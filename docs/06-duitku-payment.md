# Duitku Payment

Duitku hanya aktif untuk `PROJECT_MODE=form` dan `PAYMENT_MODE=internal`.

## Setup sandbox

```dotenv
PROJECT_MODE=form
PAYMENT_MODE=internal
PRODUCT_NAME="Nama Produk"
PRODUCT_PRICE=199000
DUITKU_ENV=sandbox
DUITKU_MERCHANT_CODE=DS12345
DUITKU_API_KEY=your-sandbox-api-key
DUITKU_EXPIRY_PERIOD=60
```

Harga frontend hanya tampilan. Controller selalu mengambil `PRODUCT_PRICE` dari server.

## Urutan

```text
Browser -> POST /lead -> leads + event Lead
Browser -> POST /checkout (lead_id)
Server -> Duitku createInvoice
Duitku -> Server: payment_url
Server -> Browser: payment_url
Browser -> Duitku: bayar
Duitku -> POST /payment/callback
Server -> verifikasi signature + amount + idempotency
Server -> orders paid + event Payment + queue CAPI Purchase
Browser -> GET /payment/return (read-only)
```

## Callback lokal dengan ngrok

```bash
php artisan serve
ngrok http 8000
```

Set `APP_URL` ke URL HTTPS ngrok, jalankan `php artisan config:clear`, lalu buat invoice baru. Jangan memakai callback URL invoice lama setelah URL ngrok berubah.

## Status order

- `pending`: invoice dibuat atau callback belum final.
- `paid`: callback kode `00` terverifikasi atau direkonsiliasi admin.
- `failed`: callback kode `01` terverifikasi.

Callback berulang untuk order paid mengembalikan 200 tanpa event kedua. Signature salah atau nominal berbeda mengembalikan 400 dan transaksi tidak berubah. Payload callback disimpan tanpa signature.

## Troubleshooting signature

Signature dihitung `md5(merchantCode + amount + merchantOrderId + apiKey)` dan dibandingkan memakai `hash_equals`. Periksa environment, API key, merchant code, nominal string dari Duitku, serta cache config. Jangan menulis API key atau expected signature ke log.
