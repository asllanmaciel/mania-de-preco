<?php

namespace App\Http\Controllers\Web\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Billing\Asaas\AsaasWebhookProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsaasWebhookController extends Controller
{
    public function __invoke(Request $request, AsaasWebhookProcessor $processor): JsonResponse
    {
        $event = $processor->process(
            $request->all(),
            $request->header('asaas-access-token')
        );

        return response()->json([
            'ok' => true,
            'event_id' => $event->event_id,
            'status' => $event->status,
        ]);
    }
}
