<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/manajemen-staff', function () {
    return view('review.app.manajemen-staff');
});
