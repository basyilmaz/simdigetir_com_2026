<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SitemapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Pages
Route::get('/', fn () => view('landing.home'))->name('home');
Route::get('/hakkimizda', fn () => view('landing.about'))->name('about');
Route::get('/hizmetler', fn () => view('landing.services'))->name('services');
Route::get('/kurumsal', fn () => view('landing.corporate'))->name('corporate');
Route::get('/kurye-basvuru', fn () => view('landing.courier-apply'))->name('courier-apply');
Route::get('/iletisim', fn () => view('landing.contact'))->name('contact');
Route::get('/sss', fn () => view('landing.faq'))->name('faq');
Route::get('/kvkk', fn () => view('landing.kvkk'))->name('kvkk');

// İlçe / Mahalle Lokasyon Sayfaları (SEO)
Route::get('/kurye', [LocationController::class, 'allDistricts'])->name('locations.index');
Route::get('/kurye/{district}', [LocationController::class, 'district'])->name('locations.district');
Route::get('/kurye/{district}/{neighborhood}', [LocationController::class, 'neighborhood'])->name('locations.neighborhood');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

