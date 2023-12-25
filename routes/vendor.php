<?php

use App\Http\Controllers\Backend\VendorController;
use Illuminate\Support\Facades\Route;

/** Vendor routes */

Route::get('dashboard', [VendorController::class, 'dashboard'])->name('dashboard');