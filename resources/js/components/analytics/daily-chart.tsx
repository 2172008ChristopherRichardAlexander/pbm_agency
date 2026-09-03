import { EVENT_TYPES } from '@/analytics/event-types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export function DailyChart({
    rows,
    label,
}: {
    rows: Array<Record<string, number | string>>;
    label: string;
}) {
    const maximum = Math.max(
        1,
        ...rows.map((row) => Number(row[EVENT_TYPES.visit] ?? 0)),
    );

    return (
        <Card>
            <CardHeader>
                <CardTitle>Daily {label}</CardTitle>
            </CardHeader>
            <CardContent className="flex h-52 items-end gap-1 overflow-x-auto">
                {rows.map((row) => (
                    <div
                        key={String(row.date)}
                        className="group flex min-w-3 flex-1 flex-col items-center"
                        title={`${row.date}: ${row[EVENT_TYPES.visit] ?? 0}`}
                    >
                        <div
                            className="w-full rounded-t bg-indigo-500"
                            style={{
                                height: `${Math.max(3, (Number(row[EVENT_TYPES.visit] ?? 0) / maximum) * 170)}px`,
                            }}
                        />
                        <span className="sr-only">{row.date}</span>
                    </div>
                ))}
                {rows.length === 0 && (
                    <p className="m-auto text-sm text-muted-foreground">
                        Belum ada data.
                    </p>
                )}
            </CardContent>
        </Card>
    );
}
