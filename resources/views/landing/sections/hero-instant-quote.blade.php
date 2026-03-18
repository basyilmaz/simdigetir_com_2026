@php
    $quoteWidgetEnabled = (bool) config('landing.quote_widget_enabled', true)
        && (bool) ($landingContent['quote_widget_enabled'] ?? true);
    $requestTimeoutSeconds = (float) config('landing.quote_widget.request_timeout_seconds', 8.2);
    $whatsappHref = (string) ($landingContent['main_cta_secondary_href'] ?? 'https://wa.me/905513567292');
    $callHref = (string) ($landingContent['main_cta_phone_href'] ?? 'tel:+905513567292');
    $quoteWidgetTitle = (string) ($landingContent['quote_widget_title_text'] ?? 'Aninda Fiyat Hesapla');
    $quoteWidgetSubtitle = (string) ($landingContent['quote_widget_subtitle_text'] ?? 'Alinis ve teslimat adresini girin, tahmini fiyat ve sureyi aninda gorun.');
    $pickupLabel = (string) ($landingContent['quote_widget_pickup_label_text'] ?? 'Alinis Adresi');
    $pickupPlaceholder = (string) ($landingContent['quote_widget_pickup_placeholder_text'] ?? 'Orn: Sisli Mecidiyekoy');
    $dropoffLabel = (string) ($landingContent['quote_widget_dropoff_label_text'] ?? 'Teslimat Adresi');
    $dropoffPlaceholder = (string) ($landingContent['quote_widget_dropoff_placeholder_text'] ?? 'Orn: Kadikoy Moda');
    $serviceLabel = (string) ($landingContent['quote_widget_service_label_text'] ?? 'Hizmet Tipi');
    $submitLabel = (string) ($landingContent['quote_widget_submit_label_text'] ?? 'Fiyat Hesapla');
    $whatsappLabel = (string) ($landingContent['quote_widget_whatsapp_label_text'] ?? 'WhatsApp ile Devam Et');
    $callLabel = (string) ($landingContent['quote_widget_call_label_text'] ?? 'Beni Arayin');
    $googleMapsApiKey = trim((string) config('services_integrations.maps.google_maps_api_key', ''));
    $autocompleteProvider = $googleMapsApiKey !== '' ? 'google_places' : 'manual';
    $autocompleteCountry = strtolower((string) ($landingContent['quote_widget_autocomplete_country'] ?? 'tr'));

    $serviceOptions = collect((array) ($landingContent['quote_widget_service_options'] ?? []))
        ->map(function ($option) {
            if (! is_array($option)) {
                return null;
            }

            $value = trim((string) ($option['value'] ?? ''));
            $label = trim((string) ($option['label'] ?? ''));

            if ($value === '' || $label === '') {
                return null;
            }

            return [
                'value' => $value,
                'label' => $label,
                'base_amount' => max(0, (int) ($option['base_amount'] ?? 0)),
                'fallback_minutes' => max(1, (int) ($option['fallback_minutes'] ?? 1)),
            ];
        })
        ->filter()
        ->values()
        ->all();

    if ($serviceOptions === []) {
        $serviceOptions = [
            [
                'value' => 'moto',
                'label' => 'Moto Kurye',
                'base_amount' => 25000,
                'fallback_minutes' => 45,
            ],
        ];
    }

    $baseAmounts = [];
    $fallbackMinutes = [];
    foreach ($serviceOptions as $option) {
        $baseAmounts[$option['value']] = (int) $option['base_amount'];
        $fallbackMinutes[$option['value']] = (int) $option['fallback_minutes'];
    }
@endphp

@if($quoteWidgetEnabled)
    <section
        class="hero-quote-widget glass"
        data-quote-widget
        data-quote-autocomplete-provider="{{ $autocompleteProvider }}"
        data-quote-autocomplete-country="{{ $autocompleteCountry }}"
        @if($googleMapsApiKey !== '') data-google-maps-api-key="{{ $googleMapsApiKey }}" @endif
    >
        <h3>{{ $quoteWidgetTitle }}</h3>
        <p>{{ $quoteWidgetSubtitle }}</p>

        <form class="hero-quote-form" data-quote-form novalidate>
            <div class="hero-quote-field">
                <label for="quote-pickup-address">{{ $pickupLabel }}</label>
                <input
                    id="quote-pickup-address"
                    name="pickup_address"
                    type="text"
                    minlength="5"
                    autocomplete="street-address"
                    required
                    placeholder="{{ $pickupPlaceholder }}"
                >
                <input type="hidden" name="pickup_lat" data-quote-pickup-lat>
                <input type="hidden" name="pickup_lng" data-quote-pickup-lng>
                <p class="hero-quote-input-hint" data-field-error="pickup_address"></p>
            </div>

            <div class="hero-quote-field">
                <label for="quote-dropoff-address">{{ $dropoffLabel }}</label>
                <input
                    id="quote-dropoff-address"
                    name="dropoff_address"
                    type="text"
                    minlength="5"
                    autocomplete="street-address"
                    required
                    placeholder="{{ $dropoffPlaceholder }}"
                >
                <input type="hidden" name="dropoff_lat" data-quote-dropoff-lat>
                <input type="hidden" name="dropoff_lng" data-quote-dropoff-lng>
                <p class="hero-quote-input-hint" data-field-error="dropoff_address"></p>
            </div>

            <div class="hero-quote-field">
                <label for="quote-service-type">{{ $serviceLabel }}</label>
                <select id="quote-service-type" name="service_type">
                    @foreach($serviceOptions as $option)
                        <option value="{{ $option['value'] ?? 'moto' }}">{{ $option['label'] ?? 'Moto Kurye' }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary hero-quote-submit" data-quote-submit data-default-label="{{ $submitLabel }}">
                <i class="fa-solid fa-calculator"></i> {{ $submitLabel }}
            </button>

            <a href="{{ route('checkout.index') }}" class="btn btn-outline hero-quote-direct" data-quote-start-checkout-direct hidden>
                <i class="fa-solid fa-cart-shopping"></i> Teklifsiz Devam Et
            </a>
        </form>

        <p class="hero-quote-feedback" data-quote-feedback hidden></p>

        <div class="hero-quote-result" data-quote-result hidden aria-live="polite">
            <div class="hero-quote-summary">
                <strong data-quote-price-range>-</strong>
                <span data-quote-eta>-</span>
            </div>
            <p class="hero-quote-detail" data-quote-distance></p>
            <p class="hero-quote-fallback" data-quote-fallback hidden></p>
            <div class="hero-quote-ctas">
                <button type="button" class="btn btn-primary" data-quote-start-checkout data-default-label="Siparise Gec" hidden>
                    <i class="fa-solid fa-arrow-right"></i> Siparişe Geç
                </button>
                <a href="{{ route('checkout.index') }}" class="btn btn-outline" data-quote-start-checkout-fallback>
                    <i class="fa-solid fa-cart-shopping"></i> Devam Et
                </a>
                <a href="{{ $whatsappHref }}" class="btn btn-primary" data-quote-cta="whatsapp" target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp"></i> {{ $whatsappLabel }}
                </a>
                <a href="{{ $callHref }}" class="btn btn-outline" data-quote-cta="call">
                    <i class="fa-solid fa-phone"></i> {{ $callLabel }}
                </a>
            </div>
        </div>
    </section>

    @push('styles')
    <style>
        .hero-quote-widget {
            margin-top: 1.5rem;
            padding: 1.25rem;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(15, 23, 42, 0.62);
            backdrop-filter: blur(12px);
            max-width: 540px;
        }

        .hero-quote-widget h3 {
            margin: 0 0 0.4rem;
            font-size: 1.1rem;
        }

        .hero-quote-widget > p {
            margin: 0 0 1rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .hero-quote-form {
            display: grid;
            gap: 0.75rem;
        }

        .hero-quote-field {
            display: grid;
            gap: 0.35rem;
        }

        .hero-quote-field label {
            font-size: 0.83rem;
            color: var(--text-secondary);
        }

        .hero-quote-field input,
        .hero-quote-field select {
            min-height: 44px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(15, 23, 42, 0.35);
            color: var(--text-primary);
            padding: 0.65rem 0.8rem;
        }

        .hero-quote-input-hint {
            margin: 0;
            min-height: 16px;
            font-size: 0.8rem;
            color: #fda4af;
        }

        .hero-quote-submit {
            min-height: 44px;
            justify-content: center;
        }

        .hero-quote-direct {
            min-height: 44px;
            justify-content: center;
            text-align: center;
        }

        .hero-quote-feedback {
            margin: 0.85rem 0 0;
            font-size: 0.86rem;
            padding: 0.55rem 0.65rem;
            border-radius: 9px;
            border: 1px solid rgba(253, 164, 175, 0.45);
            background: rgba(127, 29, 29, 0.28);
            color: #fecaca;
        }

        .hero-quote-feedback[data-level="info"] {
            border-color: rgba(147, 197, 253, 0.45);
            background: rgba(30, 58, 138, 0.24);
            color: #bfdbfe;
        }

        .hero-quote-result {
            margin-top: 0.9rem;
            padding: 0.9rem;
            border-radius: 12px;
            border: 1px solid rgba(34, 211, 238, 0.3);
            background: rgba(8, 47, 73, 0.35);
        }

        .hero-quote-summary {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .hero-quote-summary strong {
            font-size: 1.05rem;
            color: #e0f2fe;
        }

        .hero-quote-summary span {
            font-size: 0.88rem;
            color: #a5f3fc;
        }

        .hero-quote-detail {
            margin: 0.45rem 0 0;
            color: var(--text-secondary);
            font-size: 0.84rem;
        }

        .hero-quote-fallback {
            margin: 0.45rem 0 0;
            color: #fde68a;
            font-size: 0.82rem;
        }

        .hero-quote-ctas {
            margin-top: 0.75rem;
            display: grid;
            gap: 0.55rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .hero-quote-ctas .btn {
            min-height: 44px;
            justify-content: center;
            text-align: center;
            font-size: 0.84rem;
        }

        @media (max-width: 767px) {
            .hero-quote-widget {
                margin-top: 1rem;
                padding: 1rem;
                max-width: 100%;
            }

            .hero-quote-summary {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-quote-ctas {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const widget = document.querySelector('[data-quote-widget]');
            if (!widget) {
                return;
            }

            if (widget.dataset.quoteWidgetInitialized === '1') {
                return;
            }
            widget.dataset.quoteWidgetInitialized = '1';

            if (typeof trackEvent === 'function') {
                trackEvent('quote_widget_view', { location: 'hero' });
            }

            const quoteConfig = {
                endpoint: @json(route('api.v1.quotes.store')),
                checkoutSessionEndpoint: @json(route('api.v1.checkout-sessions.store')),
                checkoutBaseUrl: @json(url('/checkout')),
                serviceBaseAmounts: @json($baseAmounts),
                serviceFallbackMinutes: @json($fallbackMinutes),
                requestTimeoutMs: {{ (int) round($requestTimeoutSeconds * 1000) }},
            };

            const form = widget.querySelector('[data-quote-form]');
            const submitButton = widget.querySelector('[data-quote-submit]');
            const submitLabel = submitButton?.dataset.defaultLabel || 'Fiyat Hesapla';
            const feedback = widget.querySelector('[data-quote-feedback]');
            const result = widget.querySelector('[data-quote-result]');
            const priceRange = widget.querySelector('[data-quote-price-range]');
            const eta = widget.querySelector('[data-quote-eta]');
            const distance = widget.querySelector('[data-quote-distance]');
            const fallback = widget.querySelector('[data-quote-fallback]');
            const startCheckoutButton = widget.querySelector('[data-quote-start-checkout]');
            const startCheckoutDefaultLabel = startCheckoutButton?.dataset.defaultLabel || 'Siparise Gec';
            const startCheckoutDefaultHtml = `<i class="fa-solid fa-arrow-right"></i> ${startCheckoutDefaultLabel}`;
            const startCheckoutFallbackLink = widget.querySelector('[data-quote-start-checkout-fallback]');
            const startCheckoutDirectLink = widget.querySelector('[data-quote-start-checkout-direct]');
            const pickupInput = widget.querySelector('#quote-pickup-address');
            const dropoffInput = widget.querySelector('#quote-dropoff-address');
            const pickupLatInput = widget.querySelector('[data-quote-pickup-lat]');
            const pickupLngInput = widget.querySelector('[data-quote-pickup-lng]');
            const dropoffLatInput = widget.querySelector('[data-quote-dropoff-lat]');
            const dropoffLngInput = widget.querySelector('[data-quote-dropoff-lng]');
            const serviceTypeInput = widget.querySelector('#quote-service-type');
            const autocompleteProvider = String(widget.dataset.quoteAutocompleteProvider || 'manual');
            const autocompleteCountry = String(widget.dataset.quoteAutocompleteCountry || 'tr').trim().toLowerCase();
            const googleMapsApiKey = String(widget.dataset.googleMapsApiKey || '').trim();
            let resolvedAutocompleteMode = autocompleteProvider;
            let latestQuotePayload = null;
            let preparedCheckoutToken = null;
            let creatingCheckoutSessionPromise = null;
            const fieldErrors = {
                pickup_address: widget.querySelector('[data-field-error="pickup_address"]'),
                dropoff_address: widget.querySelector('[data-field-error="dropoff_address"]'),
            };
            const normalizeCtaLabel = (value) => {
                if (typeof window.normalizedLabel === 'function') {
                    return window.normalizedLabel(value);
                }

                return String(value || '')
                    .replace(/\s+/g, ' ')
                    .trim()
                    .slice(0, 80);
            };
            const buildQuoteCtaPayload = (channel, label, extras = {}, sourceLink = null) => {
                const basePayload = {
                    cta_channel: channel,
                    cta_context: 'hero_quote_widget',
                    cta_label: normalizeCtaLabel(label),
                };
                const mergedExtras = { ...extras };

                if (sourceLink && typeof window.buildCtaPayload === 'function') {
                    return {
                        ...window.buildCtaPayload(sourceLink, basePayload),
                        ...mergedExtras,
                    };
                }

                return {
                    ...basePayload,
                    ...mergedExtras,
                };
            };

            const dispatchHeroInteraction = (engaged) => {
                document.dispatchEvent(new CustomEvent(
                    engaged ? 'landing:hero-quote-engage' : 'landing:hero-quote-release',
                    { detail: { source: 'hero_quote_widget' } }
                ));
            };

            const formatMoney = (minorAmount, currency) => {
                const majorAmount = Math.max(0, Number(minorAmount || 0)) / 100;

                try {
                    return new Intl.NumberFormat('tr-TR', {
                        style: 'currency',
                        currency: currency || 'TRY',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    }).format(majorAmount);
                } catch (error) {
                    return `${majorAmount.toFixed(2)} ${(currency || 'TRY')}`;
                }
            };

            const normalizeText = (value) => String(value || '').trim().toLocaleLowerCase('tr-TR');
            const parseCoordinate = (value) => {
                const parsed = Number(value);

                return Number.isFinite(parsed) ? parsed : null;
            };
            const writeCoordinates = (target, lat, lng) => {
                if (target === 'pickup') {
                    if (pickupLatInput) {
                        pickupLatInput.value = lat === null ? '' : String(lat);
                    }
                    if (pickupLngInput) {
                        pickupLngInput.value = lng === null ? '' : String(lng);
                    }

                    return;
                }

                if (dropoffLatInput) {
                    dropoffLatInput.value = lat === null ? '' : String(lat);
                }
                if (dropoffLngInput) {
                    dropoffLngInput.value = lng === null ? '' : String(lng);
                }
            };
            const readCoordinates = (target) => {
                if (target === 'pickup') {
                    return {
                        lat: parseCoordinate(pickupLatInput?.value),
                        lng: parseCoordinate(pickupLngInput?.value),
                    };
                }

                return {
                    lat: parseCoordinate(dropoffLatInput?.value),
                    lng: parseCoordinate(dropoffLngInput?.value),
                };
            };
            const coordinatesToPayload = (target) => {
                const coordinates = readCoordinates(target);
                const payload = {};

                if (coordinates.lat !== null) {
                    payload.lat = coordinates.lat;
                }

                if (coordinates.lng !== null) {
                    payload.lng = coordinates.lng;
                }

                return payload;
            };

            const loadGoogleMapsPlaces = () => {
                if (!googleMapsApiKey) {
                    return Promise.reject(new Error('maps_api_key_missing'));
                }

                if (window.google?.maps?.places?.Autocomplete) {
                    return Promise.resolve(window.google.maps.places);
                }

                if (window.__simdiGoogleMapsPlacesLoaderPromise) {
                    return window.__simdiGoogleMapsPlacesLoaderPromise;
                }

                window.__simdiGoogleMapsPlacesLoaderPromise = new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    const params = new URLSearchParams({
                        key: googleMapsApiKey,
                        libraries: 'places',
                        language: 'tr',
                        region: 'TR',
                        loading: 'async',
                    });

                    script.src = `https://maps.googleapis.com/maps/api/js?${params.toString()}`;
                    script.async = true;
                    script.defer = true;
                    script.onload = () => {
                        if (window.google?.maps?.places?.Autocomplete) {
                            resolve(window.google.maps.places);
                            return;
                        }

                        reject(new Error('maps_places_not_ready'));
                    };
                    script.onerror = () => reject(new Error('maps_script_load_failed'));
                    document.head.appendChild(script);
                });

                return window.__simdiGoogleMapsPlacesLoaderPromise;
            };

            const bindAutocomplete = (inputNode, target) => {
                if (!inputNode || !window.google?.maps?.places?.Autocomplete) {
                    return;
                }

                const autocomplete = new window.google.maps.places.Autocomplete(inputNode, {
                    fields: ['formatted_address', 'geometry.location', 'name'],
                    types: ['geocode'],
                    componentRestrictions: autocompleteCountry ? { country: autocompleteCountry } : undefined,
                });

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    const lat = place?.geometry?.location?.lat?.();
                    const lng = place?.geometry?.location?.lng?.();

                    if (typeof lat === 'number' && typeof lng === 'number') {
                        writeCoordinates(target, lat, lng);
                    } else {
                        writeCoordinates(target, null, null);
                    }

                    const resolvedAddress = String(place?.formatted_address || place?.name || '').trim();
                    if (resolvedAddress !== '') {
                        inputNode.value = resolvedAddress;
                    }

                    syncFallbackCheckoutLink();
                });
            };

            const clearPreparedCheckoutSession = () => {
                preparedCheckoutToken = null;
                creatingCheckoutSessionPromise = null;
            };

            const buildFallbackCheckoutUrl = () => {
                const params = new URLSearchParams();
                const pickup = pickupInput?.value?.trim() || '';
                const dropoff = dropoffInput?.value?.trim() || '';
                const serviceType = serviceTypeInput?.value || 'moto';
                const serviceLabel = serviceTypeInput?.options?.[serviceTypeInput.selectedIndex]?.text || serviceType;

                if (pickup !== '') {
                    params.set('pickup', pickup);
                }
                if (dropoff !== '') {
                    params.set('dropoff', dropoff);
                }
                if (serviceType !== '') {
                    params.set('service_type', serviceType);
                }
                if (serviceLabel !== '') {
                    params.set('service_label', serviceLabel);
                }

                const query = params.toString();
                return query !== ''
                    ? `${quoteConfig.checkoutBaseUrl}?${query}`
                    : quoteConfig.checkoutBaseUrl;
            };

            const resolveDeterministicContinueUrl = () => {
                if (preparedCheckoutToken) {
                    return `${quoteConfig.checkoutBaseUrl}/${preparedCheckoutToken}`;
                }

                return buildFallbackCheckoutUrl();
            };

            const syncFallbackCheckoutLink = () => {
                const fallbackUrl = resolveDeterministicContinueUrl();

                if (startCheckoutFallbackLink) {
                    startCheckoutFallbackLink.href = fallbackUrl;
                    startCheckoutFallbackLink.hidden = false;
                }

                if (startCheckoutDirectLink) {
                    startCheckoutDirectLink.href = fallbackUrl;
                }
            };

            const resetQuoteResultState = () => {
                latestQuotePayload = null;
                clearPreparedCheckoutSession();
                result.hidden = true;
                fallback.hidden = true;
                fallback.textContent = '';

                if (startCheckoutButton) {
                    startCheckoutButton.hidden = true;
                    startCheckoutButton.disabled = false;
                    startCheckoutButton.innerHTML = startCheckoutDefaultHtml;
                }
            };

            syncFallbackCheckoutLink();
            pickupInput?.addEventListener('input', () => {
                writeCoordinates('pickup', null, null);
                resetQuoteResultState();
                syncFallbackCheckoutLink();
            });
            dropoffInput?.addEventListener('input', () => {
                writeCoordinates('dropoff', null, null);
                resetQuoteResultState();
                syncFallbackCheckoutLink();
            });
            serviceTypeInput?.addEventListener('change', () => {
                resetQuoteResultState();
                syncFallbackCheckoutLink();
            });

            const setFeedback = (message, level) => {
                if (!message) {
                    feedback.hidden = true;
                    feedback.textContent = '';
                    feedback.removeAttribute('data-level');
                    return;
                }

                feedback.hidden = false;
                feedback.textContent = message;
                feedback.setAttribute('data-level', level || 'error');
            };

            const fallbackToManualAutocomplete = (reason) => {
                if (resolvedAutocompleteMode === 'manual') {
                    return;
                }

                resolvedAutocompleteMode = 'manual';
                widget.dataset.quoteAutocompleteProvider = 'manual';
                writeCoordinates('pickup', null, null);
                writeCoordinates('dropoff', null, null);
                setFeedback('Adresinizi manuel olarak yazarak devam edebilirsiniz.', 'info');

                if (typeof trackEvent === 'function') {
                    trackEvent('quote_autocomplete_fallback', {
                        provider: 'manual',
                        reason: reason || 'runtime_fallback',
                    });
                }
            };

            const clearFieldErrors = () => {
                Object.values(fieldErrors).forEach((node) => {
                    if (node) {
                        node.textContent = '';
                    }
                });
            };

            widget.addEventListener('mouseenter', () => dispatchHeroInteraction(true));
            widget.addEventListener('mouseleave', () => {
                if (!widget.matches(':focus-within')) {
                    dispatchHeroInteraction(false);
                }
            });
            widget.addEventListener('focusin', () => dispatchHeroInteraction(true));
            widget.addEventListener('focusout', () => {
                window.setTimeout(() => {
                    if (!widget.matches(':hover') && !widget.matches(':focus-within')) {
                        dispatchHeroInteraction(false);
                    }
                }, 0);
            });

            const applyFieldErrors = (errors) => {
                clearFieldErrors();
                Object.keys(fieldErrors).forEach((field) => {
                    const firstError = Array.isArray(errors?.[field]) ? errors[field][0] : null;
                    if (firstError && fieldErrors[field]) {
                        fieldErrors[field].textContent = firstError;
                    }
                });
            };

            const validate = () => {
                const pickupAddress = pickupInput.value.trim();
                const dropoffAddress = dropoffInput.value.trim();
                const errors = {};

                if (pickupAddress.length < 5) {
                    errors.pickup_address = ['Alinis adresi en az 5 karakter olmali.'];
                }

                if (dropoffAddress.length < 5) {
                    errors.dropoff_address = ['Teslimat adresi en az 5 karakter olmali.'];
                }

                if (
                    pickupAddress.length >= 5 &&
                    dropoffAddress.length >= 5 &&
                    normalizeText(pickupAddress) === normalizeText(dropoffAddress)
                ) {
                    errors.dropoff_address = ['Alinis ve teslimat adresi ayni olamaz.'];
                }

                applyFieldErrors(errors);

                return {
                    valid: Object.keys(errors).length === 0,
                    pickupAddress,
                    dropoffAddress,
                };
            };

            const setLoading = (isLoading) => {
                submitButton.disabled = isLoading;
                submitButton.innerHTML = isLoading
                    ? '<span class="typing-dots"><span></span><span></span><span></span></span> Hesaplanıyor...'
                    : `<i class="fa-solid fa-calculator"></i> ${submitLabel}`;
            };

            const showResult = (payload) => {
                latestQuotePayload = payload;
                clearPreparedCheckoutSession();
                const totalAmount = Math.max(0, Number(payload?.total_amount || 0));
                const priceMin = Math.round(totalAmount * 0.95);
                const priceMax = Math.round(totalAmount * 1.08);
                const distanceMeters = Math.max(0, Number(payload?.distance_meters || 0));
                const durationMinutesRaw = Math.round(Math.max(0, Number(payload?.duration_seconds || 0)) / 60);
                const selectedService = serviceTypeInput.value || 'moto';
                const fallbackMinutes = Math.max(
                    1,
                    Number(quoteConfig.serviceFallbackMinutes?.[selectedService] || quoteConfig.serviceFallbackMinutes?.moto || 45)
                );
                const durationMinutes = durationMinutesRaw > 0 ? durationMinutesRaw : fallbackMinutes;
                const usingFallback = durationMinutesRaw <= 0 || distanceMeters <= 0;

                priceRange.textContent = `${formatMoney(priceMin, payload?.currency)} - ${formatMoney(priceMax, payload?.currency)}`;
                eta.textContent = `Tahmini süre: ${durationMinutes} dk`;
                distance.textContent = distanceMeters > 0
                    ? `Tahmini mesafe: ${(distanceMeters / 1000).toFixed(1)} km`
                    : 'Mesafe verisi geçici olarak yaklaşık hesaplanmıştır.';

                fallback.hidden = !usingFallback;
                fallback.textContent = usingFallback
                    ? 'Mesafe servisi geçici olarak yaklaşık hesaplama kullanıyor.'
                    : '';

                result.hidden = false;
                if (startCheckoutButton) {
                    startCheckoutButton.hidden = false;
                    startCheckoutButton.disabled = false;
                    startCheckoutButton.innerHTML = startCheckoutDefaultHtml;
                }
                syncFallbackCheckoutLink();

                if (window.innerWidth < 768) {
                    result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            };

            if (autocompleteProvider === 'google_places') {
                const previousAuthFailureHandler = window.gm_authFailure;
                window.gm_authFailure = function () {
                    if (typeof previousAuthFailureHandler === 'function') {
                        previousAuthFailureHandler();
                    }

                    fallbackToManualAutocomplete('auth_failure');
                };

                loadGoogleMapsPlaces()
                    .then(() => {
                        bindAutocomplete(pickupInput, 'pickup');
                        bindAutocomplete(dropoffInput, 'dropoff');

                        if (typeof trackEvent === 'function') {
                            trackEvent('quote_autocomplete_ready', {
                                provider: autocompleteProvider,
                            });
                        }
                    })
                    .catch((error) => {
                        fallbackToManualAutocomplete(error?.message || 'loader_failed');
                    });
            }

            const handleQuoteError = (status, body) => {
                resetQuoteResultState();
                syncFallbackCheckoutLink();

                if (status === 422) {
                    applyFieldErrors(body?.errors || {});
                    setFeedback('Form bilgilerini kontrol edip tekrar deneyin.', 'error');
                    return;
                }

                if (status === 429) {
                    setFeedback('Çok hızlı istek gönderdiniz. 30 saniye sonra tekrar deneyin.', 'info');
                    return;
                }

                if (status >= 500) {
                    setFeedback('Sunucu geçici olarak yanıt veremiyor. Lütfen tekrar deneyin.', 'error');
                    return;
                }

                setFeedback('Teklif alınırken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
            };

            const requestQuote = async (payload) => {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), quoteConfig.requestTimeoutMs);

                try {
                    const response = await fetch(quoteConfig.endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify(payload),
                        signal: controller.signal,
                    });

                    let body = null;
                    try {
                        body = await response.json();
                    } catch (error) {
                        body = null;
                    }

                    return { response, body };
                } finally {
                    clearTimeout(timeoutId);
                }
            };

            const buildCheckoutSessionPayload = () => {
                if (!latestQuotePayload?.id) {
                    return null;
                }

                const serviceType = serviceTypeInput?.value || 'moto';
                const selectedServiceLabel = serviceTypeInput?.options?.[serviceTypeInput.selectedIndex]?.text || serviceType;

                return {
                    pricing_quote_id: latestQuotePayload.id,
                    current_step: 'quote',
                    payload: {
                        service_type: serviceType,
                        service_label: selectedServiceLabel,
                        pickup: {
                            address: pickupInput?.value?.trim() || '',
                            ...coordinatesToPayload('pickup'),
                        },
                        dropoff: {
                            address: dropoffInput?.value?.trim() || '',
                            ...coordinatesToPayload('dropoff'),
                        },
                        quote_preview: {
                            quote_no: latestQuotePayload?.quote_no || null,
                            total_amount: latestQuotePayload?.total_amount || null,
                            currency: latestQuotePayload?.currency || 'TRY',
                        },
                    },
                };
            };

            const createCheckoutSession = async (payload) => {
                const response = await fetch(quoteConfig.checkoutSessionEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                let body = null;
                try {
                    body = await response.json();
                } catch (error) {
                    body = null;
                }

                if (!response.ok || !body?.success || !body?.data?.token) {
                    throw new Error(body?.message || 'checkout_session_create_failed');
                }

                return body.data.token;
            };

            const ensureCheckoutSessionToken = async () => {
                if (preparedCheckoutToken) {
                    return preparedCheckoutToken;
                }

                if (creatingCheckoutSessionPromise) {
                    return creatingCheckoutSessionPromise;
                }

                const sessionPayload = buildCheckoutSessionPayload();
                if (!sessionPayload) {
                    throw new Error('quote_payload_missing');
                }

                creatingCheckoutSessionPromise = createCheckoutSession(sessionPayload)
                    .then((token) => {
                        preparedCheckoutToken = token;
                        return token;
                    })
                    .finally(() => {
                        creatingCheckoutSessionPromise = null;
                        syncFallbackCheckoutLink();
                    });

                return creatingCheckoutSessionPromise;
            };

            const prepareCheckoutSessionSilently = async () => {
                if (!latestQuotePayload?.id) {
                    return;
                }

                try {
                    await ensureCheckoutSessionToken();
                } catch (error) {
                    syncFallbackCheckoutLink();
                }
            };

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                clearFieldErrors();
                setFeedback('', '');

                const validation = validate();
                if (!validation.valid) {
                    if (typeof trackEvent === 'function') {
                        trackEvent('quote_error', { reason: 'client_validation' });
                    }
                    return;
                }

                const serviceType = serviceTypeInput.value || 'moto';
                const serviceBaseAmounts = quoteConfig.serviceBaseAmounts || {};
                const baseAmount = Math.max(
                    0,
                    Number(serviceBaseAmounts[serviceType] || serviceBaseAmounts.moto || 25000)
                );

                const payload = {
                    base_amount: baseAmount,
                    currency: 'TRY',
                    pickup: {
                        address: validation.pickupAddress,
                        ...coordinatesToPayload('pickup'),
                    },
                    dropoff: {
                        address: validation.dropoffAddress,
                        ...coordinatesToPayload('dropoff'),
                    },
                    context: {
                        channel: 'landing_hero_quote_widget',
                        service_type: serviceType,
                        pickup_address: validation.pickupAddress,
                        dropoff_address: validation.dropoffAddress,
                    },
                };

                if (typeof trackEvent === 'function') {
                    trackEvent('quote_submit_click', { service_type: serviceType });
                }

                setLoading(true);

                try {
                    const { response, body } = await requestQuote(payload);

                    if (!response.ok || !body?.success) {
                        handleQuoteError(response.status, body);

                        if (typeof trackEvent === 'function') {
                            trackEvent('quote_error', {
                                status_code: response.status,
                                service_type: serviceType,
                            });
                        }

                        return;
                    }

                    showResult(body.data || {});
                    void prepareCheckoutSessionSilently();

                    if (typeof trackEvent === 'function') {
                        trackEvent('quote_success', {
                            service_type: serviceType,
                            quote_no: body?.data?.quote_no || null,
                        });
                    }
                } catch (error) {
                    const isTimeoutError = error && error.name === 'AbortError';
                    resetQuoteResultState();
                    syncFallbackCheckoutLink();
                    setFeedback(
                        isTimeoutError
                            ? 'Yanıt süresi aşıldı. Lütfen tekrar deneyin.'
                            : 'Bağlantı hatası oluştu. Lütfen tekrar deneyin.',
                        'error'
                    );

                    if (typeof trackEvent === 'function') {
                        trackEvent('quote_error', {
                            reason: isTimeoutError ? 'timeout' : 'network',
                        });
                    }
                } finally {
                    setLoading(false);
                }
            });

            startCheckoutButton?.addEventListener('click', async () => {
                if (!latestQuotePayload?.id) {
                    setFeedback('Teklif verisi eksik. Yedek checkout akışına yönlendiriliyorsunuz.', 'info');
                    window.location.href = buildFallbackCheckoutUrl();
                    return;
                }

                const serviceType = serviceTypeInput.value || 'moto';
                const selectedServiceLabel = serviceTypeInput.options?.[serviceTypeInput.selectedIndex]?.text || serviceType;
                const baseCheckoutPayload = {
                    service_type: serviceType,
                    quote_no: latestQuotePayload.quote_no || null,
                    checkout_path: 'tokenized',
                };
                startCheckoutButton.disabled = true;
                startCheckoutButton.innerHTML = '<span class="typing-dots"><span></span><span></span><span></span></span> Hazırlanıyor...';

                try {
                    if (preparedCheckoutToken) {
                        if (typeof trackEvent === 'function') {
                            const payload = buildQuoteCtaPayload(
                                'checkout',
                                startCheckoutButton.textContent || startCheckoutDefaultLabel,
                                baseCheckoutPayload
                            );
                            trackEvent('cta_click', payload);
                            trackEvent('quote_start_checkout_click', payload);
                        }
                        window.location.href = `${quoteConfig.checkoutBaseUrl}/${preparedCheckoutToken}`;
                        return;
                    }

                    const response = await fetch(quoteConfig.checkoutSessionEndpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({
                            pricing_quote_id: latestQuotePayload.id,
                            current_step: 'quote',
                            payload: {
                                service_type: serviceType,
                                service_label: selectedServiceLabel,
                                pickup: {
                                    address: pickupInput.value.trim(),
                                    ...(() => {
                                        const coords = readCoordinates('pickup');
                                        const payloadCoordinates = {};
                                        if (coords.lat !== null) {
                                            payloadCoordinates.lat = coords.lat;
                                        }
                                        if (coords.lng !== null) {
                                            payloadCoordinates.lng = coords.lng;
                                        }

                                        return payloadCoordinates;
                                    })(),
                                },
                                dropoff: {
                                    address: dropoffInput.value.trim(),
                                    ...(() => {
                                        const coords = readCoordinates('dropoff');
                                        const payloadCoordinates = {};
                                        if (coords.lat !== null) {
                                            payloadCoordinates.lat = coords.lat;
                                        }
                                        if (coords.lng !== null) {
                                            payloadCoordinates.lng = coords.lng;
                                        }

                                        return payloadCoordinates;
                                    })(),
                                },
                                quote_preview: {
                                    quote_no: latestQuotePayload.quote_no || null,
                                    total_amount: latestQuotePayload.total_amount || null,
                                    currency: latestQuotePayload.currency || 'TRY',
                                },
                            },
                        }),
                    });

                    let body = null;
                    try {
                        body = await response.json();
                    } catch (error) {
                        body = null;
                    }

                    if (!response.ok || !body?.success || !body?.data?.token) {
                        throw new Error(body?.message || 'Checkout oturumu başlatılamadı.');
                    }

                    preparedCheckoutToken = body.data.token;
                    syncFallbackCheckoutLink();

                    if (typeof trackEvent === 'function') {
                        const payload = buildQuoteCtaPayload(
                            'checkout',
                            startCheckoutButton.textContent || startCheckoutDefaultLabel,
                            baseCheckoutPayload
                        );
                        trackEvent('cta_click', payload);
                        trackEvent('quote_start_checkout_click', payload);
                    }

                    window.location.href = `${quoteConfig.checkoutBaseUrl}/${body.data.token}`;
                } catch (error) {
                    setFeedback('Checkout oturumu otomatik açılamadı. Yedek akışa yönlendiriliyorsunuz.', 'info');
                    startCheckoutButton.disabled = false;
                    startCheckoutButton.innerHTML = startCheckoutDefaultHtml;
                    window.location.href = buildFallbackCheckoutUrl();
                }
            });

            const bindCheckoutLinkTracking = (linkNode, checkoutPath) => {
                if (!linkNode) {
                    return;
                }

                linkNode.addEventListener('click', () => {
                    if (typeof trackEvent !== 'function') {
                        return;
                    }

                    const payload = buildQuoteCtaPayload(
                        'checkout',
                        linkNode.textContent || 'Devam Et',
                        {
                            service_type: serviceTypeInput?.value || 'moto',
                            quote_no: latestQuotePayload?.quote_no || null,
                            checkout_path: checkoutPath,
                        },
                        linkNode
                    );

                    trackEvent('cta_click', payload);
                    trackEvent('quote_start_checkout_click', payload);
                });
            };

            bindCheckoutLinkTracking(startCheckoutDirectLink, 'direct_link');
            bindCheckoutLinkTracking(startCheckoutFallbackLink, 'fallback_link');

            widget.querySelectorAll('[data-quote-cta]').forEach((ctaNode) => {
                ctaNode.addEventListener('click', () => {
                    if (typeof trackEvent !== 'function') {
                        return;
                    }

                    const ctaType = ctaNode.getAttribute('data-quote-cta') === 'whatsapp' ? 'whatsapp' : 'call';
                    const payload = buildQuoteCtaPayload(
                        ctaType,
                        ctaNode.textContent || ctaType,
                        {
                            service_type: serviceTypeInput?.value || 'moto',
                            quote_no: latestQuotePayload?.quote_no || null,
                        },
                        ctaNode
                    );

                    if (ctaType === 'whatsapp') {
                        trackEvent('quote_cta_whatsapp_click', payload);
                    } else {
                        trackEvent('quote_cta_call_click', payload);
                    }
                });
            });
        });
    </script>
    @endpush
@endif
