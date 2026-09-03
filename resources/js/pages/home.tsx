import { Head, Link } from '@inertiajs/react';

export default function Home() {
    return (
        <main className="grid min-h-screen place-items-center bg-slate-950 px-6 text-white">
            <Head title="PBM Landing Page Boilerplate" />
            <section className="max-w-2xl text-center">
                <p className="mb-3 text-sm font-semibold tracking-[0.25em] text-cyan-400 uppercase">PBM Agency</p>
                <h1 className="text-4xl font-bold tracking-tight sm:text-6xl">Landing Page Boilerplate</h1>
                <p className="mt-6 text-lg text-slate-300">
                    Fondasi Laravel, Inertia, React, analytics, A/B Labs, dan payment untuk project landing page PBM.
                </p>
                <Link href="/login" className="mt-8 inline-flex rounded-lg bg-cyan-400 px-5 py-3 font-semibold text-slate-950">
                    Login admin
                </Link>
            </section>
        </main>
    );
}
