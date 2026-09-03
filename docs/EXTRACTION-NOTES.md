# Extraction Notes

Dokumen ini mencatat hasil audit tiga repository produksi sebelum boilerplate dibangun. Referensi dipakai sebagai sumber pola yang sudah terbukti; taxonomy dan keputusan desain tetap mengikuti requirement brief.

## Sumber dan bagian yang di-port

### `project-fullbright` (CTWA)

- `resources/js/hooks/use-analytics.tsx`: pola deduplikasi visit berbasis `sessionStorage`, termasuk pending key dan rollback ketika request gagal.
- `resources/js/hooks/use-scroll-tracking.tsx`: milestone scroll dan throttling.
- Controller/service analytics: pola payload CTWA dan pengelompokan CTA, lalu disederhanakan menjadi taxonomy baru berbasis `action`.
- Dashboard Analytics dan A/B Labs: dipakai sebagai pembanding perilaku mode CTWA.

### `project-gacf` (FORM)

- Alur tracking formulir: pola `form_start`, submit, dan data atribusi form.
- `resources/js/hooks/use-section-tracking.tsx`: implementasi section auto-discovery, dwell, mutation observer, dan deduplikasi.
- Migration `preserve_analytics_when_users_are_deleted`: pola foreign key `user_id` dengan `nullOnDelete`.
- Konfigurasi capability mode FORM dan pengujian dashboard.

### `project-pbm` (FORM + Duitku)

- `DuitkuService`, `CheckoutController`, `PaymentCallbackController`, model/order migration, dan test payment sebagai dasar integrasi payment internal.
- `AnalyticsMetricsService`, `LabsController`, dan UI A/B Labs sebagai dasar dashboard paling lengkap.
- Migration generated columns analytics sebagai dasar optimasi query MySQL.
- Komponen dashboard analytics/labs dan seeder data sebagai dasar UI boilerplate.

## Perubahan wajib saat port

- Gunakan `App\Analytics\EventType` sebagai sumber taxonomy backend dan mirror TypeScript sebagai satu-satunya sumber event frontend.
- Hapus diskriminator `event_data.type`; resolusi CTA ditentukan oleh `action`, sedangkan `zone` hanya dimensi laporan.
- Hitung Engagement dari event `engagement`, bukan `visits - bounces`; Intent tidak mencakup lead/checkout.
- Tambahkan `analytics_sessions`, endpoint heartbeat, `visitor_id`, cookie `pbm_vid`, dan archive 90 hari.
- Pindahkan Meta CAPI ke queue serta satukan mapping di `MetaEventMapper`.
- Tulis `lead` dan `payment` dari server; callback Duitku harus terverifikasi dan idempoten.
- Batasi seluruh input eksternal sebelum insert agar tidak terjadi overflow.
- Pertahankan dashboard, tetapi label event dikirim dari `EventType::label()` dan rentang tanggal maksimum 90 hari.

## Yang tidak boleh ditiru

- Sinonim event lama (`conversion`, `conversions`, `cta_click`, `add_to_cart`, `purchase`, dan lainnya).
- `LEAD_CONVERSION_TYPES`, `LEGACY_CHECKOUT_CONVERSION_TYPES`, `lead_subtypes`, atau query yang membaca `event_data.type` untuk klasifikasi.
- Meta CAPI sinkron di request tracking dan logging rutin ke `laravel.log`.
- Event payment dari browser atau nominal transaksi dari request frontend.
- Heartbeat yang membuat banyak baris engagement.
- Parsing user agent berulang di setiap query dashboard.
- PostHog, email verification, 2FA, registrasi publik, dan fitur produk/landing page spesifik klien.

## Strategi fondasi

`project-pbm` dipakai sebagai starter Laravel/Inertia/React karena mempunyai dashboard dan payment terlengkap. File visual, aset, fitur klien, PostHog, dan integrasi di luar brief dibuang. Pola CTWA dari Fullbright dan pola FORM dari GACF kemudian dimasukkan melalui konfigurasi mode tunggal.
