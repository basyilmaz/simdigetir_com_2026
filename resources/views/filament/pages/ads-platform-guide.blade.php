<x-filament-panels::page>
    <div class="space-y-6">
        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">Onboarding</p>
            <h2 class="mt-2 text-base font-semibold text-gray-900 dark:text-gray-100">Reklam paneli ne icin kullanilir?</h2>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-700 dark:text-gray-300">
                Bu alan kampanya baglantilarini, donusum sinyallerini ve yayin operasyonunu ayni yerden takip etmek icin tasarlandi.
                Amaç, operatorun "hangi kampanya aktif, hangi baglanti calisiyor, donusum geliyor mu?" sorularina hizli cevap vermesi.
            </p>
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <article class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">1. Baglantilar</h2>
                <p class="mt-3 text-sm leading-6 text-gray-700 dark:text-gray-300">
                    Meta veya Google baglantisinin aktif, dogrulanmis ve dogru hesaba bagli oldugundan emin olun.
                </p>
                <a href="{{ route('filament.admin.resources.ad-connections.index') }}" class="mt-4 inline-flex rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Baglantilara git
                </a>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">2. Kampanyalar</h2>
                <p class="mt-3 text-sm leading-6 text-gray-700 dark:text-gray-300">
                    Hangi kaynagin hangi landing sayfaya trafik verdigini ve yayina alinacak kampanyalari burada yonetin.
                </p>
                <a href="{{ route('filament.admin.resources.ad-campaigns.index') }}" class="mt-4 inline-flex rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Kampanyalara git
                </a>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">3. Donusumler</h2>
                <p class="mt-3 text-sm leading-6 text-gray-700 dark:text-gray-300">
                    Event durumlarini, gecikmeleri ve platforma ulasan donusumleri bu ekrandan kontrol edin.
                </p>
                <a href="{{ route('filament.admin.resources.ad-conversions.index') }}" class="mt-4 inline-flex rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Donusumlere git
                </a>
            </article>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Gunluk kontrol listesi</h2>
            <ul class="mt-3 space-y-2 text-sm leading-6 text-gray-700 dark:text-gray-300">
                <li>Yeni kampanya acilmadan once dogru baglanti ve landing secildigini kontrol edin.</li>
                <li>Donusum ekraninda son 24 saate ait event akisinda beklenmeyen kopma olup olmadigina bakin.</li>
                <li>Yayinda olan kampanyalarda etiket, kaynak ve platform eslesmesini dogrulayin.</li>
                <li>Sorun varsa once health-check, sonra log ve cache adimlarini uygulayin.</li>
            </ul>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Hangi durumda nereye bakilir?</h2>
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-2 py-2 font-medium">Durum</th>
                            <th class="px-2 py-2 font-medium">Ilk kontrol</th>
                            <th class="px-2 py-2 font-medium">Ikinci adim</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-2 py-2">Yeni kampanya baglanmiyor</td>
                            <td class="px-2 py-2">Baglantilar ekraninda hesap ve token durumu</td>
                            <td class="px-2 py-2">Kampanya hedefi ve landing eslesmesi</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-2 py-2">Donusum gelmiyor</td>
                            <td class="px-2 py-2">Donusumler ekraninda event satirlari</td>
                            <td class="px-2 py-2">Health-check ve uygulama loglari</td>
                        </tr>
                        <tr>
                            <td class="px-2 py-2">Veri gecikmeli geliyor</td>
                            <td class="px-2 py-2">Son sync zamani ve platform durumu</td>
                            <td class="px-2 py-2">Cache temizligi ve yeniden deneme</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Rol ve yetki ozeti</h2>
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-2 py-2 font-medium">Rol</th>
                            <th class="px-2 py-2 font-medium">Ads Yetki Kapsami</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-2 py-2">super-admin</td>
                            <td class="px-2 py-2">Tum ads yetkileri</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-2 py-2">admin</td>
                            <td class="px-2 py-2">ads.view, ads.manage, ads.publish, ads.report</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-2 py-2">operations</td>
                            <td class="px-2 py-2">ads.view</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-2 py-2">support</td>
                            <td class="px-2 py-2">ads.view</td>
                        </tr>
                        <tr>
                            <td class="px-2 py-2">finance</td>
                            <td class="px-2 py-2">ads.report</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Operasyonel yardim</h2>
            <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-gray-700 dark:text-gray-300">
                <li><span class="font-medium">Health:</span> <code>php artisan ads:health-check --hours=48</code></li>
                <li><span class="font-medium">Log:</span> <code>storage/logs/laravel.log</code></li>
                <li><span class="font-medium">Cache reset:</span> <code>php artisan optimize:clear</code> sonra <code>php artisan optimize</code></li>
            </ul>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Derin dokumanlar</h2>
            <ul class="mt-3 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                <li><code>docs/ops/ads-admin-calisma-bilgilendirmesi-2026-03-11.md</code></li>
                <li><code>docs/ops/ads-release-governance-checklist-2026-02-28.md</code></li>
                <li><code>docs/SPRINT6-RUNBOOK.md</code></li>
            </ul>
        </section>
    </div>
</x-filament-panels::page>
