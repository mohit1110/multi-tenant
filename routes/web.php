<?php

use App\Http\Controllers\Auth\TenantAuthController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\TaskController;
use App\Http\Middleware\AuthenticateTenant;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to tenant login
Route::get('/', function () {
    return redirect()->route('tenant.login');
});

/*
|--------------------------------------------------------------------------
| Tenant Authentication Routes (no auth required)
|--------------------------------------------------------------------------
*/
Route::prefix('')->name('tenant.')->group(function () {
    Route::get('/login', [TenantAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [TenantAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [TenantAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [TenantAuthController::class, 'register'])->name('register.post');
    Route::get('/suspended', fn() => view('tenant.suspended'))->name('suspended');
});

/*
|--------------------------------------------------------------------------
| Tenant Application Routes (auth required)
|--------------------------------------------------------------------------
*/
Route::prefix('app')->name('tenant.')->middleware([
    IdentifyTenant::class,
    AuthenticateTenant::class,
    EnsureTenantIsActive::class,
])->group(function () {

    // Logout
    Route::any('/logout', [TenantAuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tasks CRUD
    Route::resource('tasks', TaskController::class)->names([
        'index'   => 'tasks.index',
        'create'  => 'tasks.create',
        'store'   => 'tasks.store',
        'show'    => 'tasks.show',
        'edit'    => 'tasks.edit',
        'update'  => 'tasks.update',
        'destroy' => 'tasks.destroy',
    ]);
});
