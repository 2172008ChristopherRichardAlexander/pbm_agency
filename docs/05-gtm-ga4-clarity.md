# GTM, GA4, dan Microsoft Clarity

## Konfigurasi

```dotenv
GTM_CONTAINER_ID=GTM-XXXXXXX
GA4_MEASUREMENT_ID=
CLARITY_PROJECT_ID=xxxxxxxxxx
```

Jika GTM terisi, GA4 langsung tidak dimuat karena GA4 harus dikelola dari container GTM. Jika GTM kosong dan GA4 terisi, aplikasi memasang gtag.js. Semua kosong berarti tidak ada script pihak ketiga.

## Struktur dataLayer

Setiap event internal mendorong payload berikut:

```js
{
  event: 'whatsapp_lead',
  zone: 'pricing',
  action: 'whatsapp',
  cta_label: 'Chat Sekarang',
  landing_source: '/hero-1',
  value: undefined,
  currency: undefined
}
```

Nama `event` persis sama dengan taxonomy. Buat Custom Event Trigger GTM menggunakan nama itu; jangan membuat alias.

## Verifikasi GTM Preview

1. Buka Tag Assistant Preview dan hubungkan URL staging.
2. Buka landing page: periksa Visit.
3. Klik anchor: periksa Intent dan properti zone/action.
4. Scroll: periksa milestone Scroll.
5. Klik WhatsApp: periksa Whatsapp Lead sebelum tab berpindah.
6. Pastikan GA4 Configuration tag hanya dimuat sekali.

Clarity otomatis menerima `landing_source` lewat `clarity('set', ...)` dan visitor UUID lewat `clarity('identify', ...)`. Gunakan custom tag tersebut saat memfilter recording A/B.
