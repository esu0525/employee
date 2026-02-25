<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmployeeController;

Route::get('/', [EmployeeController::class , 'index'])->name('employees.index');
Route::get('/employee-details', [EmployeeController::class , 'show'])->name('employees.show');
Route::post('/add-employee', [EmployeeController::class , 'store'])->name('employees.store');
Route::get('/history', [EmployeeController::class , 'history'])->name('employees.history');
Route::get('/history-inactive', [EmployeeController::class , 'historyInactive'])->name('employees.history-inactive');
Route::get('/history-resign', [EmployeeController::class , 'historyResign'])->name('employees.history-resign');
Route::get('/history-retired', [EmployeeController::class , 'historyRetired'])->name('employees.history-retired');
Route::get('/history-transfer', [EmployeeController::class , 'historyTransfer'])->name('employees.history-transfer');
Route::get('/requests', [EmployeeController::class , 'requests'])->name('employees.requests');
Route::post('/employee-details/upload/{id}', [EmployeeController::class , 'upload'])->name('employees.upload');
Route::delete('/employee-details/delete-doc/{id}', [EmployeeController::class , 'deleteDoc'])->name('employees.delete-doc');
Route::post('/requests/{id}/approve', [EmployeeController::class , 'approveRequest'])->name('requests.approve');
Route::post('/requests/{id}/reject', [EmployeeController::class , 'rejectRequest'])->name('requests.reject');
