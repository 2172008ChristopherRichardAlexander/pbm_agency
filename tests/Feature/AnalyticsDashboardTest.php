<?php

use App\Analytics\EventType;
use App\Models\AnalyticsSession;
use App\Models\User;
use App\Models\UserAnalytic;
use App\Services\AbTestingService;
use App\Services\AnalyticsMetricsService;
use Carbon\CarbonImmutable;
use Database\Seeders\AnalyticsDemoSeeder;
use Inertia\Testing\AssertableInertia as Assert;

function dashboardEvent(string $session, EventType $type, array $data = []): void
{
    UserAnalytic::query()->create([
        'session_id' => $session,
        'event_type' => $type,
        'event_data' => $data,
        'landing_source' => '/test',
        'cta_zone' => $data['zone'] ?? null,
        'cta_action' => $data['action'] ?? null,
        'payment_amount' => $data['amount'] ?? null,
        'created_at' => now(),
    ]);
}

test('ctwa total lead counts a session once across both lead actions', function () {
    config()->set('analytics.mode', 'ctwa');
    dashboardEvent('one', EventType::Visit);
    dashboardEvent('one', EventType::WhatsappLead);
    dashboardEvent('one', EventType::DirectCheckout);

    $report = app(AnalyticsMetricsService::class)->dashboard(CarbonImmutable::now()->startOfDay(), CarbonImmutable::now()->endOfDay());
    expect($report['stats']['total_leads'])->toBe(1);
});

test('split funnel is hierarchical', function () {
    config()->set('analytics.mode', 'ctwa');
    foreach (['one', 'two'] as $session) dashboardEvent($session, EventType::Visit);
    dashboardEvent('one', EventType::Engagement);
    dashboardEvent('two', EventType::Intent);
    dashboardEvent('two', EventType::WhatsappLead);

    $report = app(AbTestingService::class)->report(CarbonImmutable::now()->startOfDay(), CarbonImmutable::now()->endOfDay());
    $values = collect($report['funnel'][0]['stages'])->pluck('value');
    expect($values->zip($values->slice(1))->every(fn ($pair) => $pair[1] === null || $pair[1] <= $pair[0]))->toBeTrue();
});

test('admin dashboards render seeded data and clamp the date range', function () {
    $this->withoutVite();
    config()->set('analytics.mode', 'ctwa');
    $this->seed(AnalyticsDemoSeeder::class);
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin?range=365')->assertInertia(fn (Assert $page) => $page
        ->component('admin/analytics')->where('range', 30)->has('daily')->has('funnel'));

    $this->actingAs($admin)->get('/admin/labs')->assertInertia(fn (Assert $page) => $page
        ->component('admin/labs/index')->has('performance', 2)->has('section_heatmap'));
});
