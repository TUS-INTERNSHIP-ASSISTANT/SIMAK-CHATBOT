<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Route publik (landing page & auth) tidak memerlukan middleware apapun.
| Route dashboard dikelompokkan dalam satu group dengan middleware
| 'dashboard.auth' agar mudah di-extend ke depannya (misalnya menambah
| middleware role/permission cukup di satu tempat).
|
*/

// ── Public Routes ────────────────────────────────────────────────────────────

Route::get('/', fn () => view('layouts.landing-page'))->name('home');

Route::get('/login', fn () => view('auth.login'))->name('login');

// POST /login — web-based login (session dibuat di sini, bukan di /api/login)
Route::post('/login', [AuthController::class, 'webLogin'])->name('login.post');

// POST /logout — hapus session dan redirect ke login
Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

// ── Protected Dashboard Routes ────────────────────────────────────────────────

Route::middleware(['web', 'dashboard.auth'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {

        // Halaman utama dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('home');

        // ── Dokumen ──────────────────────────────────────────────────────────
        Route::resource('kelola-dokumen', DocumentController::class)
            ->except(['create', 'edit'])
            ->parameters(['kelola-dokumen' => 'document']);

        // Restore soft-deleted document
        Route::post('kelola-dokumen/{id}/restore', [DocumentController::class, 'restore'])
            ->name('kelola-dokumen.restore');

        Route::get('/knowledge-base', fn () => view('dashboard.knowledge-base'))
            ->name('knowledge-base');

        // ── Analitik ─────────────────────────────────────────────────────────
        Route::get('/statistik-chatbot', fn () => view('dashboard.statistik-chatbot'))
            ->name('statistik-chatbot');

        Route::get('/pertanyaan-populer', fn () => view('dashboard.pertanyaan-populer'))
            ->name('pertanyaan-populer');

        Route::get('/statistik-pengunjung', fn () => view('dashboard.statistik-pengunjung'))
            ->name('statistik-pengunjung');

        // ── Akun ─────────────────────────────────────────────────────────────
        Route::get('/manajemen-staff', fn () => view('dashboard.manajemen-staff'))
            ->name('manajemen-staff');
    });
