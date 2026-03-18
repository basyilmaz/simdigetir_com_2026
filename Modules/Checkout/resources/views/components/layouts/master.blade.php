<!DOCTYPE html>
<html lang="tr">
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
    @php
        $checkoutPhone = trim((string) \Modules\Settings\Models\Setting::getValue('contact.phone', '+90 551 356 72 92'));
        $checkoutPhoneHref = 'tel:'.(preg_replace('/[^0-9+]/', '', $checkoutPhone) ?: '+905513567292');
        $checkoutWhatsapp = preg_replace('/[^0-9]/', '', (string) \Modules\Settings\Models\Setting::getValue('contact.whatsapp', '905513567292')) ?: '905513567292';
        $checkoutEmail = trim((string) \Modules\Settings\Models\Setting::getValue('contact.email', 'webgetir@simdigetir.com'));
    @endphp
    {{ $slot }}

    <footer class="checkout-site-footer">
        <div class="checkout-site-footer__inner">
            <div class="checkout-site-footer__links" aria-label="Checkout guven baglantilari">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <a href="{{ route('contact') }}">Iletisim</a>
                <a href="{{ url('/kvkk') }}">KVKK</a>
                <a href="{{ url('/kullanim-kosullari') }}">Kullanim Kosullari</a>
            </div>
            <div class="checkout-site-footer__support">
                <a href="{{ $checkoutPhoneHref }}">{{ $checkoutPhone }}</a>
                <a href="https://wa.me/{{ $checkoutWhatsapp }}" target="_blank" rel="noopener">WhatsApp</a>
                <a href="mailto:{{ $checkoutEmail }}">{{ $checkoutEmail }}</a>
            </div>
        </div>
    </footer>

    <style>
        .checkout-site-footer {
            padding: 0 0 24px;
        }

        .checkout-site-footer__inner {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 16px 18px;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            border: 1px solid var(--sg-border-light);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.72);
            box-shadow: 0 12px 34px rgba(63, 42, 20, 0.08);
        }

        .checkout-site-footer__links,
        .checkout-site-footer__support {
            display: flex;
            gap: 12px 16px;
            flex-wrap: wrap;
            align-items: center;
        }

        .checkout-site-footer__inner a {
            color: var(--sg-link-warm);
            text-decoration: none;
            font-size: var(--sg-type-body-sm);
            font-weight: 700;
        }

        .checkout-site-footer__inner a:hover {
            text-decoration: underline;
        }

        @media (max-width: 720px) {
            .checkout-site-footer__inner {
                padding: 14px;
            }

            .checkout-site-footer__links,
            .checkout-site-footer__support {
                width: 100%;
            }
        }
    </style>
    @stack('scripts')
</body>
</html>
