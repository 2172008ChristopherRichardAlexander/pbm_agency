# Meta Pixel dan Conversions API

## Setup

```dotenv
META_PIXEL_ID=123456789
META_ACCESS_TOKEN=token-dari-events-manager
META_TEST_EVENT_CODE=TEST12345
META_CAPI_ENABLED=true
META_CAPI_LOG_ENABLED=false
```

Pixel berjalan di browser. CAPI dijalankan worker queue. Jika pixel ID atau access token kosong, CAPI berhenti tanpa exception.

## Mapping

| Event internal | Meta event | Pixel | CAPI |
|---|---|---|---|
| Visit | PageView | ya | ya |
| Engagement | ViewContent | ya | ya |
| Intent | custom Intent | ya | ya |
| Direct Checkout | InitiateCheckout | ya | ya |
| Whatsapp Lead | Lead | ya | ya |
| Form Start | InitiateCheckout | ya | ya |
| Lead | Lead | setelah server mengonfirmasi | ya |
| Payment | Purchase | tidak | ya |
| Scroll, Section View | tidak dikirim | tidak | tidak |

Mapping didefinisikan di `MetaEventMapper`. `event_id` browser diteruskan ke event internal dan job CAPI sehingga Events Manager dapat melakukan dedup.

Email dinormalisasi lowercase + trim; telepon menjadi digit E.164 tanpa `+`; keduanya di-hash SHA-256 sebelum request Meta.

## Verifikasi Test Events

1. Isi Test Event Code dan jalankan `php artisan queue:work --tries=3`.
2. Buka Events Manager → Test Events.
3. Buka demo, klik CTA, atau submit form.
4. Pastikan event menunjukkan Browser dan Server serta deduplicated, bukan dua conversion.
5. Selesaikan payment sandbox; Purchase harus berasal dari Server saja.
6. Kosongkan Test Event Code sebelum production.

## Troubleshooting

- Tidak ada Server event: pastikan worker hidup, `QUEUE_CONNECTION=database`, migration jobs sudah dijalankan, token/pixel terisi.
- Event dua kali: bandingkan `event_id` Pixel dan CAPI di Test Events.
- Match quality rendah: pastikan lead mengandung email/telepon yang benar; jangan log PII.
- Perlu audit request: aktifkan `META_CAPI_LOG_ENABLED=true` sementara dan baca `meta_capi_logs`. Matikan setelah selesai.
- Jangan menambah logging CAPI ke `laravel.log`.
