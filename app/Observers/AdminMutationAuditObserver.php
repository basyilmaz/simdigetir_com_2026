<?php

namespace App\Observers;

use App\Models\AdminAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AdminMutationAuditObserver
{
    /**
     * @var array<int, string>
     */
    private array $sensitiveKeys = [
        'password',
        'remember_token',
        'token',
        'api_key',
        'secret',
        'signature',
        'tax_no',
        'invoice_email',
        'phone',
        'pickup_phone',
        'dropoff_phone',
        'email',
    ];

    public function created(Model $model): void
    {
        $this->writeLog($model, 'created', null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->writeLog($model, 'updated', $model->getOriginal(), $model->getAttributes());
    }

    public function deleted(Model $model): void
    {
        $this->writeLog($model, 'deleted', $model->getOriginal(), null);
    }

    private function writeLog(Model $model, string $event, ?array $oldValues, ?array $newValues): void
    {
        if (! Schema::hasTable('admin_audit_logs')) {
            return;
        }

        AdminAuditLog::query()->create([
            'auditable_type' => $model::class,
            'auditable_id' => (string) $model->getKey(),
            'event' => $event,
            'old_values' => $this->sanitizeValues($oldValues),
            'new_values' => $this->sanitizeValues($newValues),
            'changed_by' => auth()->id(),
            'request_ip' => request()?->ip(),
            'request_url' => request()?->fullUrl(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }

    private function sanitizeValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $result = [];
        foreach ($values as $key => $value) {
            $normalizedKey = strtolower((string) $key);
            if (in_array($normalizedKey, $this->sensitiveKeys, true)) {
                $result[$key] = $this->maskValue($value);
                continue;
            }

            if (is_array($value)) {
                $result[$key] = $this->sanitizeValues($value);
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    private function maskValue(mixed $value): string
    {
        if ($value === null) {
            return '***';
        }

        $raw = (string) $value;
        $length = strlen($raw);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return substr($raw, 0, 2).str_repeat('*', max(1, $length - 4)).substr($raw, -2);
    }
}
