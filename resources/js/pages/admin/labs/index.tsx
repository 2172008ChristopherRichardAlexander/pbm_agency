import { Head, router, usePage } from '@inertiajs/react';
import { ReportCard } from '@/components/labs/report-card';
import AdminLayout from '@/layouts/admin-layout';
import type { TrackingProps } from '@/types/analytics';

type Row = Record<string, unknown>;
type Funnel = {
    source: string;
    stages: Array<{ event: string; label: string; value: number }>;
};
type Props = {
    performance: Row[];
    funnel: Funnel[];
    devices: Row[];
    ctas: Row[];
    personas: Array<{ source: string; segments: Record<string, number> }>;
    scroll_heatmap: Row[];
    section_heatmap: Row[];
    range: number;
    minimumWinnerVisits: number;
    primaryMetric: string;
    retentionDays: number;
};

function Table({
    rows,
    eventLabels = {},
}: {
    rows: Row[];
    eventLabels?: Record<string, string>;
}) {
    if (rows.length === 0) {
        return (
            <p className="text-sm text-muted-foreground">
                Belum ada data untuk periode ini.
            </p>
        );
    }

    const columns = Object.keys(rows[0]);

    return (
        <table className="w-full text-sm">
            <thead>
                <tr className="border-b">
                    {columns.map((column) => (
                        <th key={column} className="px-2 py-2 text-left">
                            {eventLabels[column] ?? column.replaceAll('_', ' ')}
                        </th>
                    ))}
                </tr>
            </thead>
            <tbody>
                {rows.map((row, index) => (
                    <tr key={index} className="border-b">
                        {columns.map((column) => (
                            <td key={column} className="px-2 py-2">
                                {typeof row[column] === 'boolean'
                                    ? row[column]
                                        ? 'Yes'
                                        : 'No'
                                    : String(row[column] ?? '—')}
                            </td>
                        ))}
                    </tr>
                ))}
            </tbody>
        </table>
    );
}

export default function Labs(props: Props) {
    const tracking = usePage().props.tracking as TrackingProps;

    return (
        <AdminLayout>
            <Head title="A/B Labs" />
            <div className="space-y-6 p-6">
                <header className="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold">A/B Labs</h1>
                        <p className="text-sm text-muted-foreground">
                            Primary metric: {props.primaryMetric}. Minimum{' '}
                            {props.minimumWinnerVisits} visits sebelum
                            menentukan pemenang.
                        </p>
                    </div>
                    <select
                        className="rounded-md border bg-background px-3 py-2"
                        value={props.range}
                        onChange={(event) =>
                            router.get(
                                '/admin/labs',
                                { range: event.target.value },
                                { preserveState: true },
                            )
                        }
                    >
                        {[7, 30, 90].map((days) => (
                            <option key={days} value={days}>
                                {days} hari
                            </option>
                        ))}
                    </select>
                </header>
                <ReportCard title="Performance Matrix">
                    <Table
                        rows={props.performance}
                        eventLabels={
                            tracking.eventLabels as Record<string, string>
                        }
                    />
                </ReportCard>
                <ReportCard title="Split Funnel">
                    <div className="grid gap-6 md:grid-cols-2">
                        {props.funnel.map((item) => (
                            <div key={item.source}>
                                <h3 className="mb-3 font-semibold">
                                    {item.source}
                                </h3>
                                {item.stages.map((stage) => (
                                    <div
                                        key={stage.event}
                                        className="flex justify-between border-b py-1 text-sm"
                                    >
                                        <span>
                                            {tracking.eventLabels[
                                                stage.event as keyof typeof tracking.eventLabels
                                            ] ?? stage.label}
                                        </span>
                                        <span>{stage.value}</span>
                                    </div>
                                ))}
                            </div>
                        ))}
                    </div>
                </ReportCard>
                <ReportCard title="Device Performance">
                    <Table rows={props.devices} />
                </ReportCard>
                <ReportCard title="CTA Performance">
                    <Table rows={props.ctas} />
                </ReportCard>
                <ReportCard title="Personas">
                    <Table
                        rows={props.personas.map((row) => ({
                            source: row.source,
                            ...row.segments,
                        }))}
                    />
                </ReportCard>
                <ReportCard title="Scroll Heatmap">
                    <Table rows={props.scroll_heatmap} />
                </ReportCard>
                {tracking.capabilities.section_view && (
                    <ReportCard title="Section Heatmap">
                        <Table rows={props.section_heatmap} />
                    </ReportCard>
                )}
                <p className="text-xs text-muted-foreground">
                    Dashboard dibatasi {props.retentionDays} hari. Data lebih
                    lama disimpan di arsip.
                </p>
            </div>
        </AdminLayout>
    );
}
