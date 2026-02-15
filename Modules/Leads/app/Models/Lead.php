<?php

namespace Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'type',
        'name',
        'company_name',
        'phone',
        'email',
        'message',
        'source',
        'medium',
        'campaign',
        'term',
        'content',
        'page_url',
        'referrer',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if lead is new
     */
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    /**
     * Mark as contacted
     */
    public function markAsContacted(?string $notes = null): void
    {
        $this->update([
            'status' => 'contacted',
            'notes' => $notes ? ($this->notes . "\n" . $notes) : $this->notes,
        ]);
    }
}
