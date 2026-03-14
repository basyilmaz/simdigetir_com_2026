<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Notifications\Services\NotificationOrchestrator;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function upsertTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_key' => ['required', 'string', 'max:80'],
            'channel' => ['required', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'variables' => ['nullable', 'array'],
        ]);

        $template = NotificationTemplate::query()->updateOrCreate(
            [
                'event_key' => $validated['event_key'],
                'channel' => $validated['channel'],
            ],
            [
                'subject' => $validated['subject'] ?? null,
                'body' => $validated['body'],
                'is_active' => (bool) ($validated['is_active'] ?? true),
                'variables' => $validated['variables'] ?? [],
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $template,
        ], 201);
    }

    public function dispatch(Request $request, NotificationOrchestrator $orchestrator): JsonResponse
    {
        $validated = $request->validate([
            'event_key' => ['required', 'string', 'max:80'],
            'targets' => ['required', 'array', 'min:1'],
            'targets.*.channel' => ['required', 'string', 'max:30'],
            'targets.*.target' => ['required', 'string', 'max:190'],
            'context' => ['nullable', 'array'],
        ]);

        $dispatches = $orchestrator->dispatch(
            eventKey: $validated['event_key'],
            targets: $validated['targets'],
            context: (array) ($validated['context'] ?? [])
        );

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $dispatches->count(),
                'items' => $dispatches->values(),
            ],
        ], 201);
    }
}

