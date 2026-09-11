# Migration Notes

Boilerplate ditujukan untuk project baru dan tidak melakukan migrasi data historis atau dual-write.

| Nama lama | Nama baru |
|---|---|
| `cta_click` di luar pricing | Intent |
| `conversion` + `wa_inquiry`/`wa_registration` | Whatsapp Lead |
| `conversion` + `checkout_redirect` | Direct Checkout |
| `initiate_checkout` CTWA | Direct Checkout |
| `add_to_cart`/`initiate_checkout` FORM | Form Start |
| `conversion` submit form | Lead |
| `payment` browser | Payment dari callback server |
| `conversions`, `leads`, `purchase`, `sales` | dihapus |
| Visit, Scroll, Engagement, Section View | tetap |

## Perubahan angka

- Intent baru hanya CTA non-conversion. Klik WhatsApp/checkout tidak lagi ikut Intent, sehingga Intent akan lebih rendah dan funnel lebih benar.
- Engagement dihitung sebagai `visits - bounces`, sehingga Engagement Rate selalu menjadi negasi Bounce Rate. Event aktif 15 detik tetap menjadi salah satu sinyal yang mengubah sesi menjadi engaged.
- Total Lead CTWA memakai distinct session gabungan WhatsApp + direct checkout; satu sesi yang melakukan keduanya dihitung satu.
- Payment hanya berasal dari callback/sign-off admin dengan nominal database. Refresh thank-you page tidak membuat sale baru.
- Dashboard hanya membaca 90 hari live; data lebih lama berada di tabel arsip.

Saat menjelaskan laporan bulanan, beri batas tegas antara tanggal cutover sistem lama dan baru. Jangan menyambungkan dua seri Intent/Engagement seolah definisinya sama.
