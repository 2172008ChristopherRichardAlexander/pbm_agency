import type { ReactNode } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export function MetricCard({ title, value, hint }: { title: string; value: ReactNode; hint?: string }) {
    return (
        <Card>
            <CardHeader className="pb-2"><CardTitle className="text-sm font-medium text-muted-foreground">{title}</CardTitle></CardHeader>
            <CardContent><div className="text-3xl font-bold">{value}</div>{hint && <p className="mt-1 text-xs text-muted-foreground">{hint}</p>}</CardContent>
        </Card>
    );
}
