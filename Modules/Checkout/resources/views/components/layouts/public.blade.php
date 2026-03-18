@props([
    'title' => '',
    'description' => '',
    'keywords' => '',
    'robots' => 'index, follow',
    'canonical' => null,
    'ogImage' => null,
])

@php
    $resolvedTitle = trim((string) $title);
    $resolvedDescription = trim((string) $description);
    $resolvedKeywords = trim((string) $keywords);
    $resolvedRobots = trim((string) $robots);
    $resolvedCanonical = trim((string) ($canonical ?: url()->current()));
    $resolvedOgImage = trim((string) ($ogImage ?: asset('images/og-banner.png')));
    $defaultDescription = 'SimdiGetir checkout, hesap ve siparis takip deneyimi.';
    $pageTitle = $resolvedTitle !== '' ? $resolvedTitle.' | SimdiGetir' : 'SimdiGetir';
    $pageDescription = $resolvedDescription !== '' ? $resolvedDescription : $defaultDescription;
@endphp

@extends('layouts.landing')

@section('title', $pageTitle)
@section('meta_description', $pageDescription)
@section('canonical_url', $resolvedCanonical)
@section('og_title', $pageTitle)
@section('og_description', $pageDescription)
@section('og_image', $resolvedOgImage)
@if ($resolvedKeywords !== '')
@section('meta_keywords', $resolvedKeywords)
@endif
@if ($resolvedRobots !== '')
@section('robots', $resolvedRobots)
@endif

@section('content')
<div class="checkout-public-page">
    {{ $slot }}
</div>
@endsection

@push('styles')
<style>
    .checkout-public-page {
        padding: 9.75rem 0 4.5rem;
        background:
            radial-gradient(circle at top left, rgba(124, 58, 237, 0.12), transparent 32%),
            radial-gradient(circle at bottom right, rgba(34, 211, 238, 0.1), transparent 34%),
            var(--sg-surface-page-light);
        color: var(--sg-ink-light);
    }

    [data-theme="dark"] .checkout-public-page {
        background:
            radial-gradient(circle at top left, rgba(124, 58, 237, 0.18), transparent 34%),
            radial-gradient(circle at bottom right, rgba(34, 211, 238, 0.12), transparent 36%),
            var(--sg-surface-page-dark);
        color: var(--sg-ink-dark-soft);
    }

    .checkout-shell {
        width: min(1160px, calc(100% - 32px));
        margin: 0 auto;
        display: grid;
        gap: 20px;
    }

    .checkout-shell--wide {
        width: min(1240px, calc(100% - 32px));
    }

    .checkout-hero-grid,
    .checkout-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
        gap: 20px;
        align-items: stretch;
    }

    .checkout-grid--equal {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .checkout-card {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 24px;
        box-shadow: 0 24px 60px rgba(12, 18, 40, 0.16);
        backdrop-filter: blur(18px);
    }

    [data-theme="light"] .checkout-card {
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
    }

    .checkout-card--hero,
    .checkout-card--panel,
    .checkout-card--support {
        padding: 24px;
    }

    .checkout-card--hero {
        padding: 30px;
    }

    .checkout-lead {
        display: grid;
        gap: 14px;
    }

    .checkout-lead h1,
    .checkout-card h2,
    .checkout-card h3 {
        margin: 0;
        font-family: var(--sg-font-display);
        letter-spacing: -0.03em;
    }

    .checkout-lead h1 {
        font-size: clamp(2.2rem, 4vw, 3.4rem);
        line-height: 1.02;
    }

    .checkout-card h2 {
        font-size: clamp(1.5rem, 2vw, 1.95rem);
        line-height: 1.1;
    }

    .checkout-card p,
    .checkout-card li,
    .checkout-meta,
    .checkout-muted,
    .checkout-note,
    .checkout-field small,
    .checkout-empty,
    .checkout-card small {
        color: var(--text-secondary);
    }

    .checkout-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .checkout-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 800;
        background: rgba(124, 58, 237, 0.12);
        border: 1px solid rgba(124, 58, 237, 0.18);
        color: var(--accent);
    }

    .checkout-chip--info {
        background: rgba(34, 211, 238, 0.12);
        border-color: rgba(34, 211, 238, 0.18);
        color: #22d3ee;
    }

    .checkout-form-grid {
        display: grid;
        gap: 14px;
    }

    .checkout-form-grid--inline {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        align-items: end;
    }

    .checkout-field {
        display: grid;
        gap: 8px;
    }

    .checkout-field label {
        font-size: 0.84rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .checkout-field input,
    .checkout-field textarea,
    .checkout-field select {
        width: 100%;
        min-height: 54px;
        padding: 14px 16px;
        border-radius: 16px;
        border: 1px solid var(--border-glass);
        background: rgba(255, 255, 255, 0.06);
        color: var(--text-primary);
        font: inherit;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    [data-theme="light"] .checkout-field input,
    [data-theme="light"] .checkout-field textarea,
    [data-theme="light"] .checkout-field select {
        background: rgba(255, 255, 255, 0.92);
        border-color: rgba(15, 23, 42, 0.1);
        color: var(--sg-ink-light);
    }

    .checkout-field input:focus,
    .checkout-field textarea:focus,
    .checkout-field select:focus {
        outline: none;
        border-color: rgba(124, 58, 237, 0.55);
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.12);
    }

    .checkout-field textarea {
        min-height: 120px;
        resize: vertical;
    }

    .checkout-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .checkout-actions .btn {
        min-width: 180px;
        justify-content: center;
    }

    .checkout-alert {
        padding: 14px 16px;
        border-radius: 18px;
        border: 1px solid transparent;
        font-size: 0.96rem;
        line-height: 1.6;
    }

    .checkout-alert--info {
        background: rgba(34, 211, 238, 0.12);
        border-color: rgba(34, 211, 238, 0.18);
        color: #d5f7fb;
    }

    .checkout-alert--error {
        background: rgba(248, 113, 113, 0.12);
        border-color: rgba(248, 113, 113, 0.22);
        color: #ffe3e3;
    }

    .checkout-alert--success {
        background: rgba(16, 185, 129, 0.12);
        border-color: rgba(16, 185, 129, 0.2);
        color: #d4ffe9;
    }

    [data-theme="light"] .checkout-alert--info {
        color: #155e75;
    }

    [data-theme="light"] .checkout-alert--error {
        color: #991b1b;
    }

    [data-theme="light"] .checkout-alert--success {
        color: #166534;
    }

    .checkout-list {
        display: grid;
        gap: 12px;
    }

    .checkout-list-item {
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    [data-theme="light"] .checkout-list-item {
        background: rgba(255, 255, 255, 0.78);
        border-color: rgba(15, 23, 42, 0.06);
    }

    .checkout-list-item strong {
        display: block;
        margin-bottom: 6px;
        font-size: 1rem;
    }

    .checkout-link-list {
        display: grid;
        gap: 10px;
    }

    .checkout-link-list a {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 700;
    }

    .checkout-link-list a:hover {
        color: var(--accent);
    }

    .checkout-summary-list {
        display: grid;
        gap: 12px;
    }

    .checkout-panel-head,
    .tracking-card-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .checkout-panel-head p,
    .tracking-card-header p {
        margin: 6px 0 0;
    }

    .checkout-summary-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
    }

    .checkout-summary-row strong {
        text-align: right;
    }

    .checkout-checkbox {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    [data-theme="light"] .checkout-checkbox {
        background: rgba(255, 255, 255, 0.78);
        border-color: rgba(15, 23, 42, 0.06);
    }

    .checkout-checkbox input {
        margin-top: 4px;
    }

    .checkout-checkbox a {
        color: var(--accent);
        font-weight: 800;
    }

    @media (max-width: 1080px) {
        .checkout-public-page {
            padding-top: 8.75rem;
        }

        .checkout-hero-grid,
        .checkout-grid,
        .checkout-grid--equal,
        .checkout-form-grid--inline {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .checkout-public-page {
            padding-top: 7.8rem;
            padding-bottom: 3.5rem;
        }

        .checkout-shell,
        .checkout-shell--wide {
            width: min(100% - 24px, 1160px);
        }

        .checkout-card--hero,
        .checkout-card--panel,
        .checkout-card--support {
            padding: 20px;
        }

        .checkout-actions .btn {
            width: 100%;
            min-width: 0;
        }
    }
</style>
@endpush
