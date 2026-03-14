<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function storeTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'courier_id' => ['nullable', 'integer', 'exists:couriers,id'],
            'channel' => ['nullable', 'string', 'max:30'],
            'priority' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $ticket = SupportTicket::query()->create([
            'ticket_no' => 'TCK'.now()->format('YmdHis').Str::upper(Str::random(4)),
            'order_id' => $validated['order_id'] ?? null,
            'customer_id' => $validated['customer_id'] ?? null,
            'courier_id' => $validated['courier_id'] ?? null,
            'channel' => $validated['channel'] ?? 'web',
            'status' => 'open',
            'priority' => $validated['priority'] ?? 'normal',
            'subject' => $validated['subject'],
            'description' => $validated['description'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ], 201);
    }

    public function addMessage(Request $request, SupportTicket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'author_type' => ['required', 'string', 'max:30'],
            'author_id' => ['nullable', 'integer'],
            'message' => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
            'attachments' => ['nullable', 'array'],
        ]);

        $message = SupportTicketMessage::query()->create([
            'support_ticket_id' => $ticket->id,
            'author_type' => $validated['author_type'],
            'author_id' => $validated['author_id'] ?? null,
            'message' => $validated['message'],
            'is_internal' => (bool) ($validated['is_internal'] ?? false),
            'attachments' => $validated['attachments'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'data' => $message,
        ], 201);
    }
}

