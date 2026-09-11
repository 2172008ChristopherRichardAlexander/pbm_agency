# Dashboard Guide

Dashboard memerlukan login user dengan role admin. Buat melalui `php artisan pbm:create-admin`.

## Analytics (`/admin`)

- Metric cards umum: Visit, Engagement Rate, Intent Rate, dan Bounce Rate.
- Mode CTWA menampilkan Whatsapp Lead, Direct Checkout, Total Lead (distinct union kedua outcome), dan Lead CR.
- Mode FORM menampilkan Form Start, Lead, serta Payment/Revenue sesuai capability project.
- Funnel Trends: chart harian multi-series untuk seluruh event funnel pada mode aktif.
- Conversion Funnel: sesi harus melewati tahap sebelumnya. Pada CTWA, Direct Checkout dan Whatsapp Lead adalah dua cabang sejajar setelah Intent, bukan dua tahap berurutan.
- Referral Sources: pie chart distinct session Visit per referrer.
- Key Insights: referral teratas, channel CTWA utama atau Lead CR, dan Revenue per Visit/retensi.
- Export CSV: event mentah untuk rentang aktif.

Preset rentang tersedia untuk 3, 5, 7, 14, 30, atau 90 hari. Data lebih lama berada di tabel arsip dan sengaja tidak dimasukkan dashboard.

## A/B Labs (`/admin/labs`)

- Filter: preset/custom date maksimal 90 hari, referral source, dan landing page.
- Performance Matrix: tabel sortable, pagination, mobile cards, eligibility, dan winner per landing source.
- Split Funnel: grouped chart dan tabel per varian. CTWA menggunakan dua outcome branch.
- Device Performance: visits, Total Lead, dan breakdown outcome CTWA dari `analytics_sessions.device_type`.
- CTA Performance: attribution berdasarkan kombinasi `cta_zone` dan `cta_action`.
- Personas: Bouncers, Skimmers, Deep Readers, dan Casuals dari ringkasan session.
- Scroll Heatmap: milestone Scroll 25/50/75/90.
- Section Heatmap: jangkauan dan drop-off antar-section.
- Behavior Analysis: perbandingan scroll dan dwell Total Lead vs non-lead.

## Menentukan pemenang

Jangan memilih pemenang sebelum setiap varian mencapai `ANALYTICS_MINIMUM_WINNER_VISITS`. Primary metric CTWA adalah Total Lead; FORM adalah Lead. Periksa juga penurunan ekstrem pada tahap sebelumnya, konsistensi antar-device, dan kualitas lead. Dashboard memberi arah, bukan pengganti validasi statistik/komersial.

Intent dan Engagement tidak comparable dengan project lama; lihat migration notes.
