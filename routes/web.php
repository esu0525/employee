<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Public Portal Routes
Route::get('/portal', [PortalController::class, 'index'])->name('portal.index');
Route::get('/portal/view/{id}', [PortalController::class, 'view'])->name('portal.view');
Route::post('/portal/submit', [PortalController::class, 'submit'])->name('portal.submit');

Route::get('/dashboard', [EmployeeController::class , 'index'])->name('employees.index');
Route::get('/masterlist', [EmployeeController::class, 'masterlist'])->name('employees.masterlist');
Route::post('/masterlist/import', [EmployeeController::class, 'import'])->name('employees.import');
Route::get('/employee-details', [EmployeeController::class , 'show'])->name('employees.show');
Route::post('/employee-details/{id}/update', [EmployeeController::class, 'update'])->name('employees.details-update');
Route::post('/employee-details/{id}/update-avatar', [EmployeeController::class, 'updateAvatar'])->name('employees.update-avatar');
Route::post('/add-employee', [EmployeeController::class , 'store'])->name('employees.store');
Route::post('/employee/update-status/{id}', [EmployeeController::class, 'updateStatus'])->name('employees.update-status');
Route::get('/history', [EmployeeController::class , 'history'])->name('employees.history');
Route::get('/requests', [EmployeeController::class , 'requests'])->name('employees.requests');
Route::post('/employee-details/upload/{id}', [EmployeeController::class , 'upload'])->name('employees.upload');
Route::delete('/employee-details/delete-doc/{id}', [EmployeeController::class , 'deleteDoc'])->name('employees.delete-doc');
Route::post('/requests/{id}/approve', [EmployeeController::class , 'approveRequest'])->name('requests.approve');
Route::post('/requests/{id}/reject', [EmployeeController::class , 'rejectRequest'])->name('requests.reject');
Route::post('/submit-request', [EmployeeController::class , 'submitRequest'])->name('requests.store');
Route::get('/approved-list', [EmployeeController::class , 'approvedList'])->name('employees.approved-list');

// Account Management
Route::get('/accounts', [AdminUserController::class, 'index'])->name('admin.users.index');
Route::post('/accounts', [AdminUserController::class, 'store'])->name('admin.users.store');
Route::post('/accounts/{id}/update', [AdminUserController::class, 'update'])->name('admin.users.update');
Route::delete('/accounts/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

Route::get('/logout', function () {
    return redirect()->route('login');
})->name('logout');
