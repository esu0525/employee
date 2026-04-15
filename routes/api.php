<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ArchiveReportController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['web'])->group(function () {
    // Employee Data Routes
    Route::get('/masterlist/json', [EmployeeController::class, 'allEmployeesJson'])->name('api.employees.export.json');
    Route::get('/archive/json', [EmployeeController::class, 'archiveEmployeesJson'])->name('api.employees.archive.export.json');
    Route::get('/archive/reported-employee-ids', [ArchiveReportController::class, 'reportedEmployeeIds'])->name('api.archive.reported-ids');

    // Profile/Security Data
    Route::post('/profile/check-password', [ProfileController::class, 'checkPassword'])->name('api.profile.check-password');

    // Session Data
    Route::get('/session/heartbeat', function () {
        return response()->json([
            'alive' => true,
            'keep_logged_in' => session('keep_logged_in', false),
        ]);
    })->name('api.session.heartbeat');
});

// ─── External API (API-Key Protected, no session required) ─────────────────
Route::get('/masterlist/export', [EmployeeController::class, 'masterlistExport'])->name('api.masterlist.export');
