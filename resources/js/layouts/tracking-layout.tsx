import type { PropsWithChildren } from 'react';
import { AnalyticsBootstrap } from '@/hooks/use-analytics';

export default function TrackingLayout({ children }: PropsWithChildren) {
    return (
        <>
            <AnalyticsBootstrap />
            {children}
        </>
    );
}
