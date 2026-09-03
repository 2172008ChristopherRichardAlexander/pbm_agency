<?php

namespace App\Jobs;

use App\Services\MetaConversionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendMetaCapiEvent implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public readonly string $eventName,
        public readonly string $eventId,
        public readonly array $data,
        public readonly array $context,
    ) {}

    public function handle(MetaConversionService $meta): void
    {
        if (! $meta->enabled()) {
            return;
        }

        try {
            $response = $meta->send($this->eventName, $this->eventId, $this->data, $this->context);
            $this->log('sent', $response?->status(), $response?->json());
        } catch (Throwable $exception) {
            $this->log('failed', null, null, mb_substr($exception->getMessage(), 0, 4000));

            throw $exception;
        }
    }

    private function log(string $status, ?int $httpStatus, ?array $response, ?string $error = null): void
    {
        if (! config('meta.log_enabled')) {
            return;
        }

        DB::table('meta_capi_logs')->insert([
            'event_id' => $this->eventId,
            'event_name' => $this->eventName,
            'status' => $status,
            'http_status' => $httpStatus,
            'response' => $response ? json_encode($response) : null,
            'error' => $error,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
