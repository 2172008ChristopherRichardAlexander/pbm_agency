<?php

namespace App\Services;

use App\Analytics\EventType;
use App\Models\AnalyticsSession;
use App\Models\UserAnalytic;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class AnalyticsMetricsService
{
    public function dashboard(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $mode = (string) config('analytics.mode');
        $counts = [];
        foreach (EventType::forMode($mode) as $event) {
            $counts[$event->value] = $this->eventQuery($from, $to, $event)->distinct('session_id')->count('session_id');
        }

        $leadSessions = $this->leadSessionIds($from, $to);
        $visits = $counts[EventType::Visit->value] ?? 0;
        $payments = $counts[EventType::Payment->value] ?? 0;
        $revenue = (int) $this->eventQuery($from, $to, EventType::Payment)->sum('payment_amount');
        $bounces = AnalyticsSession::query()->whereBetween('started_at', [$from, $to])->where('is_bounce', true)->count();

        return [
            'stats' => [
                'visits' => $visits,
                'engagements' => $counts[EventType::Engagement->value] ?? 0,
                'bounces' => $bounces,
                'bounce_rate' => $this->rate($bounces, $visits),
                'total_leads' => count($leadSessions),
                'lead_cr' => $this->rate(count($leadSessions), $visits),
                'payments' => $payments,
                'sales_cr' => $this->rate($payments, $visits),
                'revenue' => $revenue,
                'rpv' => $visits > 0 ? round($revenue / $visits) : 0,
            ],
            'daily' => $this->daily($from, $to),
            'referrals' => UserAnalytic::query()
                ->where('event_type', EventType::Visit)
                ->whereBetween('created_at', [$from, $to])
                ->selectRaw("COALESCE(referral_source, 'Direct') AS source, COUNT(DISTINCT session_id) AS visits")
                ->groupBy('referral_source')->orderByDesc('visits')->limit(20)->get(),
            'funnel' => $this->hierarchicalFunnel($from, $to),
        ];
    }

    /** @return list<string> */
    public function leadSessionIds(CarbonImmutable $from, CarbonImmutable $to, ?string $landingSource = null): array
    {
        $query = UserAnalytic::query()->whereBetween('created_at', [$from, $to]);
        if ($landingSource !== null) {
            $query->where('landing_source', $landingSource);
        }

        if (config('analytics.mode') === 'ctwa') {
            $query->whereIn('event_type', [EventType::WhatsappLead, EventType::DirectCheckout]);
        } else {
            $query->where('event_type', EventType::Lead);
        }

        return $query->distinct()->pluck('session_id')->all();
    }

    private function eventQuery(CarbonImmutable $from, CarbonImmutable $to, EventType $event): Builder
    {
        return UserAnalytic::query()->where('event_type', $event)->whereBetween('created_at', [$from, $to]);
    }

    private function daily(CarbonImmutable $from, CarbonImmutable $to): array
    {
        return UserAnalytic::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('event_type', [EventType::Visit, EventType::Engagement, EventType::WhatsappLead, EventType::DirectCheckout, EventType::Lead, EventType::Payment])
            ->selectRaw('DATE(created_at) AS date, event_type, COUNT(DISTINCT session_id) AS total')
            ->groupByRaw('DATE(created_at), event_type')
            ->orderBy('date')
            ->get()->groupBy('date')->map(function ($rows, $date) {
                $item = ['date' => $date];
                foreach ($rows as $row) {
                    $item[$row->event_type->value] = (int) $row->total;
                }

                return $item;
            })->values()->all();
    }

    private function hierarchicalFunnel(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $events = config('analytics.mode') === 'ctwa'
            ? [EventType::Visit, EventType::Engagement, EventType::Intent, EventType::DirectCheckout, EventType::WhatsappLead]
            : [EventType::Visit, EventType::Engagement, EventType::Intent, EventType::FormStart, EventType::Lead, EventType::Payment];

        $eligible = null;
        $result = [];
        foreach ($events as $event) {
            $ids = $this->eventQuery($from, $to, $event)->distinct()->pluck('session_id');
            $eligible = $eligible === null ? $ids : $eligible->intersect($ids);
            $result[] = ['event' => $event->value, 'label' => $event->label(), 'value' => $eligible->count()];
        }

        return $result;
    }

    private function rate(int $value, int $base): float
    {
        return $base > 0 ? round(($value / $base) * 100, 2) : 0;
    }
}
