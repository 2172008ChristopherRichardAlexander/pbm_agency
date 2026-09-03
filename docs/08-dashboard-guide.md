# Dashboard Guide

Dashboard memerlukan login user dengan role admin. Buat melalui `php artisan pbm:create-admin`.

## Analytics (`/admin`)

- Metric cards: volume Visit/Engagement, Bounce Rate, Lead CR, Payment/Sales CR, Revenue, dan RPV sesuai capability mode.
- Daily Visits: tren volume per tanggal.
- Conversion Funnel: sesi harus melewati tahap sebelumnya, sehingga angka tidak meningkat ke bawah.
- Referral Sources: distinct session Visit per referrer.
- Export CSV: event mentah untuk rentang aktif.

Rentang hanya 7, 30, atau 90 hari. Data lebih lama berada di tabel arsip dan sengaja tidak dimasukkan dashboard.

## A/B Labs (`/admin/labs`)

- Performance Matrix: event per landing source dan metrik utama.
- Split Funnel: funnel kumulatif per varian.
- Device Performance: visits/leads dari `analytics_sessions.device_type`.
- CTA Performance: group by `cta_zone` dan action.
- Personas: Skimmer, Engaged Reader, dan Deep Reader dari duration + max scroll.
- Scroll Heatmap: milestone Scroll.
- Section Heatmap: jangkauan section, diurutkan first seen.

## Menentukan pemenang

Jangan memilih pemenang sebelum setiap varian mencapai `ANALYTICS_MINIMUM_WINNER_VISITS`. Primary metric CTWA adalah Total Lead; FORM adalah Lead. Periksa juga penurunan ekstrem pada tahap sebelumnya, konsistensi antar-device, dan kualitas lead. Dashboard memberi arah, bukan pengganti validasi statistik/komersial.

Intent dan Engagement tidak comparable dengan project lama; lihat migration notes.
