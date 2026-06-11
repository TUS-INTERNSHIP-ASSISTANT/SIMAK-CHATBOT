<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') — SIMAK Staff Portal</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="bg-[#F4F6FB] text-gray-800 antialiased"
    x-data="{ sidebarOpen: false }"
>
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- SIDEBAR (reusable partial)                                          --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @include('dashboard.partials.sidebar')

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- MAIN CONTENT                                                        --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div class="md:ml-[260px] min-h-screen flex flex-col">

        {{-- Top Bar mobile --}}
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-200/60 px-6 py-4 flex items-center gap-4 md:hidden">
            <button @click="sidebarOpen = true"
                class="p-2 rounded-xl hover:bg-gray-100 transition text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-semibold text-[#7A203A]">@yield('mobile-title', 'SIMAK Staff Portal')</span>
        </header>

        {{-- Breadcrumb / Page Header --}}
        <div class="px-6 lg:px-10 pt-8 pb-2">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('dashboard.home') }}" class="hover:text-[#7A203A] transition">Dashboard</a>
                @hasSection('breadcrumb')
                    <span>/</span>
                    <span class="text-gray-600">@yield('breadcrumb')</span>
                @endif
            </div>
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">@yield('page-heading', 'Dashboard')</h1>
                    <p class="text-sm text-gray-400 mt-1">@yield('page-subheading', '')</p>
                </div>
                @hasSection('page-actions')
                    <div class="flex items-center gap-3 mt-1">
                        @yield('page-actions')
                    </div>
                @endif
            </div>
        </div>

        {{-- Page Content --}}
        <main class="flex-1 px-6 lg:px-10 py-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
