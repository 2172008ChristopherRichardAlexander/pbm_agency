import type { EventType, ProjectMode } from '@/analytics/event-types';

export type EventData = {
    event_id?: string;
    landing_source?: string;
    zone?: string;
    action?: string;
    cta_label?: string;
    depth?: number;
    section?: string;
    value?: number;
    currency?: string;
    [key: string]: unknown;
};

export type TrackingProps = {
    enabled: boolean;
    mode: ProjectMode;
    visitorId: string;
    eventLabels: Partial<Record<EventType, string>>;
    capabilities: Record<string, boolean>;
    engagementThreshold: number;
    heartbeatInterval: number;
    sectionViewEnabled: boolean;
    metaEvents: Partial<Record<EventType, string>>;
};

export type QueuedEvent = {
    event_type: EventType;
    event_data: EventData;
};
