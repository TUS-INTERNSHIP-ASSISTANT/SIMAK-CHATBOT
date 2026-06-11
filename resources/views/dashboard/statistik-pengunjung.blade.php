@extends('dashboard.layouts.app')

@section('page-title', 'Statistik Pengunjung')
@section('mobile-title', 'Statistik Pengunjung')
@section('breadcrumb', 'Analitik')
@section('page-heading', 'Statistik Pengunjung')
@section('page-subheading', 'Data pengunjung dan tren penggunaan layanan SIMAK dari waktu ke waktu.')

@section('content')
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Statistik Pengunjung</h2>
        <p class="text-gray-400 text-sm max-w-sm">
            Grafik jumlah pengunjung harian, mingguan, dan bulanan serta
            analitik sesi akan tersedia di halaman ini.
        </p>
        <span class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold">
            🚧 Dalam Pengembangan
        </span>
    </div>
@endsection
