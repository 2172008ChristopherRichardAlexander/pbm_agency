import { Head } from '@inertiajs/react';
import { TrackedCTA } from '@/components/tracking/TrackedCTA';

export default function CtwaDemo({
    whatsappUrl,
    externalCheckoutUrl,
}: {
    whatsappUrl: string;
    externalCheckoutUrl?: string;
}) {
    const button =
        'inline-flex rounded-xl bg-cyan-400 px-6 py-3 font-bold text-slate-950 shadow-lg shadow-cyan-950/30';

    return (
        <main className="bg-slate-950 text-white">
            <Head title="CTWA Demo" />
            <section
                id="hero"
                className="grid min-h-screen place-items-center px-6 text-center"
            >
                <div className="max-w-3xl">
                    <p className="text-sm font-semibold tracking-[.25em] text-cyan-400 uppercase">
                        PBM Boilerplate · CTWA
                    </p>
                    <h1 className="mt-4 text-5xl font-black tracking-tight sm:text-7xl">
                        Demo tracking yang siap diganti desainnya.
                    </h1>
                    <p className="mx-auto mt-6 max-w-2xl text-lg text-slate-300">
                        Scroll, engagement, section view, dan attribution
                        berjalan otomatis. CTA hanya perlu diberi zone dan
                        action.
                    </p>
                    <TrackedCTA
                        className={`${button} mt-8`}
                        zone="hero"
                        action="scroll"
                        label="Lihat Paket"
                        href="#pricing"
                    >
                        Lihat Paket
                    </TrackedCTA>
                </div>
            </section>
            <section
                id="pricing"
                className="grid min-h-screen place-items-center bg-slate-900 px-6"
            >
                <div className="w-full max-w-xl rounded-3xl border border-slate-700 p-8 text-center">
                    <h2 className="text-3xl font-bold">Pricing Section</h2>
                    <p className="mt-3 text-slate-300">
                        Klik berikut menghasilkan Whatsapp Lead karena
                        action-nya, bukan karena posisi section.
                    </p>
                    <div className="mt-8 flex flex-wrap justify-center gap-3">
                        <TrackedCTA
                            className={button}
                            zone="pricing"
                            action="whatsapp"
                            label="Chat Sekarang"
                            href={whatsappUrl}
                        >
                            Chat Sekarang
                        </TrackedCTA>
                        {externalCheckoutUrl && (
                            <TrackedCTA
                                className="inline-flex rounded-xl border border-slate-600 px-6 py-3 font-semibold"
                                zone="pricing"
                                action="external_checkout"
                                label="Checkout"
                                href={externalCheckoutUrl}
                            >
                                Checkout eksternal
                            </TrackedCTA>
                        )}
                    </div>
                </div>
            </section>
            <section
                id="faq"
                className="grid min-h-[60vh] place-items-center px-6 text-center"
            >
                <div>
                    <h2 className="text-3xl font-bold">FAQ tracked otomatis</h2>
                    <p className="mt-3 text-slate-400">
                        Section ini terdeteksi tanpa komponen tambahan.
                    </p>
                </div>
            </section>
            <TrackedCTA
                className="fixed right-5 bottom-5 rounded-full bg-emerald-500 px-5 py-3 font-bold text-slate-950"
                zone="floating"
                action="whatsapp"
                label="Floating WhatsApp"
                href={whatsappUrl}
            >
                WhatsApp
            </TrackedCTA>
        </main>
    );
}
