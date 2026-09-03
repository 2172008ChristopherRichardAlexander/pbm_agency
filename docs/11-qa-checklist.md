# QA Checklist Pre-launch

## Konfigurasi

- [ ] `APP_DEBUG=false`, HTTPS, database production, queue database.
- [x] Mode/payment mode sesuai project dan route yang tidak relevan tidak tersedia (otomatis).
- [x] ID integrasi kosong tidak memuat script eksternal (otomatis).
- [ ] Worker Supervisor dan scheduler berjalan.

## Event otomatis

- [ ] Load tiap varian menghasilkan satu Visit per session + landing source.
- [ ] Aktif 15 detik menghasilkan tepat satu Engagement.
- [x] Heartbeat menaikkan duration tanpa baris event baru (otomatis).
- [ ] Scroll menghasilkan milestone 25/50/75/90 satu kali.
- [ ] Section tampak 20% selama 500 ms menghasilkan Section View satu kali.
- [ ] Validasi dengan PBM Tracking QC dan data database.

## CTWA

- [ ] CTA hero anchor menghasilkan Intent dengan zone/action benar.
- [ ] WhatsApp pricing/floating menghasilkan Whatsapp Lead sebelum navigasi.
- [ ] Checkout eksternal menghasilkan Direct Checkout.
- [x] Satu sesi yang melakukan keduanya hanya satu Total Lead (otomatis).

## FORM

- [ ] Ketikan pertama menghasilkan satu Form Start; ketikan berikutnya tidak.
- [x] Submit membuat satu lead dan satu event Lead server-side (otomatis).
- [ ] Extra field tersimpan di `leads.extra`.
- [x] Mode none menuju thank-you; external menuju URL klien (otomatis).

## Duitku

- [x] Internal membuat order pending memakai `PRODUCT_PRICE` server (otomatis).
- [ ] Sandbox mengembalikan payment URL.
- [x] Callback sukses mengisi paid/paid_at dan satu Payment (otomatis).
- [x] Callback ulang tidak menggandakan Payment (otomatis).
- [x] Signature salah dan nominal berbeda mengembalikan 400 tanpa mutasi (otomatis).
- [ ] Return page hanya membaca status.

## Dashboard

- [x] Seed dummy membuat `/admin` dan `/admin/labs` terisi (otomatis).
- [ ] Performance, Split Funnel, Device, CTA, Personas, Scroll, dan Section render.
- [x] Setiap tahap funnel tidak melebihi tahap sebelumnya (otomatis).
- [ ] CTWA menyembunyikan Payment/Revenue; FORM menyembunyikan Total Lead.
- [x] Rentang hanya 7/30/90 hari dan CTA group by zone (otomatis + inspeksi).

## Meta dan tag manager

- [ ] Pixel Helper menunjukkan event browser yang sesuai.
- [ ] Meta Test Events menunjukkan Browser + Server dengan event ID sama dan dedup.
- [x] Purchase hanya Server (otomatis: event Payment dari browser ditolak).
- [ ] GTM Preview menampilkan nama internal persis.
- [x] Clarity menerima landing source dan visitor ID (otomatis pada HTML output).

## Kebersihan dan release

- [x] `composer test`, `npm run lint:check`, `npm run types:check`, `npm run build` lulus.
- [ ] 200 event tidak membesarkan Laravel log secara berarti.
- [x] Referrer/user-agent panjang tidak menyebabkan error (otomatis).
- [x] `analytics:archive` dua kali tidak menduplikasi dan live + archive tetap sama (otomatis).
- [x] Tidak ada secret atau credential test di source yang dilacak (inspeksi).

Butir yang masih kosong membutuhkan browser, layanan eksternal, worker/scheduler production, atau kredensial klien. Jalankan kembali semuanya pada staging sebelum go-live; lihat `QA-REPORT.md` untuk cakupan verifikasi implementasi.
