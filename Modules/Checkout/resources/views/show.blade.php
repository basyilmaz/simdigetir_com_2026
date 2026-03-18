
<x-checkout::layouts.public title="Sipariş Akışı" description="SimdiGetir checkout wizard">
@php
    $payload = (array) ($pageState['session']['payload'] ?? []);
    $pickup = (array) ($payload['pickup'] ?? []);
    $dropoff = (array) ($payload['dropoff'] ?? []);
    $payment = (array) ($payload['payment'] ?? []);
    $quote = (array) ($pageState['quote'] ?? []);
    $customer = (array) ($pageState['customer'] ?? []);
    $support = is_array($support ?? null) ? $support : [];
    $finalizedOrder = (array) ($pageState['finalized_order'] ?? []);
    $package = (array) (($payload['packages'] ?? [])[0] ?? []);
    $bankTransfer = (array) ($pageState['payment']['bank_transfer'] ?? []);
    $cardProviderLabel = (string) ($pageState['payment']['provider_label'] ?? strtoupper((string) ($pageState['payment']['default_provider'] ?? 'CARD')));
    $stepLabels = [
        'quote' => 'Teklif',
        'auth' => 'Hesap',
        'recipient' => 'Kişi Bilgileri',
        'payment' => 'Ödeme',
        'confirm' => 'Onay',
    ];
    $stepDescriptions = [
        'quote' => 'Adres ve fiyat kontrolü',
        'auth' => 'Telefon ile kayıt veya giriş',
        'recipient' => 'Gönderen, alıcı ve paket bilgileri',
        'payment' => 'Uygun yöntemi seçin',
        'confirm' => 'Siparişi oluştur',
    ];
    $quoteAmount = ! empty($quote)
        ? number_format(((int) ($quote['total_amount'] ?? 0)) / 100, 2, ',', '.') . ' ' . ($quote['currency'] ?? 'TRY')
        : '-';
    $distanceText = isset($quote['distance_meters'])
        ? number_format(((int) $quote['distance_meters']) / 1000, 1, ',', '.') . ' km'
        : '-';
    $durationText = isset($quote['duration_seconds'])
        ? ceil(((int) $quote['duration_seconds']) / 60) . ' dk'
        : '-';
@endphp

@push('styles')
<style>
body {
    margin: 0;
    background: transparent;
    color: var(--text-primary);
    font-family: var(--sg-font-body);
}
.shell { width: min(1240px, calc(100% - 32px)); margin: 0 auto; display: grid; gap: 20px; }
.top { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr); gap: 20px; }
.grid { display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 20px; }
.card { border: 1px solid var(--sg-border-dark); border-radius: 24px; background: var(--sg-card-dark); backdrop-filter: blur(16px); box-shadow: var(--sg-shadow-dark); }
[data-theme="light"] .card { background: var(--sg-card-light-strong); border-color: var(--sg-border-light-soft); box-shadow: var(--sg-shadow-light); }
.top > .card { padding: 26px; }
.side, .main { padding: 22px; }
.top h2 { margin: 0; font-family: var(--sg-font-display); font-size: clamp(1.6rem, 2.6vw, 2.4rem); line-height: 1.05; }
.top p, .wizard-side-note, .support-links a { color: var(--text-secondary); }
.support-links { display: grid; gap: 10px; margin-top: 16px; }
.support-links a { text-decoration: none; font-weight: 700; }
.support-links a:hover { color: var(--accent); }
.steps, .summary, .form, .formgrid, .g2, .g3 { display: grid; gap: 14px; }
.steps { gap: 10px; margin: 16px 0 20px; }
.summary { gap: 10px; }
.formgrid, .g2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.g3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.step, .box, .account-strip, .choicebox, .toggle, .helper { border-radius: 18px; border: 1px solid var(--sg-border-dark); background: var(--sg-card-dark-soft); }
.step { padding: 12px 14px; }
.step.active { border-color: var(--sg-accent-warm-border-strong); background: var(--sg-accent-warm-surface-strong); }
.step.done { border-color: var(--sg-success-border-strong); background: var(--sg-success-surface-strong); }
.step strong { display: block; margin-bottom: 4px; }
.row { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; font-size: var(--sg-type-body-sm); }
.row small, .muted, .note, .head p, .panel > p, .box p, .choicebox span, .helper, .account-strip p, .summary small, .step span { color: var(--text-secondary); }
.token, .status, .badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 999px; font-size: var(--sg-type-caption); font-weight: 700; }
.token, .status { background: rgba(249, 115, 22, 0.14); border: 1px solid rgba(249, 115, 22, 0.2); color: var(--accent); }
.badge { background: rgba(34, 211, 238, 0.14); border: 1px solid rgba(34, 211, 238, 0.18); color: #22d3ee; }
.head h1 { margin: 0; font-family: var(--sg-font-display); font-size: var(--sg-type-display-lg); line-height: var(--sg-leading-display); }
.head { display: grid; gap: 10px; }
.panel { display: none; padding: 20px; border-radius: 22px; border: 1px solid var(--sg-border-dark); background: var(--sg-card-dark-muted); }
[data-theme="light"] .panel { background: var(--sg-card-light-muted); border-color: var(--sg-border-light-soft); }
.panel.show { display: block; }
.panel h2, .box h3, .choicebox strong { margin: 0; font-family: var(--sg-font-display); }
.panel h2 { margin-bottom: 8px; }
.box { padding: 16px; }
.box h3 { margin-bottom: 8px; font-size: var(--sg-type-body-sm); }
.box p, .account-strip p { margin: 0; white-space: pre-line; line-height: 1.6; }
.field { display: grid; gap: 8px; }
.field.full { grid-column: 1 / -1; }
.field label { font-size: var(--sg-type-caption); font-weight: 700; }
.field input, .field textarea, .field select { width: 100%; min-height: 52px; padding: 14px 16px; border-radius: 16px; border: 1px solid var(--sg-border-dark-strong); background: var(--sg-card-dark-soft); font: inherit; color: var(--text-primary); }
[data-theme="light"] .field input, [data-theme="light"] .field textarea, [data-theme="light"] .field select { background: var(--sg-card-light-strong); border-color: var(--sg-border-light); color: var(--sg-ink-light); }
.field textarea { min-height: 104px; resize: vertical; }
.err { min-height: 1em; margin: 0; color: var(--sg-error-text); font-size: var(--sg-type-caption-sm); }
.toggle, .account-strip, .helper { padding: 14px 16px; }
.toggle { display: inline-flex; align-items: center; gap: 10px; font-weight: 600; }
.toggle input { margin: 0; }
.choice { position: relative; }
.choice input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.choicebox { display: block; padding: 16px; min-height: 168px; }
.choice input:checked + .choicebox { border-color: var(--sg-accent-warm-border-strong); background: var(--sg-accent-warm-surface-strong); }
.choicebox span { display: block; margin-top: 8px; font-size: var(--sg-type-caption); line-height: 1.55; }
.choicebox em { display: inline-block; margin-top: 10px; font-style: normal; font-size: var(--sg-type-caption-sm); font-weight: 700; color: var(--sg-accent-warm-text-strong); }
.choice.disabled { opacity: 0.58; }
.actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 18px; }
.btn { display: inline-flex; align-items: center; justify-content: center; min-height: 50px; padding: 0 18px; border-radius: 16px; border: 0; font: inherit; font-weight: 800; text-decoration: none; cursor: pointer; }
.btn.primary { background: var(--sg-action-gradient); color: var(--sg-ink-contrast); }
.btn.secondary { background: var(--sg-card-dark-soft); color: var(--text-primary); border: 1px solid var(--sg-border-dark-strong); }
[data-theme="light"] .btn.secondary { background: var(--sg-card-light-strong); color: var(--sg-ink-light); border-color: var(--sg-border-light); }
.alert { margin-top: 16px; padding: 14px 16px; border-radius: 18px; font-size: var(--sg-type-body-sm); line-height: 1.6; }
.info { background: var(--sg-info-bg-strong); color: var(--sg-info-text-strong); }
.success { background: var(--sg-success-bg-strong); color: var(--sg-success-text-strong); }
.warn { background: var(--sg-warning-bg-strong); color: var(--sg-warning-text-strong); }
.error { background: var(--sg-error-bg-strong); color: var(--sg-error-text-strong); }
.account-strip { display: grid; gap: 10px; }
.account-strip strong { font-size: var(--sg-type-body-sm); }
.quick-actions { display: flex; flex-wrap: wrap; gap: 10px; }
.link-btn { padding: 0; border: 0; background: transparent; color: var(--sg-accent-warm-text-strong); font: inherit; font-weight: 800; cursor: pointer; }
[data-finalize-success][hidden] { display: none; }
@media (max-width: 1080px) { .top, .grid, .g2, .g3, .formgrid { grid-template-columns: 1fr; } }
</style>
@endpush

<div class="shell" data-checkout-app data-page-state='@json($pageState)'>
    <section class="top">
        <article class="card">
            <div class="section-badge">
                <i class="fa-solid fa-cart-flatbed"></i> Quote to order checkout
            </div>
            <h2>Teklifi siparişe çevirin, ödemeyi seçin, operasyonu kilitleyin.</h2>
            <p>Bu sayfa landing teklifinden sonra gerçek checkout kontratına iner. Session bilgi toplar; order sadece onay adımında oluşur.</p>
            <div class="actions">
                <a href="{{ route('home') }}" class="btn secondary">Ana sayfaya dön</a>
                <a href="{{ route('checkout.tracking') }}" class="btn secondary">Sipariş takip</a>
            </div>
        </article>
        <aside class="card">
            <div class="section-badge">
                <i class="fa-solid fa-headset"></i> Destek ve güven
            </div>
            <h2>Checkout sırasında yardıma ihtiyacınız olursa destek açık.</h2>
            <p>{{ $support['support_note'] ?? 'Destek ekibimiz telefon, WhatsApp veya e-posta uzerinden yardimci olur.' }}</p>
            <div class="support-links">
                <a href="{{ $support['phone_href'] ?? 'tel:+905513567292' }}">{{ $support['phone_display'] ?? '+90 551 356 72 92' }}</a>
                <a href="{{ $support['whatsapp_href'] ?? 'https://wa.me/905513567292' }}" target="_blank" rel="noopener">WhatsApp desteği</a>
                <a href="{{ $support['email_href'] ?? 'mailto:webgetir@simdigetir.com' }}">{{ $support['email'] ?? 'webgetir@simdigetir.com' }}</a>
                <a href="{{ $support['privacy_href'] ?? url('/kvkk') }}">KVKK</a>
            </div>
        </aside>
    </section>

    <div class="grid">
        <aside class="card side">
            <h2>Akış Özeti</h2>
            <div class="token">Token: {{ $checkoutSession->token }}</div>
            <div class="steps">
                @foreach ($stepLabels as $stepKey => $stepLabel)
                    <div class="step" data-step="{{ $stepKey }}"><strong>{{ $stepLabel }}</strong><span>{{ $stepDescriptions[$stepKey] ?? '' }}</span></div>
                @endforeach
            </div>
            <div class="summary">
                <div class="row"><small>Alış</small><strong data-summary-pickup>{{ $pickup['address'] ?? '-' }}</strong></div>
                <div class="row"><small>Teslimat</small><strong data-summary-dropoff>{{ $dropoff['address'] ?? '-' }}</strong></div>
                <div class="row"><small>Hizmet</small><strong data-summary-service>{{ (string) ($payload['service_label'] ?? strtoupper((string) ($payload['service_type'] ?? 'moto'))) }}</strong></div>
                <div class="row"><small>Mesafe</small><strong>{{ $distanceText }}</strong></div>
                <div class="row"><small>Tahmini süre</small><strong>{{ $durationText }}</strong></div>
                <div class="row"><small>Tutar</small><strong>{{ $quoteAmount }}</strong></div>
                <div class="row"><small>Ödeme</small><strong data-summary-payment-method>{{ $payment['method'] ?? '-' }}</strong></div>
                <div class="row"><small>Zaman</small><strong data-summary-payment-timing>{{ $payment['timing'] ?? '-' }}</strong></div>
                <div class="row"><small>Ödeyen</small><strong data-summary-payer-role>{{ $payment['payer_role'] ?? '-' }}</strong></div>
                <div class="row"><small>Paket</small><strong data-summary-package>{{ $package['package_type'] ?? '-' }}</strong></div>
                <div class="row"><small>Hesap</small><strong data-summary-customer>{{ $customer['phone'] ?? '-' }}</strong></div>
            </div>
        </aside>

        <main class="card main">
            <div class="head">
                <h1>Teklifi siparişe çevir, ödemeyi seç, operasyonu kilitle.</h1>
                <p>Bu sayfa hero teklifinden sonra gerçek order kontratına iner. Checkout session bilgi toplar, order sadece onay adımında oluşur.</p>
                <div class="status" data-checkout-status>Durum: {{ $checkoutSession->status }}</div>
            </div>
            <div style="margin-top:24px;display:grid;gap:16px">
                <section class="panel" data-step-panel="quote">
                    <h2>Teklif doğrulama</h2>
                    <p>Alış ve teslimat adreslerini, hizmet tipini ve toplam ücreti burada netleştirin. Bu adım sipariş oluşturmaz.</p>
                    <div class="g2">
                        <div class="box"><h3>Alış</h3><p>{{ $pickup['address'] ?? '-' }}</p></div>
                        <div class="box"><h3>Teslimat</h3><p>{{ $dropoff['address'] ?? '-' }}</p></div>
                        <div class="box"><h3>Hizmet ve süre</h3><p>{{ (string) ($payload['service_label'] ?? strtoupper((string) ($payload['service_type'] ?? 'moto'))) }}
Mesafe: {{ $distanceText }}
Tahmini süre: {{ $durationText }}</p></div>
                        <div class="box"><h3>Toplam fiyat</h3><p>{{ $quoteAmount }}</p></div>
                    </div>
                    <div class="actions"><button type="button" class="btn primary" data-action="continue-from-quote">Devam Et</button><a href="{{ route('home') }}" class="btn secondary">Ana sayfaya dön</a></div>
                </section>
                <section class="panel" data-step-panel="auth">
                    <h2>Kayıt veya giriş</h2>
                    <p>Phase 1 kararına göre OTP yok. Müşteri akışı telefon + şifre ile ilerler.</p>
                    <div class="g3">
                        <label class="choice">
                            <input type="radio" name="auth_mode" value="register" checked>
                            <span class="choicebox"><strong>Yeni müşteri</strong><span>Ad, telefon ve şifre ile hesap oluştur. Siparişler daha sonra "Hesabım" alanından izlenir.</span><em>Önerilen</em></span>
                        </label>
                        <label class="choice">
                            <input type="radio" name="auth_mode" value="login">
                            <span class="choicebox"><strong>Var olan hesap</strong><span>Telefon ve şifre ile giriş yap. Kayıtlı müşteriysen alış bilgilerini hızlı doldurabilirsin.</span><em>Hızlı geçiş</em></span>
                        </label>
                        <label class="choice">
                            <input type="radio" name="auth_mode" value="guest">
                            <span class="choicebox"><strong>Misafir devam</strong><span>Hesap açmadan siparişe devam et. Sipariş kaydı telefon numarası ile oluşturulur.</span><em>Hızlı Checkout</em></span>
                        </label>
                    </div>
                    <div class="alert info" data-auth-note>Başarılı doğrulama sonrasında customer_id checkout session'a yazılır ve sipariş bu hesapla ilişkilendirilir.</div>
                    <div class="account-strip" data-account-strip {{ !empty($customer) ? '' : 'hidden' }}>
                        <span class="badge">Bağlı hesap</span>
                        <strong data-account-name>{{ $customer['name'] ?? '-' }}</strong>
                        <p data-account-contact>{{ trim(($customer['phone'] ?? '') . "\n" . ($customer['email'] ?? '')) }}</p>
                    </div>
                    <form class="form" data-auth-form>
                        <div class="formgrid">
                            <div class="field" data-register-only>
                                <label for="auth-name">Ad Soyad</label>
                                <input id="auth-name" name="name" type="text" placeholder="Örn: Ayşe Yılmaz" value="{{ $customer['name'] ?? '' }}">
                                <p class="err" data-field-error="auth.name"></p>
                            </div>
                            <div class="field">
                                <label for="auth-phone">Telefon</label>
                                <input id="auth-phone" name="phone" type="text" placeholder="0551 356 72 92" value="{{ $customer['phone'] ?? '' }}">
                                <p class="err" data-field-error="auth.phone"></p>
                            </div>
                            <div class="field" data-register-only>
                                <label for="auth-email">E-posta (opsiyonel)</label>
                                <input id="auth-email" name="email" type="email" placeholder="ornek@alanadi.com" value="{{ $customer['email'] ?? '' }}">
                                <p class="err" data-field-error="auth.email"></p>
                            </div>
                            <div class="field" data-auth-password-group>
                                <label for="auth-password">Şifre</label>
                                <input id="auth-password" name="password" type="password" placeholder="En az 8 karakter">
                                <p class="err" data-field-error="auth.password"></p>
                            </div>
                        </div>
                        <div class="actions"><button type="submit" class="btn primary" data-auth-submit>Hesabı Bağla</button><button type="button" class="btn secondary" data-step-back="quote">Geri</button></div>
                    </form>
                </section>

                <section class="panel" data-step-panel="recipient">
                    <h2>Gönderen, alıcı ve paket detayları</h2>
                    <p>Adresler tekliften gelir. Bu adımda kişi bilgileri, gönderi tipi ve operasyon notları kesinleşir.</p>
                    <div class="account-strip" data-recipient-account {{ !empty($customer) ? '' : 'hidden' }}>
                        <span class="badge">Hesap sahibi</span>
                        <strong data-recipient-account-name>{{ $customer['name'] ?? '-' }}</strong>
                        <p data-recipient-account-contact>{{ trim(($customer['phone'] ?? '') . "\n" . ($customer['email'] ?? '')) }}</p>
                        <div class="quick-actions"><button type="button" class="link-btn" data-fill-pickup-from-account>Göndereni hesap sahibi ile doldur</button></div>
                    </div>
                    <form class="form" data-recipient-form>
                        <div class="formgrid">
                            <div class="field">
                                <label for="pickup-name">Gönderen Ad Soyad</label>
                                <input id="pickup-name" name="pickup_name" type="text" value="{{ $pickup['name'] ?? ($customer['name'] ?? '') }}">
                                <p class="err" data-field-error="recipient.pickup_name"></p>
                            </div>
                            <div class="field">
                                <label for="pickup-phone">Gönderen Telefon</label>
                                <input id="pickup-phone" name="pickup_phone" type="text" value="{{ $pickup['phone'] ?? ($customer['phone'] ?? '') }}">
                                <p class="err" data-field-error="recipient.pickup_phone"></p>
                            </div>
                            <div class="field full">
                                <label for="pickup-address">Alış Adresi</label>
                                <textarea id="pickup-address" name="pickup_address">{{ $pickup['address'] ?? '' }}</textarea>
                                <p class="err" data-field-error="recipient.pickup_address"></p>
                            </div>
                            <div class="field full">
                                <label class="toggle"><input type="checkbox" name="same_person" value="1" {{ !empty($payload['same_person']) ? 'checked' : '' }}><span>Gönderen ve alıcı aynı kişi ise isim ve telefon aynalansın.</span></label>
                            </div>
                            <div class="field">
                                <label for="dropoff-name">Alıcı Ad Soyad</label>
                                <input id="dropoff-name" name="dropoff_name" type="text" value="{{ $dropoff['name'] ?? '' }}">
                                <p class="err" data-field-error="recipient.dropoff_name"></p>
                            </div>
                            <div class="field">
                                <label for="dropoff-phone">Alıcı Telefon</label>
                                <input id="dropoff-phone" name="dropoff_phone" type="text" value="{{ $dropoff['phone'] ?? '' }}">
                                <p class="err" data-field-error="recipient.dropoff_phone"></p>
                            </div>
                            <div class="field full">
                                <label for="dropoff-address">Teslimat Adresi</label>
                                <textarea id="dropoff-address" name="dropoff_address">{{ $dropoff['address'] ?? '' }}</textarea>
                                <p class="err" data-field-error="recipient.dropoff_address"></p>
                            </div>
                        </div>
                        <div class="g2">
                            <div class="field">
                                <label for="package-type">Gönderi Şekli</label>
                                <select id="package-type" name="package_type">
                                    <option value="document" {{ ($package['package_type'] ?? '') === 'document' ? 'selected' : '' }}>Evrak</option>
                                    <option value="parcel" {{ ($package['package_type'] ?? '') === 'parcel' ? 'selected' : '' }}>Paket</option>
                                    <option value="food" {{ ($package['package_type'] ?? '') === 'food' ? 'selected' : '' }}>Yemek</option>
                                    <option value="flower" {{ ($package['package_type'] ?? '') === 'flower' ? 'selected' : '' }}>Çiçek</option>
                                    <option value="electronics" {{ ($package['package_type'] ?? '') === 'electronics' ? 'selected' : '' }}>Elektronik</option>
                                    <option value="other" {{ ($package['package_type'] ?? '') === 'other' ? 'selected' : '' }}>Diğer</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="package-quantity">Adet</label>
                                <input id="package-quantity" name="package_quantity" type="number" min="1" step="1" value="{{ (int) ($package['quantity'] ?? 1) }}">
                            </div>
                            <div class="field">
                                <label for="package-weight">Tahmini Ağırlık (gr)</label>
                                <input id="package-weight" name="package_weight_grams" type="number" min="0" step="50" value="{{ $package['weight_grams'] ?? '' }}">
                            </div>
                            <div class="field">
                                <label for="package-value">Beyan Edilen Değer (TRY)</label>
                                <input id="package-value" name="package_declared_value_amount" type="number" min="0" step="1" value="{{ $package['declared_value_amount'] ?? '' }}">
                            </div>
                            <div class="field full">
                                <label for="package-description">Gönderi Açıklaması</label>
                                <textarea id="package-description" name="package_description" placeholder="Örn: 1 adet telefon kutusu, kırılabilir değil">{{ $package['description'] ?? '' }}</textarea>
                            </div>
                            <div class="field full">
                                <label for="delivery-notes">Teslimat Notu</label>
                                <textarea id="delivery-notes" name="delivery_notes" placeholder="Kurye bina girişinden arasın, resepsiyona bırakılmasın">{{ $payload['notes']['delivery_notes'] ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="helper">Pickup ve delivery proof operasyon tarafında ayrıca tutulur. Bu ekrandaki paket bilgisi kurye ve admin görünümlerini besler.</div>
                        <div class="actions"><button type="submit" class="btn primary">Bilgileri kaydet</button><button type="button" class="btn secondary" data-step-back="auth">Geri</button></div>
                    </form>
                </section>
                <section class="panel" data-step-panel="payment">
                    <h2>Ödeme yöntemi</h2>
                    <p>Phase 1 kombinasyonları: kart + prepaid, havale + prepaid, nakit + teslimatta ödeme.</p>
                    <div class="g3">
                        <label class="choice {{ empty($pageState['payment']['card_ready']) ? 'disabled' : '' }}">
                            <input type="radio" name="payment_method" value="card" {{ ($payment['method'] ?? '') === 'card' ? 'checked' : '' }} {{ empty($pageState['payment']['card_ready']) ? 'disabled' : '' }}>
                            <span class="choicebox"><strong>Kredi Kartı</strong><span>Sipariş draft olarak açılır. {{ $cardProviderLabel }} aktifse prepaid tahsilat başlatılır.</span><em>{{ !empty($pageState['payment']['card_ready']) ? 'Aktif' : 'Aktivasyon bekleniyor' }}</em></span>
                        </label>
                        <label class="choice">
                            <input type="radio" name="payment_method" value="bank_transfer" {{ ($payment['method'] ?? '') === 'bank_transfer' ? 'checked' : '' }}>
                            <span class="choicebox"><strong>Havale / EFT</strong><span>Sipariş pending_payment olarak açılır. Dekont yükleme bu fazda yok; muhasebe admin reconcile ile onaylar.</span><em>Phase 1 aktif</em></span>
                        </label>
                        <label class="choice">
                            <input type="radio" name="payment_method" value="cash" {{ ($payment['method'] ?? '') === 'cash' ? 'checked' : '' }}>
                            <span class="choicebox"><strong>Nakit</strong><span>Yalnızca teslimatta ödeme. Ödeyen varsayılan olarak alıcı olur ve sipariş dispatch akışına hazır açılır.</span><em>Phase 1 aktif</em></span>
                        </label>
                    </div>
                    <div class="helper" data-payment-detail>Havale seçildiğinde sipariş pending_payment olarak açılır. Nakit seçildiğinde teslimatta tahsil edilir. Kart seçimi provider durumuna bağlıdır.</div>
                    <div class="box" data-bank-transfer-box {{ ($payment['method'] ?? '') === 'bank_transfer' ? '' : 'hidden' }}>
                        <h3 data-bank-transfer-title>{{ $bankTransfer['title'] ?? 'Havale / EFT Ödeme Talimatı' }}</h3>
                        <p data-bank-transfer-body>{{ trim(collect([
                            $bankTransfer['body'] ?? null,
                            !empty($bankTransfer['bank_name']) ? 'Banka: '.$bankTransfer['bank_name'] : null,
                            !empty($bankTransfer['account_holder']) ? 'Hesap Sahibi: '.$bankTransfer['account_holder'] : null,
                            !empty($bankTransfer['iban']) ? 'IBAN: '.$bankTransfer['iban'] : null,
                            $bankTransfer['reference_note'] ?? null,
                        ])->filter()->implode("\n")) }}</p>
                    </div>
                    <div class="actions"><button type="button" class="btn primary" data-payment-save>Ödeme seçimini kaydet</button><button type="button" class="btn secondary" data-step-back="recipient">Geri</button></div>
                    <div class="alert warn" data-payment-help>Ödeme seçiminiz henüz kaydedilmedi.</div>
                </section>

                <section class="panel" data-step-panel="confirm">
                    <h2>Son kontrol ve sipariş oluşturma</h2>
                    <p>Bu adım checkout session'ı tamamlar ve gerçek order kaydını yaratır.</p>
                    <div class="g2">
                        <div class="box"><h3>Bağlı hesap</h3><p data-confirm-customer>{{ trim(($customer['name'] ?? '') . "\n" . ($customer['phone'] ?? '') . "\n" . ($customer['email'] ?? '')) }}</p></div>
                        <div class="box"><h3>Gönderen</h3><p data-confirm-pickup>{{ trim(($pickup['name'] ?? '') . "\n" . ($pickup['phone'] ?? '') . "\n" . ($pickup['address'] ?? '')) }}</p></div>
                        <div class="box"><h3>Alıcı</h3><p data-confirm-dropoff>{{ trim(($dropoff['name'] ?? '') . "\n" . ($dropoff['phone'] ?? '') . "\n" . ($dropoff['address'] ?? '')) }}</p></div>
                        <div class="box"><h3>Paket</h3><p data-confirm-package>{{ trim(($package['package_type'] ?? '-') . "\nAdet: " . ((int) ($package['quantity'] ?? 1)) . "\n" . ($package['description'] ?? '')) }}</p></div>
                        <div class="box"><h3>Ödeme</h3><p data-confirm-payment>{{ trim(($payment['method'] ?? '-') . ' / ' . ($payment['timing'] ?? '-')) }}</p></div>
                        <div class="box"><h3>Toplam</h3><p>{{ $quoteAmount }}</p></div>
                    </div>
                    <div class="actions"><button type="button" class="btn primary" data-finalize-submit>Siparişi oluştur</button><button type="button" class="btn secondary" data-step-back="payment">Geri</button></div>
                    <div class="box" data-confirm-bank-transfer-box {{ ($payment['method'] ?? '') === 'bank_transfer' ? '' : 'hidden' }}>
                        <h3>{{ $bankTransfer['title'] ?? 'Havale / EFT Ödeme Talimatı' }}</h3>
                        <p data-confirm-bank-transfer-body>{{ trim(collect([
                            $bankTransfer['body'] ?? null,
                            !empty($bankTransfer['bank_name']) ? 'Banka: '.$bankTransfer['bank_name'] : null,
                            !empty($bankTransfer['account_holder']) ? 'Hesap Sahibi: '.$bankTransfer['account_holder'] : null,
                            !empty($bankTransfer['iban']) ? 'IBAN: '.$bankTransfer['iban'] : null,
                            $bankTransfer['reference_note'] ?? null,
                        ])->filter()->implode("\n")) }}</p>
                    </div>
                    <div data-finalize-feedback></div>
                    <div class="alert success" data-finalize-success {{ !empty($finalizedOrder) ? '' : 'hidden' }}>
                        <strong data-finalize-order-no>{{ !empty($finalizedOrder) ? 'Sipariş No: ' . ($finalizedOrder['order_no'] ?? '') : '' }}</strong>
                        <div data-finalize-order-message>@if (!empty($finalizedOrder))Durum: {{ $finalizedOrder['state'] ?? '-' }} / Ödeme: {{ $finalizedOrder['payment_state'] ?? '-' }}@endif</div>
                        <div data-finalize-bank-transfer-note {{ (($finalizedOrder['payment_method'] ?? '') === 'bank_transfer') ? '' : 'hidden' }}>{{ trim(collect([
                            $bankTransfer['body'] ?? null,
                            !empty($bankTransfer['bank_name']) ? 'Banka: '.$bankTransfer['bank_name'] : null,
                            !empty($bankTransfer['account_holder']) ? 'Hesap Sahibi: '.$bankTransfer['account_holder'] : null,
                            !empty($bankTransfer['iban']) ? 'IBAN: '.$bankTransfer['iban'] : null,
                            $bankTransfer['reference_note'] ?? null,
                        ])->filter()->implode("\n")) }}</div>
                        @php($showFinalizePaymentButton = !empty($finalizedOrder) && ($finalizedOrder['payment_method'] ?? '') === 'card' && ($finalizedOrder['payment_state'] ?? '') === 'pending' && (($pageState['payment']['card_ready'] ?? false) === true))
                        <div class="actions">
                            <button
                                type="button"
                                class="btn primary"
                                data-finalize-payment-button
                                {{ $showFinalizePaymentButton ? '' : 'hidden' }}
                            >{{ $showFinalizePaymentButton ? 'Kart ödemesine geç' : '' }}</button>
                            <a href="{{ route('checkout.tracking') }}" class="btn secondary" data-finalize-tracking-link {{ !empty($finalizedOrder) ? '' : 'hidden' }}>Siparişi takip et</a>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const app = document.querySelector('[data-checkout-app]');
    if (!app || app.dataset.checkoutInitialized === '1') {
        return;
    }

    app.dataset.checkoutInitialized = '1';
    const pageState = JSON.parse(app.dataset.pageState || '{}');
    const session = pageState.session || {};
    const endpoints = pageState.endpoints || {};
    const paymentState = pageState.payment || {};
    const trackingBaseUrl = @json(route('checkout.tracking'));
    const steps = ['quote', 'auth', 'recipient', 'payment', 'confirm'];
    const stepNodes = [...document.querySelectorAll('[data-step]')];
    const panels = [...document.querySelectorAll('[data-step-panel]')];
    const statusNode = document.querySelector('[data-checkout-status]');
    const finalizeFeedback = document.querySelector('[data-finalize-feedback]');
    const finalizeSuccess = document.querySelector('[data-finalize-success]');
    const finalizeOrderNo = document.querySelector('[data-finalize-order-no]');
    const finalizeOrderMessage = document.querySelector('[data-finalize-order-message]');
    const finalizeBankTransferNote = document.querySelector('[data-finalize-bank-transfer-note]');
    const finalizePaymentButton = document.querySelector('[data-finalize-payment-button]');
    const finalizeTrackingLink = document.querySelector('[data-finalize-tracking-link]');
    const paymentDetailNode = document.querySelector('[data-payment-detail]');
    const paymentHelpNode = document.querySelector('[data-payment-help]');
    const bankTransferBox = document.querySelector('[data-bank-transfer-box]');
    const bankTransferTitleNode = document.querySelector('[data-bank-transfer-title]');
    const bankTransferBodyNode = document.querySelector('[data-bank-transfer-body]');
    const confirmBankTransferBox = document.querySelector('[data-confirm-bank-transfer-box]');
    const confirmBankTransferBodyNode = document.querySelector('[data-confirm-bank-transfer-body]');
    const authNote = document.querySelector('[data-auth-note]');
    const accountStrip = document.querySelector('[data-account-strip]');
    const accountNameNode = document.querySelector('[data-account-name]');
    const accountContactNode = document.querySelector('[data-account-contact]');
    const recipientAccount = document.querySelector('[data-recipient-account]');
    const recipientAccountName = document.querySelector('[data-recipient-account-name]');
    const recipientAccountContact = document.querySelector('[data-recipient-account-contact]');
    const fillPickupFromAccountButton = document.querySelector('[data-fill-pickup-from-account]');
    const bankTransferConfig = paymentState.bank_transfer || {};

    let state = {
        currentStep: session.current_step || 'quote',
        status: session.status || 'draft',
        customerId: session.customer_id || null,
        payload: session.payload || {},
        finalizedOrder: pageState.finalized_order || null,
        customer: pageState.customer || (session.payload || {}).customer || null,
        paymentUrl: '',
    };

    if (state.finalizedOrder && state.status === 'completed') {
        state.currentStep = 'confirm';
    } else if (state.customerId && state.currentStep === 'auth') {
        state.currentStep = 'recipient';
    }

    const authModeInputs = [...document.querySelectorAll('input[name="auth_mode"]')];
    const authForm = document.querySelector('[data-auth-form]');
    const authSubmit = document.querySelector('[data-auth-submit]');
    const registerOnlyNodes = [...document.querySelectorAll('[data-register-only]')];
    const authPasswordGroups = [...document.querySelectorAll('[data-auth-password-group]')];
    const recipientForm = document.querySelector('[data-recipient-form]');
    const samePersonInput = recipientForm?.querySelector('input[name="same_person"]');
    const pickupNameInput = recipientForm?.querySelector('input[name="pickup_name"]');
    const pickupPhoneInput = recipientForm?.querySelector('input[name="pickup_phone"]');
    const pickupAddressInput = recipientForm?.querySelector('textarea[name="pickup_address"]');
    const dropoffNameInput = recipientForm?.querySelector('input[name="dropoff_name"]');
    const dropoffPhoneInput = recipientForm?.querySelector('input[name="dropoff_phone"]');
    const dropoffAddressInput = recipientForm?.querySelector('textarea[name="dropoff_address"]');
    const packageTypeInput = recipientForm?.querySelector('select[name="package_type"]');
    const packageQuantityInput = recipientForm?.querySelector('input[name="package_quantity"]');
    const packageWeightInput = recipientForm?.querySelector('input[name="package_weight_grams"]');
    const packageValueInput = recipientForm?.querySelector('input[name="package_declared_value_amount"]');
    const packageDescriptionInput = recipientForm?.querySelector('textarea[name="package_description"]');
    const deliveryNotesInput = recipientForm?.querySelector('textarea[name="delivery_notes"]');

    const showAlert = (node, level, message) => {
        if (!node) {
            return;
        }
        node.className = `alert ${level}`;
        node.textContent = message;
        node.hidden = !message;
    };

    const clearErrors = (scope) => {
        document.querySelectorAll(`[data-field-error^="${scope}."]`).forEach((node) => {
            node.textContent = '';
        });
    };

    const applyErrors = (scope, errors) => {
        clearErrors(scope);
        Object.entries(errors || {}).forEach(([field, messages]) => {
            const node = document.querySelector(`[data-field-error="${scope}.${field}"]`);
            if (node) {
                node.textContent = Array.isArray(messages) ? (messages[0] || '') : String(messages || '');
            }
        });
    };

    const requestJson = async (url, method, body) => {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify(body || {}),
        });

        let json = null;
        try {
            json = await response.json();
        } catch (error) {
            json = null;
        }

        return { response, json };
    };

    const setText = (selector, value) => {
        const node = document.querySelector(selector);
        if (node) {
            node.textContent = value || '-';
        }
    };

    const setValue = (node, value) => {
        if (node && document.activeElement !== node) {
            node.value = value || '';
        }
    };

    const normalizeCustomer = () => {
        if (state.payload.customer && typeof state.payload.customer === 'object') {
            state.customer = state.payload.customer;
        }
    };

    const selectedPackage = () => {
        const packages = Array.isArray(state.payload.packages) ? state.payload.packages : [];
        return packages[0] || {};
    };

    const selectedTrackingPhone = () => {
        return (state.payload.dropoff || {}).phone || (state.payload.pickup || {}).phone || (state.customer || {}).phone || '';
    };

    const bankTransferText = () => {
        return [
            bankTransferConfig.body || '',
            bankTransferConfig.bank_name ? `Banka: ${bankTransferConfig.bank_name}` : '',
            bankTransferConfig.account_holder ? `Hesap Sahibi: ${bankTransferConfig.account_holder}` : '',
            bankTransferConfig.iban ? `IBAN: ${bankTransferConfig.iban}` : '',
            bankTransferConfig.reference_note || '',
        ].filter(Boolean).join('\n');
    };

    const syncAccountPanels = () => {
        const customer = state.customer || {};
        const hasCustomer = Boolean(customer.phone || customer.name || customer.email);
        if (accountStrip) {
            accountStrip.hidden = !hasCustomer;
        }
        if (recipientAccount) {
            recipientAccount.hidden = !hasCustomer;
        }
        if (accountNameNode) {
            accountNameNode.textContent = customer.name || '-';
        }
        if (accountContactNode) {
            accountContactNode.textContent = [customer.phone, customer.email].filter(Boolean).join('\n');
        }
        if (recipientAccountName) {
            recipientAccountName.textContent = customer.name || '-';
        }
        if (recipientAccountContact) {
            recipientAccountContact.textContent = [customer.phone, customer.email].filter(Boolean).join('\n');
        }
    };

    const syncForms = () => {
        normalizeCustomer();
        const pickup = state.payload.pickup || {};
        const dropoff = state.payload.dropoff || {};
        const pkg = selectedPackage();
        const customer = state.customer || {};

        setValue(document.querySelector('#auth-name'), customer.name || '');
        setValue(document.querySelector('#auth-phone'), customer.phone || '');
        setValue(document.querySelector('#auth-email'), customer.email || '');
        setValue(pickupNameInput, pickup.name || customer.name || '');
        setValue(pickupPhoneInput, pickup.phone || customer.phone || '');
        setValue(pickupAddressInput, pickup.address || '');
        setValue(dropoffNameInput, dropoff.name || '');
        setValue(dropoffPhoneInput, dropoff.phone || '');
        setValue(dropoffAddressInput, dropoff.address || '');
        if (samePersonInput) {
            samePersonInput.checked = Boolean(state.payload.same_person);
        }
        setValue(packageTypeInput, pkg.package_type || 'document');
        setValue(packageQuantityInput, String(pkg.quantity || 1));
        setValue(packageWeightInput, pkg.weight_grams || '');
        setValue(packageValueInput, pkg.declared_value_amount || '');
        setValue(packageDescriptionInput, pkg.description || '');
        setValue(deliveryNotesInput, ((state.payload.notes || {}).delivery_notes) || '');

        const paymentMethod = ((state.payload.payment || {}).method) || '';
        document.querySelectorAll('input[name="payment_method"]').forEach((node) => {
            node.checked = node.value === paymentMethod;
        });

        syncAccountPanels();
    };

    const syncSummary = () => {
        const pickup = state.payload.pickup || {};
        const dropoff = state.payload.dropoff || {};
        const payment = state.payload.payment || {};
        const customer = state.customer || {};
        const pkg = selectedPackage();

        setText('[data-summary-pickup]', pickup.address || '-');
        setText('[data-summary-dropoff]', dropoff.address || '-');
        setText('[data-summary-service]', state.payload.service_label || (state.payload.service_type || 'moto').toUpperCase());
        setText('[data-summary-payment-method]', payment.method || '-');
        setText('[data-summary-payment-timing]', payment.timing || '-');
        setText('[data-summary-payer-role]', payment.payer_role || '-');
        setText('[data-summary-package]', pkg.package_type || '-');
        setText('[data-summary-customer]', customer.phone || '-');
        setText('[data-confirm-customer]', [customer.name, customer.phone, customer.email].filter(Boolean).join('\n'));
        setText('[data-confirm-pickup]', [pickup.name, pickup.phone, pickup.address].filter(Boolean).join('\n'));
        setText('[data-confirm-dropoff]', [dropoff.name, dropoff.phone, dropoff.address].filter(Boolean).join('\n'));
        setText('[data-confirm-package]', [pkg.package_type || '-', `Adet: ${pkg.quantity || 1}`, pkg.weight_grams ? `Agirlik: ${pkg.weight_grams} gr` : '', pkg.declared_value_amount ? `Deger: ${pkg.declared_value_amount} TRY` : '', pkg.description || ''].filter(Boolean).join('\n'));
        setText('[data-confirm-payment]', `${payment.method || '-'} / ${payment.timing || '-'}`);
    };

    const syncTrackingLink = () => {
        if (!finalizeTrackingLink || !state.finalizedOrder) {
            return;
        }
        const params = new URLSearchParams({ order_no: state.finalizedOrder.order_no || '' });
        const phone = selectedTrackingPhone();
        if (phone) {
            params.set('phone', phone);
        }
        finalizeTrackingLink.href = `${trackingBaseUrl}?${params.toString()}`;
        finalizeTrackingLink.hidden = false;
    };

    const syncPaymentActionButton = () => {
        if (!finalizePaymentButton) {
            return;
        }

        const shouldShow = Boolean(
            state.finalizedOrder
            && state.finalizedOrder.payment_method === 'card'
            && state.finalizedOrder.payment_state === 'pending'
            && paymentState.card_ready
            && endpoints.payment_initiate
        );

        finalizePaymentButton.hidden = !shouldShow;
        finalizePaymentButton.textContent = shouldShow
            ? (state.paymentUrl ? 'Kart ödeme ekranını tekrar aç' : 'Kart ödemesine geç')
            : '';
    };

    const syncBankTransferBoxes = () => {
        const method = (state.payload.payment || {}).method || '';
        const shouldShow = method === 'bank_transfer';
        const text = bankTransferText();

        if (bankTransferTitleNode) {
            bankTransferTitleNode.textContent = bankTransferConfig.title || 'Havale / EFT Ödeme Talimatı';
        }
        if (bankTransferBodyNode) {
            bankTransferBodyNode.textContent = text || 'Havale talimatı henüz tanımlanmadı.';
        }
        if (confirmBankTransferBodyNode) {
            confirmBankTransferBodyNode.textContent = text || 'Havale talimatı henüz tanımlanmadı.';
        }
        if (bankTransferBox) {
            bankTransferBox.hidden = !shouldShow;
        }
        if (confirmBankTransferBox) {
            confirmBankTransferBox.hidden = !shouldShow;
        }
        if (finalizeBankTransferNote) {
            finalizeBankTransferNote.hidden = !(shouldShow && state.finalizedOrder);
            finalizeBankTransferNote.textContent = text || 'Havale talimatı henüz tanımlanmadı.';
        }
    };

    const syncPaymentDetail = () => {
        const method = document.querySelector('input[name="payment_method"]:checked')?.value || (state.payload.payment || {}).method || '';
        if (!paymentDetailNode) {
            return;
        }
        if (method === 'bank_transfer') {
            paymentDetailNode.textContent = 'Havale seçildi. Sipariş pending_payment olarak açılır. Aşağıdaki talimat ve referans notuna göre ödemeyi tamamlayın.';
            syncBankTransferBoxes();
            return;
        }
        if (method === 'cash') {
            paymentDetailNode.textContent = 'Nakit seçildi. Phase 1 kuralı gereği yalnızca teslimatta ödeme aktif. Tahsilat alıcı tarafından yapılır.';
            syncBankTransferBoxes();
            return;
        }
        if (method === 'card') {
            paymentDetailNode.textContent = paymentState.card_ready
                ? `Kart seçildi. Sipariş finalize sonrası ${paymentState.provider_label || 'kart ödeme'} ekranına yönlendirilirsiniz.`
                : 'Kart ödeme şu an pasif. Provider aktif olmadığı için prepaid kart akışı bu sürüm diliminde kapalı.';
            syncBankTransferBoxes();
            return;
        }
        paymentDetailNode.textContent = 'Ödeme yöntemi seçildiğinde sistem order state ve payment state kombinasyonunu buna göre kurar.';
        syncBankTransferBoxes();
    };

    const render = () => {
        normalizeCustomer();
        const current = state.finalizedOrder && state.status === 'completed'
            ? 'confirm'
            : (steps.includes(state.currentStep) ? state.currentStep : 'quote');
        const activeIndex = steps.indexOf(current);

        stepNodes.forEach((node) => {
            const idx = steps.indexOf(node.dataset.step);
            node.classList.toggle('active', node.dataset.step === current);
            node.classList.toggle('done', idx > -1 && idx < activeIndex);
        });

        panels.forEach((panel) => {
            panel.classList.toggle('show', panel.dataset.stepPanel === current);
        });

        if (statusNode) {
            statusNode.textContent = `Durum: ${state.status}`;
        }

        if (state.finalizedOrder && finalizeSuccess && finalizeOrderNo && finalizeOrderMessage) {
            finalizeSuccess.hidden = false;
            finalizeOrderNo.textContent = `Sipariş No: ${state.finalizedOrder.order_no}`;
            finalizeOrderMessage.textContent = `Durum: ${state.finalizedOrder.state} / Ödeme: ${state.finalizedOrder.payment_state}`;
            syncTrackingLink();
            syncPaymentActionButton();
        }

        syncForms();
        syncSummary();
        syncPaymentDetail();
        syncPaymentActionButton();
    };

    const initiateCardPayment = async (messagePrefix = 'Sipariş oluştu.') => {
        if (!endpoints.payment_initiate) {
            throw new Error('Kart ödeme endpointi tanımlı değil.');
        }

        showAlert(finalizeFeedback, 'info', `${messagePrefix} Kart ödeme ekranına bağlanıyor...`);
        const { response, json } = await requestJson(endpoints.payment_initiate, 'POST', {});

        if (!response.ok || !json || json.success !== true) {
            throw new Error(json?.message || 'Kart ödeme başlatılamadı.');
        }

        state.paymentUrl = json.data.payment_url || '';
        state.finalizedOrder = json.data.order || state.finalizedOrder;
        render();

        if (!state.paymentUrl) {
            throw new Error('Ödeme sağlayıcısından yönlendirme linki dönmedi.');
        }

        showAlert(finalizeFeedback, 'success', `${messagePrefix} Kart ödeme ekranına yönlendiriliyorsunuz.`);
        window.location.href = state.paymentUrl;
    };

    const persistSession = async (patch) => {
        const { response, json } = await requestJson(endpoints.update, 'PATCH', patch);
        if (!response.ok || !json || json.success !== true) {
            throw new Error(json?.message || 'Checkout session güncellenemedi.');
        }
        state.status = json.data.status;
        state.currentStep = json.data.current_step;
        state.customerId = json.data.customer_id;
        state.payload = json.data.payload || {};
        normalizeCustomer();
        render();
    };

    document.querySelector('[data-action="continue-from-quote"]')?.addEventListener('click', async () => {
        try {
            await persistSession({ current_step: 'recipient', status: state.customerId ? 'authenticated' : state.status });
        } catch (error) {
            showAlert(finalizeFeedback, 'error', error.message);
        }
    });

    document.querySelectorAll('[data-step-back]').forEach((button) => {
        button.addEventListener('click', async () => {
            try {
                await persistSession({ current_step: button.dataset.stepBack });
            } catch (error) {
                showAlert(finalizeFeedback, 'error', error.message);
            }
        });
    });

    const syncAuthMode = () => {
        const mode = authModeInputs.find((input) => input.checked)?.value || 'register';
        registerOnlyNodes.forEach((node) => {
            node.hidden = mode !== 'register';
        });
        authPasswordGroups.forEach((node) => {
            node.hidden = mode === 'guest';
        });
        if (authSubmit) {
            authSubmit.textContent = mode === 'register'
                ? 'Hesap oluştur ve devam et'
                : (mode === 'login' ? 'Giriş yap ve devam et' : 'Misafir devam et');
        }
    };

    authModeInputs.forEach((input) => {
        input.addEventListener('change', syncAuthMode);
    });
    syncAuthMode();

    authForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors('auth');
        const mode = authModeInputs.find((input) => input.checked)?.value || 'register';
        const formData = new FormData(authForm);

        if (mode === 'guest') {
            const guestPhone = String(formData.get('phone') || '').trim();
            const guestName = String(formData.get('name') || '').trim();
            if (guestPhone.length < 10) {
                applyErrors('auth', { phone: ['Telefon zorunludur.'] });
                showAlert(authNote, 'error', 'Misafir devam için telefon zorunludur.');
                return;
            }

            try {
                const existingPickup = state.payload.pickup || {};
                await persistSession({
                    current_step: 'recipient',
                    payload: {
                        customer: {
                            name: guestName !== '' ? guestName : (existingPickup.name || ''),
                            phone: guestPhone,
                            email: '',
                            guest_checkout: true,
                        },
                    },
                });
                showAlert(authNote, 'success', 'Misafir checkout modu açıldı.');
            } catch (error) {
                showAlert(authNote, 'error', error.message);
            }

            return;
        }

        const payload = {
            phone: String(formData.get('phone') || '').trim(),
            password: String(formData.get('password') || '').trim(),
            device_name: 'checkout-web',
        };
        if (mode === 'register') {
            payload.name = String(formData.get('name') || '').trim();
            const email = String(formData.get('email') || '').trim();
            if (email !== '') {
                payload.email = email;
            }
        }

        try {
            const endpoint = mode === 'register' ? endpoints.register : endpoints.login;
            const { response, json } = await requestJson(endpoint, 'POST', payload);
            if (!response.ok || !json || json.success !== true) {
                if (response.status === 422) {
                    applyErrors('auth', json?.errors || {});
                }
                throw new Error(json?.message || 'Hesap işlemi tamamlanamadı.');
            }
            if (json.data?.token) {
                sessionStorage.setItem(`checkout_auth_${session.token}`, json.data.token);
            }
            state.customer = {
                id: json.data.user.id,
                name: json.data.user.name,
                phone: json.data.user.phone,
                email: json.data.user.email,
            };
            await persistSession({ customer_id: json.data.user.id, status: 'authenticated', current_step: 'recipient', payload: { customer: state.customer } });
            showAlert(authNote, 'success', 'Hesap checkout oturumuna bağlandı.');
        } catch (error) {
            showAlert(authNote, 'error', error.message);
        }
    });

    const syncSamePerson = () => {
        if (!samePersonInput?.checked) {
            return;
        }
        if (dropoffNameInput) {
            dropoffNameInput.value = pickupNameInput?.value || '';
        }
        if (dropoffPhoneInput) {
            dropoffPhoneInput.value = pickupPhoneInput?.value || '';
        }
    };

    samePersonInput?.addEventListener('change', syncSamePerson);
    pickupNameInput?.addEventListener('input', syncSamePerson);
    pickupPhoneInput?.addEventListener('input', syncSamePerson);
    fillPickupFromAccountButton?.addEventListener('click', () => {
        const customer = state.customer || {};
        setValue(pickupNameInput, customer.name || '');
        setValue(pickupPhoneInput, customer.phone || '');
        syncSamePerson();
    });

    recipientForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors('recipient');
        const formData = new FormData(recipientForm);
        const pickupAddress = String(formData.get('pickup_address') || '').trim();
        const dropoffAddress = String(formData.get('dropoff_address') || '').trim();
        const pickupPhone = String(formData.get('pickup_phone') || '').trim();
        const dropoffPhone = String(formData.get('dropoff_phone') || '').trim();
        const pickupName = String(formData.get('pickup_name') || '').trim();
        const dropoffName = String(formData.get('dropoff_name') || '').trim();
        const deliveryNotes = String(formData.get('delivery_notes') || '').trim();
        const samePerson = samePersonInput?.checked === true;
        const packageType = String(formData.get('package_type') || 'document').trim() || 'document';
        const packageQuantity = Math.max(parseInt(String(formData.get('package_quantity') || '1'), 10) || 1, 1);
        const packageWeight = parseInt(String(formData.get('package_weight_grams') || ''), 10);
        const packageValue = parseInt(String(formData.get('package_declared_value_amount') || ''), 10);
        const packageDescription = String(formData.get('package_description') || '').trim();
        const errors = {};

        if (pickupAddress.length < 5) {
            errors.pickup_address = ['Alış adresi en az 5 karakter olmalı.'];
        }
        if (dropoffAddress.length < 5) {
            errors.dropoff_address = ['Teslimat adresi en az 5 karakter olmalı.'];
        }
        if (pickupName.length < 2) {
            errors.pickup_name = ['Gönderen adı zorunludur.'];
        }
        if (dropoffName.length < 2) {
            errors.dropoff_name = ['Alıcı adı zorunludur.'];
        }
        if (pickupPhone.length < 10) {
            errors.pickup_phone = ['Gönderen telefonu zorunludur.'];
        }
        if (dropoffPhone.length < 10) {
            errors.dropoff_phone = ['Alıcı telefonu zorunludur.'];
        }
        if (Object.keys(errors).length > 0) {
            applyErrors('recipient', errors);
            return;
        }

        const existingPickup = state.payload.pickup || {};
        const existingDropoff = state.payload.dropoff || {};
        const existingCustomer = state.customer || {};
        const packages = [{ package_type: packageType, quantity: packageQuantity, description: packageDescription || null }];
        if (!Number.isNaN(packageWeight) && packageWeight >= 0) {
            packages[0].weight_grams = packageWeight;
        }
        if (!Number.isNaN(packageValue) && packageValue >= 0) {
            packages[0].declared_value_amount = packageValue;
        }

        try {
            await persistSession({
                current_step: 'payment',
                payload: {
                    customer: {
                        name: existingCustomer.name || pickupName,
                        phone: existingCustomer.phone || pickupPhone,
                        email: existingCustomer.email || '',
                        guest_checkout: !state.customerId,
                    },
                    same_person: samePerson,
                    pickup: { ...existingPickup, name: pickupName, phone: pickupPhone, address: pickupAddress },
                    dropoff: { ...existingDropoff, name: dropoffName, phone: dropoffPhone, address: dropoffAddress },
                    packages,
                    notes: { ...(state.payload.notes || {}), delivery_notes: deliveryNotes },
                },
            });
        } catch (error) {
            showAlert(finalizeFeedback, 'error', error.message);
        }
    });

    document.querySelectorAll('input[name="payment_method"]').forEach((node) => {
        node.addEventListener('change', syncPaymentDetail);
    });

    document.querySelector('[data-payment-save]')?.addEventListener('click', async () => {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value || '';
        if (selectedMethod === '') {
            showAlert(paymentHelpNode, 'error', 'Bir ödeme yöntemi seçin.');
            return;
        }
        if (selectedMethod === 'card' && !paymentState.card_ready) {
            showAlert(paymentHelpNode, 'warn', 'Kart ödeme sağlayıcısı henüz aktif değil.');
            return;
        }

        const paymentPayload = selectedMethod === 'cash'
            ? { method: 'cash', timing: 'delivery', payer_role: 'recipient' }
            : selectedMethod === 'bank_transfer'
                ? { method: 'bank_transfer', timing: 'prepaid', payer_role: 'sender' }
                : { method: 'card', timing: 'prepaid', payer_role: 'sender' };

        try {
            await persistSession({ current_step: 'confirm', status: 'ready', payload: { payment: paymentPayload } });
            showAlert(paymentHelpNode, 'success', 'Ödeme seçimi kaydedildi.');
        } catch (error) {
            showAlert(paymentHelpNode, 'error', error.message);
        }
    });

    finalizePaymentButton?.addEventListener('click', async () => {
        try {
            await initiateCardPayment('Sipariş ödeme bekliyor.');
        } catch (error) {
            showAlert(finalizeFeedback, 'error', error.message);
        }
    });

    document.querySelector('[data-finalize-submit]')?.addEventListener('click', async () => {
        try {
            showAlert(finalizeFeedback, 'info', 'Sipariş oluşturuluyor...');
            const { response, json } = await requestJson(endpoints.finalize, 'POST', { customer_id: state.customerId });
            if (!response.ok || !json || json.success !== true) {
                throw new Error(json?.message || 'Sipariş finalize edilemedi.');
            }
            state.status = json.data.checkout_session.status;
            state.currentStep = json.data.checkout_session.current_step;
            state.customerId = json.data.checkout_session.customer_id || state.customerId;
            state.payload = json.data.checkout_session.payload || state.payload;
            state.finalizedOrder = json.data.order || null;
            state.paymentUrl = '';

            let message = `Sipariş oluştu. Durum: ${json.data.order.state} / Ödeme: ${json.data.order.payment_state}`;
            if (json.data.next_action === 'await_bank_transfer_reconcile') {
                message += ' | Havale bildirimi sonrasında admin reconcile beklenir.';
            } else if (json.data.next_action === 'dispatch_ready') {
                message += ' | Sipariş dispatch akışına hazır.';
            } else if (json.data.next_action === 'initiate_card_payment') {
                message += ' | Kart ödeme sağlayıcısı bağlanıyor.';
            }

            render();

            if (json.data.next_action === 'initiate_card_payment' && paymentState.card_ready) {
                await initiateCardPayment(message);
                return;
            }

            showAlert(finalizeFeedback, 'success', message);
        } catch (error) {
            showAlert(finalizeFeedback, 'error', error.message);
        }
    });

    render();
});
</script>
@endpush
</x-checkout::layouts.public>
