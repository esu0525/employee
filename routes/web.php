<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\ArchiveReportController;
use App\Http\Middleware\AuthMiddleware;

// ─── Landing Page ──────────────────────────────────────────────────────────
Route::get('/', function() {
    return view('landing');
})->name('landing');

// ─── Static Pages ──────────────────────────────────────────────────────────
Route::get('/legal/privacy', function() { return view('info', ['section' => 'privacy', 'title' => 'Privacy Policy']); })->name('legal.privacy');
Route::get('/legal/terms', function() { return view('info', ['section' => 'terms', 'title' => 'Terms of Service']); })->name('legal.terms');
Route::get('/legal/data-protection', function() { return view('info', ['section' => 'data_protection', 'title' => 'Data Protection']); })->name('legal.data-protection');
Route::get('/support/contact', function() { return view('info', ['section' => 'contact', 'title' => 'Contact Us']); })->name('support.contact');
Route::get('/support/faq', function() { return view('info', ['section' => 'faq', 'title' => 'Frequently Asked Questions']); })->name('support.faq');
Route::get('/support/documentation', function() { return view('info', ['section' => 'documentation', 'title' => 'System Documentation']); })->name('support.documentation');

// ─── Auth Routes (public) ──────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// OTP
Route::get('/otp', [AuthController::class, 'showOtp'])->name('auth.otp');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');
Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('auth.otp.resend');

// Password Reset
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

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
    Route::get('/masterlist/json', [EmployeeController::class, 'allEmployeesJson'])->name('employees.export.json');
    Route::post('/masterlist/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/employee-details', [EmployeeController::class, 'show'])->name('employees.show');
    Route::post('/employee-details/{id}/update', [EmployeeController::class, 'update'])->name('employees.details-update');
    Route::post('/employee-details/{id}/update-avatar', [EmployeeController::class, 'updateAvatar'])->name('employees.update-avatar');
    Route::post('/add-employee', [EmployeeController::class, 'store'])->name('employees.store');
    Route::post('/employee/update-status/{id}', [EmployeeController::class, 'updateStatus'])->name('employees.update-status');
    Route::get('/archive', [EmployeeController::class, 'archive'])->name('employees.archive');
    Route::get('/archive/json', [EmployeeController::class, 'archiveEmployeesJson'])->name('employees.archive.export.json');
    Route::get('/history', function() { return redirect()->route('employees.archive'); });
    
    // Archive Reports
    Route::get('/archive/reports', [ArchiveReportController::class, 'index'])->name('archive.reports.index');
    Route::get('/archive/reports/{id}/download', [ArchiveReportController::class, 'download'])->name('archive.reports.download');
    Route::post('/archive/reports', [ArchiveReportController::class, 'store'])->name('archive.reports.store');
    Route::delete('/archive/reports/{id}', [ArchiveReportController::class, 'destroy'])->name('archive.reports.destroy');
    Route::get('/archive/reported-employee-ids', [ArchiveReportController::class, 'reportedEmployeeIds'])->name('archive.reported-ids');
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
    
    // Audit Trail
    Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('admin.audit-trail');
    Route::get('/audit-trail/filter', [AuditTrailController::class, 'filter'])->name('admin.audit-trail.filter');
    Route::post('/audit-trail/cleanup', [AuditTrailController::class, 'cleanup'])->name('admin.audit-trail.cleanup');
    Route::post('/accounts/{id}/update', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/accounts/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/accounts/{id}/profile', [ProfileController::class, 'show'])->name('admin.users.profile');
    Route::post('/accounts/{id}/profile', [ProfileController::class, 'updateAdmin'])->name('admin.users.update-from-profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/check-password', [ProfileController::class, 'checkPassword'])->name('profile.check-password');

    // File Display Routes
    Route::get('/display-file/employee-avatar/{id}', [FileController::class, 'showEmployeeAvatar'])->name('display.employee-avatar');
    Route::get('/display-file/user-avatar/{id}', [FileController::class, 'showUserAvatar'])->name('display.user-avatar');
    Route::get('/display-file/document/{id}', [FileController::class, 'showDocument'])->name('display.document');
    Route::get('/display-file/request-file/{id}', [FileController::class, 'showRequestFile'])->name('display.request-file');
});

Route::get('/profile/verify-email/{token}', [ProfileController::class, 'verifyEmail'])->name('profile.verify-email');

