# Decisions

- `[ASSUMPTION] 2026-09-03 — CLIENT_ID tetap berupa label event dan belum dijadikan generated/indexed column — brief menyatakan arsitektur saat ini satu database per project dan keputusan agregasi lintas klien belum tersedia.`
- `[DECISION] 2026-09-03 — Fondasi aplikasi diambil dari project-pbm lalu dibersihkan — repository ini paling dekat dengan stack yang diminta dan memiliki dashboard serta Duitku paling lengkap.`
- `[DECISION] 2026-09-03 — SQLite dipakai untuk test suite, sedangkan generated columns lengkap diaktifkan hanya pada MySQL/MariaDB — membuat pengujian lokal deterministik tanpa mengubah skema produksi.`
