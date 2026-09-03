<?php

namespace App\Services;

use App\Analytics\EventType;
use App\Models\AnalyticsSession;
use App\Models\UserAnalytic;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class AbTestingService
{
    public function report(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $events = UserAnalytic::query()->whereBetween('created_at', [$from, $to])->get();
        $sessions = AnalyticsSession::query()->whereBetween('started_at', [$from, $to])->get();
        $sources = $events->pluck('landing_source')->merge($sessions->pluck('landing_source'))->filter()->unique()->sort()->values();

        return [
            'sources' => $sources,
            'performance' => $this->performance($sources, $events),
            'funnel' => $this->funnels($sources, $events),
            'devices' => $this->devices($sessions, $events),
            'ctas' => $this->ctas($events),
            'personas' => $this->personas($sessions),
            'scroll_heatmap' => $this->scrollHeatmap($events),
            'section_heatmap' => $this->sectionHeatmap($events),
        ];
    }

    private function performance(Collection $sources, Collection $events): array
    {
        return $sources->map(function (string $source) use ($events) {
            $rows = $events->where('landing_source', $source);
            $count = fn (EventType $type) => $rows->where('event_type', $type)->pluck('session_id')->unique()->count();
            $visits = $count(EventType::Visit);
            $leadIds = config('analytics.mode') === 'ctwa'
                ? $rows->whereIn('event_type', [EventType::WhatsappLead, EventType::DirectCheckout])->pluck('session_id')->unique()
                : $rows->where('event_type', EventType::Lead)->pluck('session_id')->unique();
            $revenue = (int) $rows->where('event_type', EventType::Payment)->sum('payment_amount');

            $base = [
                'source' => $source,
                EventType::Visit->value => $visits,
                EventType::Engagement->value => $count(EventType::Engagement),
                EventType::Intent->value => $count(EventType::Intent),
            ];
            $modeMetrics = config('analytics.mode') === 'ctwa' ? [
                EventType::DirectCheckout->value => $count(EventType::DirectCheckout),
                EventType::WhatsappLead->value => $count(EventType::WhatsappLead),
                'total_lead' => $leadIds->count(),
            ] : [
                EventType::FormStart->value => $count(EventType::FormStart),
                EventType::Lead->value => $count(EventType::Lead),
            ];
            $financial = config('analytics.capabilities.payment') ? [
                EventType::Payment->value => $count(EventType::Payment),
                'revenue' => $revenue,
                'rpv' => $visits ? round($revenue / $visits) : 0,
            ] : [];

            return [...$base, ...$modeMetrics, ...$financial,
                'lead_cr' => $visits ? round($leadIds->count() / $visits * 100, 2) : 0,
                'eligible' => $visits >= (int) config('analytics.minimum_winner_visits'),
            ];
        })->values()->all();
    }

    private function funnels(Collection $sources, Collection $events): array
    {
        $stages = config('analytics.mode') === 'ctwa'
            ? [EventType::Visit, EventType::Engagement, EventType::Intent]
            : [EventType::Visit, EventType::Engagement, EventType::Intent, EventType::FormStart, EventType::Lead, EventType::Payment];

        return $sources->map(function (string $source) use ($events, $stages) {
            $rows = $events->where('landing_source', $source);
            $eligible = null;
            $values = [];
            foreach ($stages as $stage) {
                $ids = $rows->where('event_type', $stage)->pluck('session_id')->unique();
                $eligible = $eligible === null ? $ids : $eligible->intersect($ids);
                $values[] = ['event' => $stage->value, 'label' => $stage->label(), 'value' => $eligible->count()];
            }

            if (config('analytics.mode') === 'ctwa') {
                foreach ([EventType::DirectCheckout, EventType::WhatsappLead] as $branch) {
                    $ids = $rows->where('event_type', $branch)->pluck('session_id')->unique();
                    $values[] = ['event' => $branch->value, 'label' => $branch->label(), 'value' => $eligible->intersect($ids)->count()];
                }
            }

            return ['source' => $source, 'stages' => $values];
        })->values()->all();
    }

    private function devices(Collection $sessions, Collection $events): array
    {
        $leadIds = config('analytics.mode') === 'ctwa'
            ? $events->whereIn('event_type', [EventType::WhatsappLead, EventType::DirectCheckout])->pluck('session_id')->unique()
            : $events->where('event_type', EventType::Lead)->pluck('session_id')->unique();

        return $sessions->groupBy(fn ($session) => ($session->landing_source ?: '/').'|'.($session->device_type ?: 'unknown'))
            ->map(function ($rows) use ($leadIds) {
                $first = $rows->first();

                return [
                    'source' => $first->landing_source ?: '/',
                    'device' => $first->device_type ?: 'unknown',
                    'visits' => $rows->count(),
                    'leads' => $rows->pluck('session_id')->intersect($leadIds)->count(),
                ];
            })->values()->all();
    }

    private function ctas(Collection $events): array
    {
        return $events->whereNotNull('cta_zone')->groupBy(fn ($row) => $row->landing_source.'|'.$row->cta_zone.'|'.$row->cta_action)
            ->map(function ($rows) {
                $first = $rows->first();

                return ['source' => $first->landing_source, 'zone' => $first->cta_zone, 'action' => $first->cta_action, 'clicks' => $rows->count(), 'sessions' => $rows->pluck('session_id')->unique()->count()];
            })->values()->all();
    }

    private function personas(Collection $sessions): array
    {
        return $sessions->groupBy('landing_source')->map(function ($rows, $source) {
            $personas = ['Skimmer' => 0, 'Engaged Reader' => 0, 'Deep Reader' => 0];
            foreach ($rows as $row) {
                $persona = $row->max_scroll_depth >= 75 ? 'Deep Reader' : (($row->duration_seconds >= 30 || $row->max_scroll_depth >= 50) ? 'Engaged Reader' : 'Skimmer');
                $personas[$persona]++;
            }

            return ['source' => $source ?: '/', 'segments' => $personas];
        })->values()->all();
    }

    private function scrollHeatmap(Collection $events): array
    {
        return $events->where('event_type', EventType::Scroll)->groupBy(fn ($row) => $row->landing_source.'|'.$row->scroll_depth)
            ->map(function ($rows) {
                $first = $rows->first();

                return ['source' => $first->landing_source, 'depth' => $first->scroll_depth, 'sessions' => $rows->pluck('session_id')->unique()->count()];
            })->values()->all();
    }

    private function sectionHeatmap(Collection $events): array
    {
        return $events->where('event_type', EventType::SectionView)->groupBy(fn ($row) => $row->landing_source.'|'.$row->section_id)
            ->map(function ($rows) {
                $first = $rows->sortBy('created_at')->first();

                return ['source' => $first->landing_source, 'section' => $first->section_id, 'sessions' => $rows->pluck('session_id')->unique()->count(), 'first_seen' => $first->created_at?->toIso8601String()];
            })
            ->sortBy('first_seen')->values()->all();
    }
}
