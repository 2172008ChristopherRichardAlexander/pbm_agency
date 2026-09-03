<?php

use App\Analytics\EventType;
use App\Models\AnalyticsSession;
use App\Models\UserAnalytic;
use Illuminate\Support\Facades\DB;

test('a valid event is tracked and long external strings are truncated', function () {
    $this->withHeader('User-Agent', str_repeat('x', 2000))
        ->postJson('/analytics/track', [
            'event_type' => EventType::Visit->value,
            'event_data' => [
                'event_id' => 'event-one',
                'landing_source' => '/hero-1',
                'referral_source' => str_repeat('r', 3000),
            ],
        ])->assertCreated()->assertJson(['accepted' => 1, 'duplicates' => 0]);

    $event = UserAnalytic::query()->firstOrFail();
    expect(mb_strlen($event->user_agent))->toBe(1024)
        ->and(mb_strlen($event->referral_source))->toBe(2048)
        ->and($event->landing_source)->toBe('/hero-1');
});

test('event ids are idempotent', function () {
    $payload = [
        'event_type' => EventType::Visit->value,
        'event_data' => ['event_id' => 'same-event'],
    ];

    $this->postJson('/analytics/track', $payload)->assertCreated();
    $this->postJson('/analytics/track', $payload)->assertCreated()->assertJson(['duplicates' => 1]);

    expect(UserAnalytic::query()->count())->toBe(1);
});

test('mode-invalid and server-only events are rejected', function () {
    config()->set('analytics.mode', 'form');

    $this->postJson('/analytics/track', [
        'event_type' => EventType::WhatsappLead->value,
        'event_data' => [],
    ])->assertUnprocessable();

    $this->postJson('/analytics/track', [
        'event_type' => EventType::Payment->value,
        'event_data' => [],
    ])->assertUnprocessable();
});

test('heartbeat updates one session without creating event rows', function () {
    $this->postJson('/analytics/heartbeat', [
        'duration_seconds' => 60,
        'max_scroll_depth' => 75,
        'landing_source' => '/hero-2',
    ])->assertNoContent();

    expect(AnalyticsSession::query()->count())->toBe(1)
        ->and(AnalyticsSession::query()->value('duration_seconds'))->toBe(60)
        ->and(UserAnalytic::query()->count())->toBe(0);
});

test('archive command moves expired telemetry and is idempotent', function () {
    UserAnalytic::query()->create([
        'session_id' => 'expired',
        'event_type' => EventType::Visit,
        'event_data' => [],
        'created_at' => now()->subDays(91),
    ]);

    $this->artisan('analytics:archive')->assertSuccessful();
    $this->artisan('analytics:archive')->assertSuccessful();

    expect(UserAnalytic::query()->count())->toBe(0)
        ->and(DB::table('user_analytics_archive')->count())->toBe(1);
});
