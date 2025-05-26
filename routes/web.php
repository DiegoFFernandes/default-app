<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/clear-cache-all', function () {

    Artisan::call('cache:clear');

    dd("Cache Clear All");
});

Auth::routes();

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');


Route::get('/home', [HomeController::class, 'index'])->name('home');


