# Frontend Wiring

Tracking visit, engagement, heartbeat, scroll, dan section aktif otomatis melalui `AnalyticsBootstrap`. Developer hanya memasang wrapper pada CTA dan form.

## 1. WhatsApp di pricing

```tsx
import { TrackedCTA } from '@/components/tracking/TrackedCTA';

export function PricingWhatsApp({ waLink }: { waLink: string }) {
    return <TrackedCTA zone="pricing" action="whatsapp" label="Chat Sekarang" href={waLink}>Chat Sekarang</TrackedCTA>;
}
```

## 2. Anchor hero ke pricing

```tsx
import { TrackedCTA } from '@/components/tracking/TrackedCTA';

export function HeroCTA() {
    return <TrackedCTA zone="hero" action="scroll" label="Lihat Paket" href="#pricing">Lihat Paket</TrackedCTA>;
}
```

## 3. Checkout eksternal

```tsx
import { TrackedCTA } from '@/components/tracking/TrackedCTA';

export function ExternalCheckout({ url }: { url: string }) {
    return <TrackedCTA zone="pricing" action="external_checkout" label="Checkout" href={url}>Checkout</TrackedCTA>;
}
```

## 4. Form + payment internal

Set `PROJECT_MODE=form`, `PAYMENT_MODE=internal`, harga, dan Duitku. Wrapper otomatis POST lead, POST checkout, lalu membuka URL Duitku.

```tsx
import { TrackedForm } from '@/components/tracking/TrackedForm';

export function RegistrationForm() {
    return <TrackedForm formName="main" onError={(message) => alert(message)}>
        <input name="name" required />
        <input name="email" type="email" />
        <input name="phone" required />
        <button type="submit">Bayar sekarang</button>
    </TrackedForm>;
}
```

## 5. Form + redirect eksternal

Set `PAYMENT_MODE=external` dan `EXTERNAL_PAYMENT_URL`. Redirect dilakukan dari response server.

```tsx
import { TrackedForm, type LeadResponse } from '@/components/tracking/TrackedForm';

export function ExternalPaymentForm() {
    const redirect = (response: LeadResponse) => window.location.assign(response.redirect_url);
    return <TrackedForm formName="main" onSuccess={redirect}>
        <input name="name" required />
        <input name="phone" required />
        <button type="submit">Lanjut pembayaran</button>
    </TrackedForm>;
}
```

Untuk `PAYMENT_MODE=none`, contoh yang sama menuju `THANK_YOU_PATH`.

## 6. Event manual

```tsx
import { EVENT_TYPES, useAnalytics } from '@/hooks/use-analytics';

export function SyllabusLink() {
    const { track } = useAnalytics();
    return <button onClick={() => track(EVENT_TYPES.intent, { zone: 'faq', action: 'link', cta_label: 'Lihat Silabus' })}>Lihat Silabus</button>;
}
```

## Section

```tsx
export function Pricing() {
    return <section id="pricing">...</section>;
}
```

Hook menemukan `section[id]`, termasuk section lazy-load melalui MutationObserver. Gunakan ID stabil dan unik. `TrackedSection` hanya diperlukan untuk membungkus struktur yang belum berupa elemen section.

## Keandalan

Queue flush setiap dua detik atau sepuluh event. Event yang meninggalkan halaman memakai beacon langsung. Queue di-flush pada `pagehide` dan saat tab menjadi hidden. Pengiriman gagal dicoba sekali lalu dibuang agar navigasi tidak pernah tertahan.
