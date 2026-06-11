@extends('dashboard.layouts.app')

@section('page-title', 'Pertanyaan Populer')
@section('mobile-title', 'Pertanyaan Populer')
@section('breadcrumb', 'Analitik')
@section('page-heading', 'Pertanyaan Populer')
@section('page-subheading', 'Daftar pertanyaan yang paling sering diajukan oleh mahasiswa kepada chatbot.')

@section('content')
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Pertanyaan Populer</h2>
        <p class="text-gray-400 text-sm max-w-sm">
            Tabel lengkap pertanyaan yang paling sering ditanyakan beserta
            jumlah kemunculannya akan ditampilkan di halaman ini.
        </p>
        <span class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-semibold">
            🚧 Dalam Pengembangan
        </span>
    </div>
@endsection
