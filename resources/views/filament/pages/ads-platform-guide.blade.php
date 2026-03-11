<x-filament-panels::page>
    <div class="space-y-6">
        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Hizli Baslangic</h2>
            <ol class="mt-3 list-decimal space-y-1 pl-5 text-sm text-gray-700 dark:text-gray-300">
                <li>Baglantilar ekraninda platform baglantisi olustur.</li>
                <li>Kampanyalar ekraninda baglanti secip kampanya olustur.</li>
                <li>Donusumler ekraninda event durumlarini takip et.</li>
                <li>Sorun halinde health-check ve log kontrolu yap.</li>
            </ol>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Panel Kisayollari</h2>
            <div class="mt-3 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('filament.admin.resources.ad-connections.index') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Baglantilar
                </a>
                <a href="{{ route('filament.admin.resources.ad-campaigns.index') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Kampanyalar
                </a>
                <a href="{{ route('filament.admin.resources.ad-conversions.index') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Donusumler
                </a>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Rol ve Yetki Ozeti</h2>
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
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Operasyonel Kontrol</h2>
            <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-gray-700 dark:text-gray-300">
                <li><span class="font-medium">Health:</span> <code>php artisan ads:health-check --hours=48</code></li>
                <li><span class="font-medium">Log:</span> <code>storage/logs/laravel.log</code></li>
                <li><span class="font-medium">Cache reset:</span> <code>php artisan optimize:clear</code> sonra <code>php artisan optimize</code></li>
            </ul>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Referans Dokumanlar</h2>
            <ul class="mt-3 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                <li><code>docs/ops/ads-admin-calisma-bilgilendirmesi-2026-03-11.md</code></li>
                <li><code>docs/ops/ads-release-governance-checklist-2026-02-28.md</code></li>
                <li><code>docs/SPRINT6-RUNBOOK.md</code></li>
            </ul>
        </section>
    </div>
</x-filament-panels::page>

