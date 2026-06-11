@extends('dashboard.layouts.app')

@section('page-title', 'Statistik Chatbot')
@section('mobile-title', 'Statistik Chatbot')
@section('breadcrumb', 'Analitik')
@section('page-heading', 'Statistik Chatbot')
@section('page-subheading', 'Pantau performa dan penggunaan chatbot SIMAK secara real-time.')

@section('content')
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Statistik Chatbot</h2>
        <p class="text-gray-400 text-sm max-w-sm">
            Grafik percakapan, tingkat respons, dan analitik penggunaan chatbot
            akan ditampilkan di halaman ini.
        </p>
        <span class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-sky-600 text-xs font-semibold">
            🚧 Dalam Pengembangan
        </span>
    </div>
@endsection
