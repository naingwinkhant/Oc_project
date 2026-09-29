<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/catalog')->name('home');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/register', [AuthenticatedSessionController::class, 'createAccount'])->name('register')->middleware('guest');
Route::post('/register', [AuthenticatedSessionController::class, 'storeAccount'])->middleware('guest');

/*
|--------------------------------------------------------------------------
| Cart & checkout
|--------------------------------------------------------------------------
*/

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'store'])->name('store');
    Route::patch('/', [CartController::class, 'update'])->name('update');
    Route::delete('/{product}', [CartController::class, 'destroy'])->whereNumber('product')->name('destroy');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'create'])->name('create');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/{order}', [CheckoutController::class, 'show'])->name('show');
    Route::post('/{order}/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
});

/*
|--------------------------------------------------------------------------
| Payments
|--------------------------------------------------------------------------
| The callback is a server-to-server notification signed by the provider, so it
| is exempt from CSRF in bootstrap/app.php.
*/

Route::prefix('payments')->name('payments.')->group(function () {
    Route::post('/callback/{gateway}', [PaymentController::class, 'callback'])->name('callback');
    Route::get('/return/{gateway}', [PaymentController::class, 'return'])->name('return');
    Route::get('/sandbox/{order}', [PaymentController::class, 'sandbox'])->name('sandbox');
    Route::post('/sandbox/{order}/settle', [PaymentController::class, 'settle'])->name('sandbox.settle');
});

/*
|--------------------------------------------------------------------------
| Public catalogue
|--------------------------------------------------------------------------
*/

Route::prefix('favourites')->name('favourites.')->group(function () {
    Route::get('/', [FavouriteController::class, 'index'])->name('index');
    Route::post('/toggle', [FavouriteController::class, 'toggle'])->name('toggle');
    Route::delete('/', [FavouriteController::class, 'clear'])->name('clear');
});

Route::prefix('catalog')->name('catalog.')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('index');
    Route::get('/new-arrivals', [CatalogController::class, 'newArrivals'])->name('new-arrivals');
    Route::get('/goods/{product:slug}', [CatalogController::class, 'product'])->name('product');
    Route::get('/{category:slug}', [CatalogController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/low', [StockController::class, 'low'])->name('stock.low');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('products', ProductController::class)
            ->except(['show', 'index'])
            ->names([
                'create' => 'products.create',
                'store' => 'products.store',
                'edit' => 'products.edit',
                'update' => 'products.update',
                'destroy' => 'products.destroy',
            ]);

        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

        Route::resource('categories', CategoryController::class)->except('show');
        Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
        Route::post('categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');

        Route::resource('suppliers', SupplierController::class)->except('show');

        Route::post('stock', [StockController::class, 'store'])->name('stock.store');

        Route::get('activity', [ActivityController::class, 'index'])->name('activity.index');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });
});
