# QA Checklist Pre-launch

## Konfigurasi

- [ ] `APP_DEBUG=false`, HTTPS, database production, queue database.
- [ ] Mode/payment mode sesuai project dan route yang tidak relevan tidak tersedia.
- [ ] ID integrasi kosong tidak memuat script eksternal.
- [ ] Worker Supervisor dan scheduler berjalan.

## Event otomatis

- [ ] Load tiap varian menghasilkan satu Visit per session + landing source.
- [ ] Aktif 15 detik menghasilkan tepat satu Engagement.
- [ ] Heartbeat menaikkan duration tanpa baris event baru.
- [ ] Scroll menghasilkan milestone 25/50/75/90 satu kali.
- [ ] Section tampak 20% selama 500 ms menghasilkan Section View satu kali.
- [ ] Validasi dengan PBM Tracking QC dan data database.

## CTWA

- [ ] CTA hero anchor menghasilkan Intent dengan zone/action benar.
- [ ] WhatsApp pricing/floating menghasilkan Whatsapp Lead sebelum navigasi.
- [ ] Checkout eksternal menghasilkan Direct Checkout.
- [ ] Satu sesi yang melakukan keduanya hanya satu Total Lead.

## FORM

- [ ] Ketikan pertama menghasilkan satu Form Start; ketikan berikutnya tidak.
- [ ] Submit membuat satu lead dan satu event Lead server-side.
- [ ] Extra field tersimpan di `leads.extra`.
- [ ] Mode none menuju thank-you; external menuju URL klien.

## Duitku

- [ ] Internal membuat order pending memakai `PRODUCT_PRICE` server.
- [ ] Sandbox mengembalikan payment URL.
- [ ] Callback sukses mengisi paid/paid_at dan satu Payment.
- [ ] Callback ulang tidak menggandakan Payment.
- [ ] Signature salah dan nominal berbeda mengembalikan 400 tanpa mutasi.
- [ ] Return page hanya membaca status.

## Dashboard

- [ ] Seed dummy membuat `/admin` dan `/admin/labs` terisi.
- [ ] Performance, Split Funnel, Device, CTA, Personas, Scroll, dan Section render.
- [ ] Setiap tahap funnel tidak melebihi tahap sebelumnya.
- [ ] CTWA menyembunyikan Payment/Revenue; FORM menyembunyikan Total Lead.
- [ ] Rentang hanya 7/30/90 hari dan CTA group by zone.

## Meta dan tag manager

- [ ] Pixel Helper menunjukkan event browser yang sesuai.
- [ ] Meta Test Events menunjukkan Browser + Server dengan event ID sama dan dedup.
- [ ] Purchase hanya Server.
- [ ] GTM Preview menampilkan nama internal persis.
- [ ] Clarity menerima landing source dan visitor ID.

## Kebersihan dan release

- [ ] `composer test`, `npm run lint:check`, `npm run types:check`, `npm run build` lulus.
- [ ] 200 event tidak membesarkan Laravel log secara berarti.
- [ ] Referrer/user-agent panjang tidak menyebabkan error.
- [ ] `analytics:archive` dua kali tidak menduplikasi dan live + archive tetap sama.
- [ ] Tidak ada placeholder, secret, credential test, atau data dummy production.
