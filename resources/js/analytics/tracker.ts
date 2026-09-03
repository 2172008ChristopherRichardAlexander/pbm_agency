import { EVENT_TYPES, isCriticalEvent, type EventType } from '@/analytics/event-types';
import { AnalyticsQueue } from '@/analytics/queue';
import type { EventData, QueuedEvent } from '@/types/analytics';

const queue = new AnalyticsQueue();
let maximumScrollDepth = 0;

function eventId(): string {
    return window.crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

export function landingSource(): string {
    if (typeof window === 'undefined') return '/';
    const key = 'pbm_landing_source';
    const existing = window.sessionStorage.getItem(key);
    if (existing) return existing;
    window.sessionStorage.setItem(key, window.location.pathname);
    return window.location.pathname;
}

function makePayload(eventType: EventType, data: EventData): QueuedEvent {
    return {
        event_type: eventType,
        event_data: { ...data, event_id: data.event_id ?? eventId(), landing_source: data.landing_source ?? landingSource() },
    };
}

function pushDataLayer(eventType: EventType, data: EventData, payload: QueuedEvent): void {
    window.dataLayer ??= [];
    window.dataLayer.push({
        event: eventType,
        zone: data.zone,
        action: data.action,
        cta_label: data.cta_label,
        landing_source: payload.event_data.landing_source,
        value: data.value,
        currency: data.currency,
    });
}

export function track(eventType: EventType, data: EventData = {}): Promise<void> {
    if (typeof window === 'undefined') return Promise.resolve();

    const payload = makePayload(eventType, data);
    pushDataLayer(eventType, data, payload);

    if (isCriticalEvent(eventType)) queue.sendImmediately(payload);
    else queue.enqueue(payload);

    return Promise.resolve();
}

export async function trackVisit(data: EventData = {}): Promise<boolean> {
    if (typeof window === 'undefined') return false;
    const payload = makePayload(EVENT_TYPES.visit, data);
    pushDataLayer(EVENT_TYPES.visit, data, payload);
    return queue.deliver(payload);
}

export function flushTracking(): void {
    queue.flushBeacon();
}

export function sendHeartbeat(durationSeconds: number, maxScrollDepth: number): void {
    const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
    const body = JSON.stringify({ _token: csrf, duration_seconds: durationSeconds, max_scroll_depth: maxScrollDepth, landing_source: landingSource() });
    const blob = new Blob([body], { type: 'application/json' });
    if (!navigator.sendBeacon('/analytics/heartbeat', blob)) {
        void fetch('/analytics/heartbeat', { method: 'POST', body, keepalive: true, headers: { 'Content-Type': 'application/json' } });
    }
}

export function rememberScrollDepth(depth: number): void {
    maximumScrollDepth = Math.max(maximumScrollDepth, depth);
}

export function getMaximumScrollDepth(): number {
    return maximumScrollDepth;
}
