<?php

namespace App\Observers;

use App\Models\LegalDocument;
use App\Models\LegalDocumentVersion;

class LegalDocumentObserver
{
    public function creating(LegalDocument $document): void
    {
        if ($document->version < 1) {
            $document->version = 1;
        }

        if ($document->is_published && $document->published_at === null) {
            $document->published_at = now();
        }

        $document->updated_by = auth()->id();
    }

    public function updating(LegalDocument $document): void
    {
        if ($document->isDirty(['content', 'summary', 'is_published'])) {
            $document->version = max(1, (int) $document->getOriginal('version')) + 1;
        }

        if ($document->is_published && $document->published_at === null) {
            $document->published_at = now();
        }

        if (! $document->is_published) {
            $document->published_at = null;
        }

        $document->updated_by = auth()->id();
    }

    public function created(LegalDocument $document): void
    {
        $this->snapshot($document, 'created');
    }

    public function updated(LegalDocument $document): void
    {
        if ($document->wasChanged(['content', 'summary', 'is_published', 'version'])) {
            $this->snapshot($document, 'updated');
        }
    }

    private function snapshot(LegalDocument $document, string $note): void
    {
        LegalDocumentVersion::query()->create([
            'legal_document_id' => $document->id,
            'version' => $document->version,
            'content' => $document->content,
            'summary' => $document->summary,
            'is_published' => $document->is_published,
            'published_at' => $document->published_at,
            'changed_by' => auth()->id(),
            'note' => $note,
            'created_at' => now(),
        ]);
    }
}

