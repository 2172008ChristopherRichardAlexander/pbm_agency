# Project Modes

## Pilih mode

| Kebutuhan | PROJECT_MODE | PAYMENT_MODE |
|---|---|---|
| CTA utama WhatsApp | `ctwa` | tidak dipakai |
| Form lalu halaman terima kasih | `form` | `none` |
| Form lalu payment klien | `form` | `external` |
| Form lalu Duitku | `form` | `internal` |

## Perbedaan capability

| Capability | CTWA | FORM |
|---|---|---|
| Lead source | WhatsApp + direct checkout | Submit form server |
| Route lead/checkout | tidak didaftarkan | didaftarkan |
| Total Lead card | tampil | disembunyikan; sama dengan Lead |
| Payment/Revenue | mati | hidup hanya internal |
| Demo root | `demo/ctwa` | `demo/form` |

## Contoh CTWA

```dotenv
CLIENT_ID=fullbright
PROJECT_MODE=ctwa
WHATSAPP_NUMBER=628123456789
WHATSAPP_DEFAULT_MESSAGE="Halo, saya tertarik."
EXTERNAL_CHECKOUT_URL=https://client.example/checkout
```

## Contoh FORM external

```dotenv
CLIENT_ID=gacf
PROJECT_MODE=form
PAYMENT_MODE=external
EXTERNAL_PAYMENT_URL=https://client.example/pay
THANK_YOU_PATH=/terima-kasih
```

Setelah mengganti mode pada server dengan config cache, jalankan `php artisan config:clear && php artisan config:cache` dan restart worker queue.
