<?php

namespace App\Http\Controllers;

use App\Models\FormDefinition;
use App\Models\FormSubmission;
use App\Support\FormDefinitionDefaults;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Leads\Models\Lead;

class FormSubmissionController extends Controller
{
    public function submit(Request $request, string $key): JsonResponse
    {
        $definition = FormDefinition::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        if (! $definition) {
            $defaultDefinitionPayload = FormDefinitionDefaults::byKey($key);

            if (is_array($defaultDefinitionPayload)) {
                $definition = FormDefinition::query()->updateOrCreate(
                    ['key' => $key],
                    $defaultDefinitionPayload
                );
            }
        }

        if (! $definition) {
            return response()->json([
                'success' => false,
                'message' => 'Form tanimi bulunamadi.',
            ], 404);
        }

        $limitKey = 'form-submit:'.$definition->key.':'.$request->ip();
        $rateLimit = max(1, (int) $definition->rate_limit_per_minute);

        if (RateLimiter::tooManyAttempts($limitKey, $rateLimit)) {
            return response()->json([
                'success' => false,
                'message' => 'Cok fazla istek. Lutfen daha sonra tekrar deneyin.',
            ], 429);
        }
        RateLimiter::hit($limitKey, 60);

        $validated = $request->validate($this->rulesFromSchema($definition->schema ?? []));

        $submission = FormSubmission::query()->create([
            'form_definition_id' => $definition->id,
            'payload' => $validated,
            'status' => 'received',
            'request_ip' => $request->ip(),
            'page_url' => $request->input('page_url'),
            'referrer' => $request->input('referrer'),
            'user_agent' => $request->userAgent(),
        ]);

        $companyName = trim((string) ($validated['company_name'] ?? $request->input('company_name', '')));

        if ($definition->target_type === 'lead' && class_exists(Lead::class)) {
            Lead::query()->create([
                'type' => (string) ($validated['type'] ?? 'contact'),
                'name' => (string) ($validated['name'] ?? 'Anonim'),
                'company_name' => $companyName !== '' ? $companyName : null,
                'phone' => $validated['phone'] ?? '-',
                'email' => $validated['email'] ?? null,
                'message' => $validated['message'] ?? null,
                'source' => $validated['utm_source'] ?? null,
                'medium' => $validated['utm_medium'] ?? null,
                'campaign' => $validated['utm_campaign'] ?? null,
                'term' => $validated['utm_term'] ?? null,
                'content' => $validated['utm_content'] ?? null,
                'page_url' => $request->input('page_url'),
                'referrer' => $request->input('referrer'),
                'status' => 'new',
            ]);

            $submission->update(['status' => 'forwarded_to_leads']);
        }

        return response()->json([
            'success' => true,
            'message' => $definition->success_message ?: 'Talebiniz alindi.',
            'data' => [
                'submission_id' => $submission->id,
            ],
        ], 201);
    }

    private function rulesFromSchema(array $schema): array
    {
        $rules = [];
        $fields = is_array($schema['fields'] ?? null) ? $schema['fields'] : [];

        foreach ($fields as $field) {
            if (! is_array($field) || empty($field['name'])) {
                continue;
            }

            $name = (string) $field['name'];
            $type = (string) ($field['type'] ?? 'string');
            $required = (bool) ($field['required'] ?? false);
            $max = (int) ($field['max'] ?? 0);
            $pattern = (string) ($field['pattern'] ?? '');

            $fieldRules = $required ? ['required'] : ['nullable'];
            $fieldRules[] = match ($type) {
                'email' => 'email',
                'numeric' => 'numeric',
                default => 'string',
            };

            if ($max > 0) {
                $fieldRules[] = 'max:'.$max;
            }
            if ($pattern !== '') {
                $fieldRules[] = 'regex:'.$pattern;
            }

            $rules[$name] = $fieldRules;
        }

        // Preserve analytics/context fields used by current frontend.
        $rules['page_url'] = ['nullable', 'string', 'max:500'];
        $rules['referrer'] = ['nullable', 'string', 'max:500'];
        $rules['utm_source'] = ['nullable', 'string', 'max:100'];
        $rules['utm_medium'] = ['nullable', 'string', 'max:100'];
        $rules['utm_campaign'] = ['nullable', 'string', 'max:100'];
        $rules['utm_term'] = ['nullable', 'string', 'max:100'];
        $rules['utm_content'] = ['nullable', 'string', 'max:100'];
        $rules['company_name'] = $rules['company_name'] ?? ['nullable', 'string', 'max:255'];

        return $rules;
    }
}
