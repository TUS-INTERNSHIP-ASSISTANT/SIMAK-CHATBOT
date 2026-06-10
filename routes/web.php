<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.landing-page');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('components.dashboard');
});
