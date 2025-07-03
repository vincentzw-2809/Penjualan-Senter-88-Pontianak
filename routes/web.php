<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\CheckoutController;

// Log viewer (admin only)
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware('admin');

// Halaman utama dan toko
Route::get('/', 'WelcomePageController@index')->name('welcome');
Route::get('/shop', 'ShopController@index')->name('shop.index');
Route::get('/shop/{product}', 'ShopController@show')->name('shop.show');
Route::get('/shop/search/{query}', 'ShopController@search')->name('shop.search');

// 🛒 Keranjang belanja
Route::get('/cart', 'CartController@index')->name('cart.index');
Route::post('/cart', 'CartController@store')->name('cart.store');
Route::delete('/cart/{product}/{cart}', 'CartController@destroy')->name('cart.destroy');
Route::post('/cart/save-later/{product}', 'CartController@saveLater')->name('cart.save-later');
Route::post('/cart/add-to-cart/{product}', 'CartController@addToCart')->name('cart.add-to-cart');
Route::patch('/cart/{product}', 'CartController@update')->name('cart.update');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/shipping-cost', [CheckoutController::class, 'getShippingCost']);


// 💳 Checkout (Mock untuk pengganti Midtrans)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/shipping-cost', [CheckoutController::class, 'getShippingCost']);
Route::get('/checkout/search-city', [CheckoutController::class, 'searchCity']);
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// ✅ Route Mock Payment (pengganti Midtrans)
Route::post('/checkout/mock-pay', [CheckoutController::class, 'mockPay'])->name('checkout.mock');

// ✅ Halaman Terima Kasih
Route::get('/thankyou', function () {
    return view('thankyou');
})->name('checkout.thankyou');

// 🎟️ Voucher/Kupon
Route::post('/coupon', 'CouponsController@store')->name('coupon.store');
Route::delete('/coupon', 'CouponsController@destroy')->name('coupon.destroy');

// 🔐 Autentikasi
Auth::routes();
Route::get('/login/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('/login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/home', 'HomeController@index')->name('home');

//laporan
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('voyager.reports.index');
    Route::get('/reports/pdf', [App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('voyager.reports.pdf');
});


// 🛠️ Admin Panel Voyager
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('/country_visits', 'VisitsController@index')->name('voyager.visits');
});
