<?php

namespace App\Http\Controllers\NotificationHelper;

#use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\CentreonModel\MetricsService;
use Illuminate\Http\Request;

class MetricsController extends Controller {

    public function hasMetrics ($hostServiceId) {
        $svc = new MetricsService();
        $hasMetrics = $svc->hasServiceMetrics($hostServiceId);
        if ($hasMetrics)
            return response()->make('true');
        else
            return response()->make('false');
    }
}