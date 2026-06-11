<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard — SIMAK Staff Portal</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F4F6FB] text-gray-800 antialiased" x-data="dashboardApp()" x-init="init()">

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- SIDEBAR --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm md:hidden" style="display: none;"></div>

    {{-- Sidebar Panel --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed top-0 left-0 z-40 h-screen w-[260px] bg-[#FDFDFD] text-black flex flex-col
               transition-transform duration-300 ease-in-out
               md:translate-x-0">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-black/20">
            <img src="{{ asset('assets/images/simak-logo-transparent.png') }}" class="w-40  md:w-50 h-auto rounded-lg">
            <!-- <div class="w-8 h-8 rounded-lg bg-[#7A203A] flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
            </div> -->
            <!-- <div>
                <span class="text-lg font-bold tracking-tight">SIMAK</span>
                <p class="text-[10px] text-white/40 leading-none mt-0.5">Staff Portal</p>
            </div> -->

            {{-- Tombol close mobile --}}
            <button @click="sidebarOpen = false"
                class="ml-auto p-1 border border-[#6D263C] rounded-md hover:bg-[#F9F0F3] transition md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-6">

            {{-- Dashboard --}}
            <div>
                <a href="{{ route('dashboard.home') }}" id="nav-dashboard"
                    class="{{ request()->routeIs('dashboard.home') ? 'bg-[#F9F0F3] text-[#7A203A]' : 'text-[#4A4A4A] hover:text-[#7A203A] hover:bg-[#F9F0F3]/60' }}
                          group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-150">
                    <span
                        class="{{ request()->routeIs('dashboard.home') ? 'text-[#7A203A]' : 'text-[#4A4A4A] group-hover:text-[#7A203A]' }} transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </span>
                    Dashboard
                    @if(request()->routeIs('dashboard.home'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-[#E8617A]"></span>
                    @endif
                </a>
            </div>

            {{-- Dokumen --}}
            <div>
                <p class="px-4 mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-[#6D263C]">Dokumen</p>
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard.kelola-dokumen.index') }}" id="nav-kelola-dokumen"
                        class="{{ request()->routeIs('dashboard.kelola-dokumen.*') ? 'bg-[#F9F0F3] text-[#7A203A]' : 'text-[#4A4A4A] hover:text-[#7A203A] hover:bg-[#F9F0F3]/60' }}
                              group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-150">
                        <span
                            class="{{ request()->routeIs('dashboard.kelola-dokumen.*') ? 'text-[#7A203A]' : 'text-[#4A4A4A] group-hover:text-[#7A203A]' }} transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        Kelola Dokumen
                    </a>

                    <a href="{{ route('dashboard.knowledge-base') }}" id="nav-knowledge-base"
                        class="{{ request()->routeIs('dashboard.knowledge-base') ? 'bg-[#F9F0F3] text-[#7A203A]' : 'text-[#4A4A4A] hover:text-[#7A203A] hover:bg-[#F9F0F3]/60' }}
                              group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-150">
                        <span
                            class="{{ request()->routeIs('dashboard.knowledge-base') ? 'text-[#7A203A]' : 'text-[#4A4A4A] group-hover:text-[#7A203A]' }} transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </span>
                        Knowledge Base
                    </a>
                </div>
            </div>

            {{-- Analitik --}}
            <div>
                <p class="px-4 mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-[#6D263C]">Analitik</p>
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard.statistik-chatbot') }}" id="nav-statistik-chatbot"
                        class="{{ request()->routeIs('dashboard.statistik-chatbot') ? 'bg-[#F9F0F3] text-[#7A203A]' : 'text-[#4A4A4A] hover:text-[#7A203A] hover:bg-[#F9F0F3]/60' }}
                              group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-150">
                        <span
                            class="{{ request()->routeIs('dashboard.statistik-chatbot') ? 'text-[#7A203A]' : 'text-[#4A4A4A] group-hover:text-[#7A203A]' }} transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </span>
                        Statistik Chatbot
                    </a>

                    <a href="{{ route('dashboard.pertanyaan-populer') }}" id="nav-pertanyaan-populer"
                        class="{{ request()->routeIs('dashboard.pertanyaan-populer') ? 'bg-[#F9F0F3] text-[#7A203A]' : 'text-[#4A4A4A] hover:text-[#7A203A] hover:bg-[#F9F0F3]/60' }}
                              group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-150">
                        <span
                            class="{{ request()->routeIs('dashboard.pertanyaan-populer') ? 'text-[#7A203A]' : 'text-[#4A4A4A] group-hover:text-[#7A203A]' }} transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Pertanyaan Populer
                    </a>

                    <a href="{{ route('dashboard.statistik-pengunjung') }}" id="nav-statistik-pengunjung"
                        class="{{ request()->routeIs('dashboard.statistik-pengunjung') ? 'bg-[#F9F0F3] text-[#7A203A]' : 'text-[#4A4A4A] hover:text-[#7A203A] hover:bg-[#F9F0F3]/60' }}
                              group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-150">
                        <span
                            class="{{ request()->routeIs('dashboard.statistik-pengunjung') ? 'text-[#7A203A]' : 'text-[#4A4A4A] group-hover:text-[#7A203A]' }} transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        Statistik Pengunjung
                    </a>
                </div>
            </div>

            {{-- Akun --}}
            <div>
                <p class="px-4 mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-[#6D263C]">Akun</p>
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard.manajemen-staff') }}" id="nav-manajemen-staff"
                        class="{{ request()->routeIs('dashboard.manajemen-staff') ? 'bg-[#F9F0F3] text-[#7A203A]' : 'text-[#4A4A4A] hover:text-[#7A203A] hover:bg-[#F9F0F3]/60' }}
                              group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-150">
                        <span
                            class="{{ request()->routeIs('dashboard.manajemen-staff') ? 'text-[#7A203A]' : 'text-[#4A4A4A] group-hover:text-[#7A203A]' }} transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                        Manajemen Staff
                    </a>
                </div>
            </div>
        </nav>

        {{-- Logout --}}
        <div class="px-3 py-4 border-t border-black/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" id="btn-logout" class="w-full group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium
                           text-red-400/80 hover:text-red-500 hover:bg-red-50 transition-all duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 opacity-70 group-hover:opacity-100 transition-opacity" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- MAIN CONTENT --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}

    <div class="md:ml-[260px] min-h-screen flex flex-col">

        {{-- Top Bar (mobile hamburger) --}}
        <header
            class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-200/60 px-6 py-4 flex items-center gap-4 md:hidden">
            <button @click="sidebarOpen = true" id="btn-hamburger"
                class="p-2 rounded-xl hover:bg-gray-100 transition text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="font-semibold text-[#7A203A]">SIMAK Staff Portal</span>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 px-6 py-8 lg:px-10">

            {{-- ── Header Section ───────────────────────────────────────── --}}
            <div class="mb-8">
                <p class="text-sm text-gray-400 font-medium" x-text="currentDate"></p>
                <h1 class="text-2xl font-bold text-gray-900 mt-1" x-text="greeting"></h1>
            </div>

            {{-- ── Stats Cards (2×2 grid) ───────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

                {{-- Card 1: Dokumen Aktif --}}
                <div
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow duration-200">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Dokumen Aktif</p>
                        <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $activeDocuments }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            dari {{ $totalDocuments }} total
                            @if($inactiveDocuments > 0)
                                &bull; <span class="text-amber-500">{{ $inactiveDocuments }} nonaktif</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Card 2: Pengunjung --}}
                <div
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow duration-200">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total Pengunjung</p>
                        <p class="text-2xl font-bold text-gray-900 mt-0.5">1.000</p>
                        <p class="text-xs text-gray-400 mt-0.5">Pengunjung bulan ini</p>
                    </div>
                </div>

                {{-- Card 3: Pertumbuhan --}}
                <div
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow duration-200">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Pertumbuhan</p>
                        <p class="text-2xl font-bold text-gray-900 mt-0.5">30%</p>
                        <p class="text-xs text-emerald-500 mt-0.5 font-medium">↑ dari bulan lalu</p>
                    </div>
                </div>

                {{-- Card 4: Update Terakhir --}}
                <div
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow duration-200">
                    <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-violet-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Update Terakhir</p>
                        @if($lastDocument)
                            <p class="text-2xl font-bold text-gray-900 mt-0.5">
                                {{ $lastUploadDaysAgo === 0 ? 'Hari ini' : $lastUploadDaysAgo . ' hari' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5 truncate" title="{{ $lastDocument->title }}">
                                {{ Str::limit($lastDocument->title, 28) }}
                            </p>
                        @else
                            <p class="text-2xl font-bold text-gray-900 mt-0.5">—</p>
                            <p class="text-xs text-gray-400 mt-0.5">Belum ada dokumen</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Bottom Section (2 Kolom) ─────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Pertanyaan Populer --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Pertanyaan yang Sering Muncul</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Diurutkan berdasarkan jumlah tertinggi</p>
                        </div>
                        <a href="{{ route('dashboard.pertanyaan-populer') }}"
                            class="text-xs font-medium text-[#7A203A] hover:underline transition">
                            Lihat semua →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50/80">
                                    <th
                                        class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-6 py-3 w-10">
                                        No</th>
                                    <th
                                        class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-4 py-3">
                                        Pertanyaan</th>
                                    <th
                                        class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wide px-6 py-3">
                                        Ditanyakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @php
                                    $faqData = [
                                        ['no' => 1, 'question' => 'Apa syarat Kerja Praktik?', 'count' => 140],
                                        ['no' => 2, 'question' => 'Berapa minimal SKS untuk magang?', 'count' => 55],
                                        ['no' => 3, 'question' => 'Bagaimana cara daftar KP online?', 'count' => 48],
                                        ['no' => 4, 'question' => 'Kapan batas pengumpulan laporan?', 'count' => 32],
                                        ['no' => 5, 'question' => 'Siapa dosen pembimbing saya?', 'count' => 21],
                                    ];
                                @endphp
                                @foreach ($faqData as $item)
                                    <tr class="hover:bg-gray-50/60 transition-colors group">
                                        <td class="px-6 py-3.5 text-gray-400 text-xs font-medium">{{ $item['no'] }}</td>
                                        <td class="px-4 py-3.5 text-gray-700 font-medium">{{ $item['question'] }}</td>
                                        <td class="px-6 py-3.5 text-right">
                                            <span
                                                class="inline-flex items-center justify-center min-w-[2rem] text-xs font-bold text-[#7A203A] bg-[#7A203A]/8 rounded-lg px-2 py-0.5">
                                                {{ $item['count'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Log Aktivitas --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Log Aktivitas</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Riwayat aktivitas terbaru</p>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @php
                            $activityLog = [
                                ['time' => '10 Jun 2026, 09:14', 'activity' => 'Admin mengunggah file SOP KP.pdf', 'type' => 'upload'],
                                ['time' => '10 Jun 2026, 08:30', 'activity' => 'Knowledge Base diperbarui', 'type' => 'update'],
                                ['time' => '09 Jun 2026, 15:22', 'activity' => 'Admin menghapus dokumen lama', 'type' => 'delete'],
                                ['time' => '09 Jun 2026, 11:05', 'activity' => 'Staff baru berhasil login', 'type' => 'login'],
                                ['time' => '08 Jun 2026, 14:48', 'activity' => 'Statistik chatbot diperbarui otomatis', 'type' => 'update'],
                            ];
                            $typeConfig = [
                                'upload' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => '↑'],
                                'update' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'icon' => '✎'],
                                'delete' => ['bg' => 'bg-red-100', 'text' => 'text-red-500', 'icon' => '✕'],
                                'login' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => '→'],
                            ];
                        @endphp
                        @foreach ($activityLog as $log)
                            @php $cfg = $typeConfig[$log['type']] ?? $typeConfig['update']; @endphp
                            <div class="flex items-start gap-3 px-6 py-3.5 hover:bg-gray-50/60 transition-colors">
                                <span
                                    class="w-6 h-6 rounded-full {{ $cfg['bg'] }} {{ $cfg['text'] }} flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                    {{ $cfg['icon'] }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-700 font-medium leading-snug">{{ $log['activity'] }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $log['time'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardApp', () => ({
                sidebarOpen: false,
                greeting: '',
                currentDate: '',

                init() {
                    this.setGreetingAndDate();
                    // Set Authorization header untuk API call
                    const token = localStorage.getItem('access_token');
                    if (token) {
                        window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                    }
                },

                setGreetingAndDate() {
                    const now = new Date();
                    const hour = now.getHours();

                    // Greeting berdasarkan waktu
                    let greet = 'Selamat ';
                    if (hour >= 5 && hour < 12) greet += 'Pagi';
                    else if (hour >= 12 && hour < 15) greet += 'Siang';
                    else if (hour >= 15 && hour < 19) greet += 'Sore';
                    else greet += 'Malam';

                    // Role dari server (di-embed via Blade)
                    const role = '{{ auth()->user()->role ?? "Admin" }}';
                    const name = '{{ auth()->user()->name ?? "Admin" }}';
                    this.greeting = `${greet}, ${name}! 👋`;

                    // Format tanggal Indonesia
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    this.currentDate = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
                }
            }));
        });
    </script>

</body>

</html>