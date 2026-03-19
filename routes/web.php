<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AuthMiddleware;

// ─── Auth Routes (public) ──────────────────────────────────────────────────
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// OTP
Route::get('/otp', [AuthController::class, 'showOtp'])->name('auth.otp');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');
Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('auth.otp.resend');

// ─── Public Portal Routes ──────────────────────────────────────────────────
Route::get('/portal', [PortalController::class, 'index'])->name('portal.index');
Route::get('/portal/view/{id}', [PortalController::class, 'view'])->name('portal.view');
Route::post('/portal/submit', [PortalController::class, 'submit'])->name('portal.submit');

// ─── Protected Routes (require login + OTP) ────────────────────────────────
Route::middleware([AuthMiddleware::class])->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/add-employee', [EmployeeController::class, 'addEmployee'])->name('employees.add');
    Route::get('/masterlist', [EmployeeController::class, 'masterlist'])->name('employees.masterlist');
    Route::post('/masterlist/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/employee-details', [EmployeeController::class, 'show'])->name('employees.show');
    Route::post('/employee-details/{id}/update', [EmployeeController::class, 'update'])->name('employees.details-update');
    Route::post('/employee-details/{id}/update-avatar', [EmployeeController::class, 'updateAvatar'])->name('employees.update-avatar');
    Route::post('/add-employee', [EmployeeController::class, 'store'])->name('employees.store');
    Route::post('/employee/update-status/{id}', [EmployeeController::class, 'updateStatus'])->name('employees.update-status');
    Route::get('/archive', [EmployeeController::class, 'archive'])->name('employees.archive');
    Route::get('/history', function() { return redirect()->route('employees.archive'); });
    Route::get('/requests', [EmployeeController::class, 'requests'])->name('employees.requests');
    Route::post('/employee-details/upload/{id}', [EmployeeController::class, 'upload'])->name('employees.upload');
    Route::delete('/employee-details/delete-doc/{id}', [EmployeeController::class, 'deleteDoc'])->name('employees.delete-doc');
    Route::post('/requests/{id}/approve', [EmployeeController::class, 'approveRequest'])->name('requests.approve');
    Route::post('/requests/{id}/reject', [EmployeeController::class, 'rejectRequest'])->name('requests.reject');
    Route::post('/submit-request', [EmployeeController::class, 'submitRequest'])->name('requests.store');
    Route::get('/approved-list', [EmployeeController::class, 'approvedList'])->name('employees.approved-list');

    // Account Management
    Route::get('/accounts', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/accounts', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::post('/accounts/{id}/update', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/accounts/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/check-password', [ProfileController::class, 'checkPassword'])->name('profile.check-password');
});
