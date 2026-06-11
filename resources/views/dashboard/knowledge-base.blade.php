@extends('dashboard.layouts.app')

@section('page-title', 'Knowledge Base')
@section('mobile-title', 'Knowledge Base')
@section('breadcrumb', 'Dokumen')
@section('page-heading', 'Knowledge Base')
@section('page-subheading', 'Kelola basis pengetahuan yang digunakan chatbot untuk menjawab pertanyaan mahasiswa.')

@section('content')
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Knowledge Base</h2>
        <p class="text-gray-400 text-sm max-w-sm">
            Di sini Anda dapat mengelola dan memperbarui basis pengetahuan chatbot,
            termasuk FAQ, panduan, dan dokumen referensi.
        </p>
        <span class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold">
            🚧 Dalam Pengembangan
        </span>
    </div>
@endsection
