import { Head, Link, router, usePage } from '@inertiajs/react';
import { ConversionFunnel } from '@/components/analytics/conversion-funnel';
import { DailyChart } from '@/components/analytics/daily-chart';
import { MetricCard } from '@/components/analytics/metric-card';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AdminLayout from '@/layouts/admin-layout';
import type { TrackingProps } from '@/types/analytics';

type Props = {
    stats: { visits: number; engagements: number; bounce_rate: number; total_leads: number; lead_cr: number; payments: number; sales_cr: number; revenue: number; rpv: number };
    daily: Array<Record<string, number | string>>;
    referrals: Array<{ source: string; visits: number }>;
    funnel: Array<{ event: string; label: string; value: number }>;
    range: number;
    retentionDays: number;
};

const money = (value: number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);

export default function Analytics({ stats, daily, referrals, funnel, range, retentionDays }: Props) {
    const tracking = usePage().props.tracking as TrackingProps;
    const changeRange = (value: string) => router.get('/admin', { range: value }, { preserveState: true });
    return (
        <AdminLayout>
            <Head title="Analytics" />
            <div className="space-y-6 p-6">
                <header className="flex flex-wrap items-end justify-between gap-4">
                    <div><h1 className="text-2xl font-bold">Analytics</h1><p className="text-sm text-muted-foreground">Data live maksimum {retentionDays} hari; periode lebih lama tersedia di tabel arsip.</p></div>
                    <div className="flex gap-2"><select className="rounded-md border bg-background px-3 py-2" value={range} onChange={(event) => changeRange(event.target.value)}>{[7, 30, 90].map((days) => <option key={days} value={days}>{days} hari</option>)}</select><Link href={`/admin/export?range=${range}`} className="rounded-md border px-3 py-2">Export CSV</Link></div>
                </header>
                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <MetricCard title={tracking.eventLabels.visit ?? 'Visit'} value={stats.visits.toLocaleString()} />
                    <MetricCard title={tracking.eventLabels.engagement ?? 'Engagement'} value={stats.engagements.toLocaleString()} />
                    <MetricCard title="Bounce Rate" value={`${stats.bounce_rate}%`} />
                    {tracking.capabilities.total_lead && <MetricCard title="Total Lead" value={stats.total_leads.toLocaleString()} hint={`${stats.lead_cr}% Lead CR`} />}
                    {tracking.mode === 'form' && <MetricCard title={tracking.eventLabels.lead ?? 'Lead'} value={stats.total_leads.toLocaleString()} hint={`${stats.lead_cr}% Lead CR`} />}
                    {tracking.mode === 'form' && <MetricCard title={tracking.eventLabels.payment ?? 'Payment'} value={stats.payments.toLocaleString()} hint={`${stats.sales_cr}% Sales CR`} />}
                    {tracking.capabilities.revenue && <MetricCard title="Revenue" value={money(stats.revenue)} />}
                    {tracking.capabilities.revenue && <MetricCard title="RPV" value={money(stats.rpv)} />}
                </div>
                <div className="grid gap-6 xl:grid-cols-2"><DailyChart rows={daily} /><ConversionFunnel stages={funnel} /></div>
                <Card><CardHeader><CardTitle>Referral Sources</CardTitle></CardHeader><CardContent><table className="w-full text-sm"><thead><tr className="border-b text-left"><th className="py-2">Source</th><th className="py-2 text-right">Visits</th></tr></thead><tbody>{referrals.map((row) => <tr key={row.source} className="border-b"><td className="py-2">{row.source}</td><td className="py-2 text-right">{row.visits}</td></tr>)}</tbody></table></CardContent></Card>
            </div>
        </AdminLayout>
    );
}
