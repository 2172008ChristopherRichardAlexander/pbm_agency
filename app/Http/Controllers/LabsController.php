<?php

namespace App\Http\Controllers;

use App\Services\AbTestingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LabsController extends Controller
{
    public function index(Request $request, AbTestingService $labs): Response
    {
        $days = (int) $request->integer('range', 30);
        if (! in_array($days, [7, 30, 90], true)) {
            $days = 30;
        }
        $to = CarbonImmutable::now()->endOfDay();
        $from = $to->subDays($days - 1)->startOfDay();

        return Inertia::render('admin/labs/index', [
            ...$labs->report($from, $to),
            'range' => $days,
            'minimumWinnerVisits' => config('analytics.minimum_winner_visits'),
            'primaryMetric' => config('analytics.primary_metric'),
            'retentionDays' => config('analytics.retention_days'),
        ]);
    }
}
