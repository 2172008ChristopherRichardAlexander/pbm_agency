import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Stage = { event: string; label: string; value: number };

export function ConversionFunnel({ stages }: { stages: Stage[] }) {
    const base = Math.max(1, stages[0]?.value ?? 1);

    return (
        <Card>
            <CardHeader>
                <CardTitle>Conversion Funnel</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
                {stages.map((stage) => (
                    <div key={stage.event}>
                        <div className="mb-1 flex justify-between text-sm">
                            <span>{stage.label}</span>
                            <span>{stage.value.toLocaleString()}</span>
                        </div>
                        <div className="h-2 rounded-full bg-muted">
                            <div
                                className="h-2 rounded-full bg-cyan-500"
                                style={{
                                    width: `${Math.min(100, (stage.value / base) * 100)}%`,
                                }}
                            />
                        </div>
                    </div>
                ))}
            </CardContent>
        </Card>
    );
}
