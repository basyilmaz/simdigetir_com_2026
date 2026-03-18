<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>{{ $title ?? 'Checkout' }} | {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="author" content="{{ $author ?? '' }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|space-grotesk:500,700&display=swap" rel="stylesheet" />

    @include('components.design-tokens')
    <style id="sg-typography-contract">
        body {
            font-family: var(--sg-font-body);
            line-height: var(--sg-leading-body);
        }
        h1, h2, h3, h4 {
            font-family: var(--sg-font-display);
            line-height: var(--sg-leading-heading);
        }
        small,
        label,
        .caption {
            font-size: var(--sg-type-caption);
            line-height: var(--sg-leading-caption);
        }
    </style>
    @stack('styles')
</head>
<body>
    {{ $slot }}

    @stack('scripts')
</body>
</html>
