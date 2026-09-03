<?php

namespace App\Http\Controllers;

use App\Analytics\TrackingService;
use App\Http\Requests\TrackEventRequest;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function track(TrackEventRequest $request, TrackingService $tracking): JsonResponse
    {
        $accepted = 0;
        $duplicates = 0;

        foreach ($request->events() as $payload) {
            $stored = $tracking->track($request, $payload['event_type'], $payload['event_data']);
            $stored ? $accepted++ : $duplicates++;
        }

        return response()->json(compact('accepted', 'duplicates'), 201);
    }
}
