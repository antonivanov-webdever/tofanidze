<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnalyticsEventRequest;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Response;

class EventController extends Controller
{
    /**
     * First-party page-view beacon. No cookies, no fingerprinting, no
     * third-party script — just a path and an optional referrer, fired once
     * per page load via navigator.sendBeacon (see resources/js/app.js).
     */
    public function store(StoreAnalyticsEventRequest $request): Response
    {
        AnalyticsEvent::create($request->validated());

        return response()->noContent();
    }
}
