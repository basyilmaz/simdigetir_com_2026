<?php

namespace App\Http\Controllers;

use App\Models\LegalDocument;
use Illuminate\View\View;

class LegalDocumentController extends Controller
{
    public function show(string $slug): View
    {
        $document = LegalDocument::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (! $document && $slug === 'kvkk') {
            return view('landing.kvkk');
        }

        abort_if(! $document, 404);

        return view('landing.legal-document', [
            'document' => $document,
        ]);
    }
}

