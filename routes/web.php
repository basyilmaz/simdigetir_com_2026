<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\LegalDocumentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\SitemapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Backoffice auth fallback for middleware that expects named login route.
Route::redirect('/login', '/admin/login')->name('login');

// Landing Pages
Route::get('/', fn () => view('landing.home'))->name('home');
Route::get('/hakkimizda', fn () => view('landing.about'))->name('about');
Route::get('/hizmetler', fn () => view('landing.services'))->name('services');
Route::get('/kurumsal', fn () => view('landing.corporate'))->name('corporate');
Route::get('/kurye-basvuru', fn () => view('landing.courier-apply'))->name('courier-apply');
Route::get('/iletisim', fn () => view('landing.contact'))->name('contact');
Route::get('/sss', fn () => view('landing.faq'))->name('faq');
Route::get('/kvkk', fn () => view('landing.kvkk'))->name('kvkk');
Route::get('/cerez-politikasi', fn (LegalDocumentController $controller) => $controller->show('cerez-politikasi'))->name('cookies');
Route::get('/kullanim-kosullari', fn (LegalDocumentController $controller) => $controller->show('kullanim-kosullari'))->name('terms');

// İlçe / Mahalle Lokasyon Sayfaları (SEO)
Route::get('/kurye', [LocationController::class, 'allDistricts'])->name('locations.index');
Route::get('/kurye/{district}', [LocationController::class, 'district'])->name('locations.district');
Route::get('/kurye/{district}/{neighborhood}', [LocationController::class, 'neighborhood'])->name('locations.neighborhood');

// Basic panel UIs
Route::get('/kurye-panel', [PanelController::class, 'courierPanel'])->name('panel.courier.simple');
Route::get('/musteri-panel', [PanelController::class, 'customerPanel'])->name('panel.customer.simple');
Route::get('/panel/courier/{courier}', [PanelController::class, 'courierDashboard'])->name('panel.courier');
Route::get('/panel/customer/{user}', [PanelController::class, 'customerDashboard'])->name('panel.customer');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Dynamic form submissions
Route::post('/api/forms/{key}/submit', [FormSubmissionController::class, 'submit'])->name('forms.submit');
