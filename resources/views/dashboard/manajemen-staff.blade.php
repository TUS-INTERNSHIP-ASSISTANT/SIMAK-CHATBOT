@extends('dashboard.layouts.app')

@section('page-title', 'Manajemen Staff')
@section('mobile-title', 'Manajemen Staff')
@section('breadcrumb', 'Akun')
@section('page-heading', 'Manajemen Staff')
@section('page-subheading', 'Kelola akun staff yang memiliki akses ke portal ini.')

@section('page-actions')
    <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#7A203A] text-white text-sm font-semibold hover:bg-[#9B2A4A] transition-colors shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Staff
    </button>
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 rounded-2xl bg-violet-50 flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Manajemen Staff</h2>
        <p class="text-gray-400 text-sm max-w-sm">
            Tabel daftar staff, pengaturan role, dan manajemen akses
            akan tersedia di halaman ini.
        </p>
        <span class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-violet-50 text-violet-600 text-xs font-semibold">
            🚧 Dalam Pengembangan
        </span>
    </div>
@endsection
