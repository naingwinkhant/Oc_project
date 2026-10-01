<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/catalog')->name('home');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Area landing pages
|--------------------------------------------------------------------------
| /staff and /admin are the two front doors. A visitor who is not signed in is
| sent to the sign-in page for that area rather than the generic one, so the
| page they land on already belongs to the right door. Somebody from the team
| goes straight through to the dashboard.
*/

Route::get('/staff', function (Request $request) {
    if ($request->user()?->isStaff()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('staff.login.php');
})->name('staff.home');

/*
|--------------------------------------------------------------------------
| Create Account
|--------------------------------------------------------------------------
| Open to anyone who does not already have an account. A new registration is
| always a staff account waiting to be accepted, so it cannot reach anything
| until an administrator or manager approves it.
*/

Route::middleware('guest')->group(function () {
    Route::get('/create-account', [RegisterController::class, 'create'])->name('register');
    Route::post('/create-account', [RegisterController::class, 'store'])->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Role-specific sign-in doors
|--------------------------------------------------------------------------
| Two separate front doors, so the admin area and the staff area each have
| their own page. Both end up at the same session-backed dashboard; what
| differs is the role a credential is accepted for. A wrong-role sign-in is
| refused here rather than being allowed in and bounced later.
*/

Route::prefix('admin')->name('admin.')->middleware('guest')->group(function () {
    Route::get('/login.php', [AuthenticatedSessionController::class, 'createAdmin'])->name('login.php');
    Route::post('/login.php', [AuthenticatedSessionController::class, 'storeAdmin'])->name('login.php.store');
});

Route::prefix('staff')->name('staff.')->middleware('guest')->group(function () {
    Route::get('/login.php', [AuthenticatedSessionController::class, 'createStaff'])->name('login.php');
    Route::post('/login.php', [AuthenticatedSessionController::class, 'storeStaff'])->name('login.php.store');
});

/*
|--------------------------------------------------------------------------
| Choosing a password
|--------------------------------------------------------------------------
| An account is created with an email, a username and a role only. This is the
| next step, where the person it belongs to sets their own password from a
| one-time link. Open to anyone holding a valid link, so it is not behind auth.
*/

Route::middleware('guest')->group(function () {
    Route::get('/set-password', [SetPasswordController::class, 'create'])->name('set-password');
    Route::post('/set-password', [SetPasswordController::class, 'store'])->name('set-password.store');
});

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

/*
|--------------------------------------------------------------------------
| Customer information
|--------------------------------------------------------------------------
| Services and Information are open to everyone: they are the pages a shopper
| reads before deciding to order, so neither needs an account.
*/

Route::get('/services', [StoreController::class, 'services'])->name('services');
Route::get('/information', [StoreController::class, 'information'])->name('information');

/*
|--------------------------------------------------------------------------
| Settings & history
|--------------------------------------------------------------------------
*/

Route::get('/settings', [SettingsController::class, 'show'])->name('settings');
Route::post('/settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.theme');

Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
Route::delete('/history', [HistoryController::class, 'clear'])->name('history.clear');

/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
| Store notices, shown in the bell in the header and cleared per shopper.
*/

Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::post('/{notice}/dismiss', [NotificationController::class, 'dismiss'])->name('dismiss');
    Route::delete('/', [NotificationController::class, 'dismissAll'])->name('dismiss-all');
});

/*
|--------------------------------------------------------------------------
| Team alerts
|--------------------------------------------------------------------------
| Raised by the shop itself — a new order, for instance — and cleared by each
| member of staff individually. Sign-in is required: these are never shown to a
| shopper.
*/

Route::middleware('auth')->group(function () {
    Route::post('/team-alerts/{alert}/dismiss', [NotificationController::class, 'dismissAlert'])
        ->name('team-alerts.dismiss');
});

/*
|--------------------------------------------------------------------------
| Customer page
|--------------------------------------------------------------------------
| Where a customer lands after signing in. Ordering itself never needs an
| account; this is for looking at what they have already bought.
*/

Route::get('/account', [LandingController::class, 'account'])
    ->middleware(['auth', 'active', 'role:customer'])
    ->name('account.home');

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

// The admin area is for the team only. A customer's page is /account, so a
// customer reaching in here is turned away before any controller runs.
Route::middleware(['auth', 'active', 'role:admin,manager,staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Each role lands on its own page. The role is declared here rather than
        // only checked inside the controller, so anything that reads a route's
        // middleware — the redirect after sign-in, for one — can tell that these
        // are not four names for the same dashboard.
        Route::get('manager', [LandingController::class, 'manager'])
            ->middleware('role:manager')
            ->name('manager.home');
        Route::get('staff', [LandingController::class, 'staff'])
            ->middleware('role:staff')
            ->name('staff.home');

        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('stock', [StockController::class, 'index'])->name('stock.index');
        Route::get('stock/low', [StockController::class, 'low'])->name('stock.low');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        // Notices are read-only for staff: they can see what is live, but only a
        // manager can write or retire one.
        Route::get('notices', [NoticeController::class, 'index'])->name('notices.index');

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
            Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
            Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
            Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

            Route::resource('categories', CategoryController::class)->except('show');
            Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
            Route::post('categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');

            Route::resource('suppliers', SupplierController::class)->except('show');

            // Only the write routes: index is registered above, outside the role
            // group, so staff can read the list without being able to write.
            Route::resource('notices', NoticeController::class)
                ->only(['create', 'store', 'edit', 'update', 'destroy']);

            Route::post('stock', [StockController::class, 'store'])->name('stock.store');

            // Accepting an account is what lets it sign in, so an administrator or a
            // manager can do it.
            Route::patch('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
            Route::patch('users/{user}/revoke-approval', [UserController::class, 'revokeApproval'])->name('users.revoke');

            Route::get('activity', [ActivityController::class, 'index'])->name('activity.index');

            // Accepting or turning down a registration. Both roles share the queue.
            Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
            Route::patch('approvals/{user}/accept', [ApprovalController::class, 'accept'])->name('approvals.accept');
            Route::patch('approvals/{user}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
        });

        Route::middleware('role:admin')->group(function () {
            // Staff accounts only. There are no customer accounts: shoppers order
            // as guests and never appear in this table.
            Route::resource('users', UserController::class)->except('show');
        });
    });
