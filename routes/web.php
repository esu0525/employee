<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmployeeController;

Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
