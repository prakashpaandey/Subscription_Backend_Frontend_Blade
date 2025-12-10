<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;

Route::get('/', function () {
    return view('welcome');
});

// Package Management Page
Route::get('/packages', [PackageController::class, 'indexView'])->name('packages.index');
