# Analytics Events

Dokumen ini adalah kontrak data utama. Backend memakai `EventType.php`; frontend me-mirror nilai yang sama di `event-types.ts`. `event_type` adalah satu-satunya diskriminator. Jangan tambahkan `event_data.type`.

Label dashboard dibentuk mekanis dengan `ucwords(str_replace('_', ' ', event_type))`. Contoh: `whatsapp_lead` menjadi **Whatsapp Lead**. UI menerima label dari backend; label event tidak ditulis manual.

## Funnel events

| Event | Mode | Definisi | Dedup |
|---|---|---|---|
| `visit` | keduanya | Halaman dibuka | sekali per sesi + landing source |
| `engagement` | keduanya | Aktif 15 detik | sekali per sesi |
| `intent` | keduanya | CTA yang tetap di halaman/form | tidak |
| `direct_checkout` | CTWA | Menuju checkout eksternal | tidak |
| `whatsapp_lead` | CTWA | Menuju WhatsApp | tidak |
| `form_start` | FORM | Ketikan pertama pada form | sekali per sesi + form |
| `lead` | FORM | Form tersimpan oleh server | sekali per submit |
| `payment` | FORM internal | Callback sukses terverifikasi | sekali per order number |

## Supporting events

Supporting event tidak menjadi tahap funnel, tetapi wajib untuk analisis.

| Event | Fungsi |
|---|---|
| `scroll` | Bounce, Scroll Heatmap, dan persona; milestone 25/50/75/90. |
| `section_view` | Section Heatmap; terlihat minimal 20% selama 500 ms. |

## Metrik turunan

Metrik berikut **tidak mempunyai event pasangan**. Jangan mencari atau membuat event bernama `bounce`, `total_lead`, `lead_cr`, `sales_cr`, `revenue`, atau `rpv`.

- Engagement: seluruh visit yang tidak berstatus bounce. Engagement Rate selalu merupakan negasi Bounce Rate.
- Bounce: visit tanpa durasi aktif minimum, intent, aksi funnel, atau scroll di atas 25%.
- Total Lead CTWA: distinct session dari gabungan Whatsapp Lead dan Direct Checkout.
- Total Lead FORM: sama dengan Lead, sehingga card Total Lead disembunyikan.
- Lead CR: Total Lead dibagi Visit.
- Sales CR: Payment dibagi Visit.
- Revenue: jumlah `payment_amount` yang berasal dari callback server.
- RPV: Revenue dibagi Visit.

## Resolusi CTA

Zone valid: `hero`, `pricing`, `sticky`, `floating`, `footer`, `midpage`, `faq`, `nav`.

Action valid: `whatsapp`, `external_checkout`, `form_anchor`, `internal_checkout`, `scroll`, `link`.

| Mode | Action | Event hasil |
|---|---|---|
| CTWA | `whatsapp` | Whatsapp Lead |
| CTWA | `external_checkout` | Direct Checkout |
| CTWA | `scroll`, `link`, `form_anchor` | Intent |
| FORM | `form_anchor`, `scroll`, `link` | Intent |
| FORM | `internal_checkout` | Intent; Payment datang dari server |

Event ditentukan dari `action`, bukan `zone`. Karena itu floating WhatsApp tetap lead dan perubahan posisi pricing tidak mengubah metrik.

## Payload contoh

```json
{
  "event_type": "intent",
  "event_data": {
    "event_id": "0198f810-2e91-7ef1-a08b-f8380c5dd642",
    "landing_source": "/hero-1",
    "zone": "hero",
    "action": "scroll",
    "cta_label": "Lihat Paket"
  }
}
```

`payment` dan `lead` ditolak oleh endpoint browser. Keduanya ditulis server. Event yang tidak sah untuk mode aktif mendapat HTTP 422 beserta daftar event sah.
