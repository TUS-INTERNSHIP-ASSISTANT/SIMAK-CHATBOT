{{--
|--------------------------------------------------------------------------
| Dashboard Sidebar Partial
|--------------------------------------------------------------------------
| File ini adalah partial (komponen reusable) untuk sidebar dashboard.
| Di-include oleh semua halaman dashboard menggunakan:
|   @include('dashboard.partials.sidebar')
|
| Scalable: Menambah menu baru cukup di file ini saja, semua halaman
| akan otomatis terupdate.
--}}

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
        <img src="{{ asset('assets/images/simak-logo-transparent.png') }}" class="w-40 md:w-50 h-auto rounded-lg">

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

        @php
            /**
             * Definisi menu sidebar.
             * Untuk menambah menu baru: cukup tambah item di array ini.
             * Format: ['route' => 'named_route', 'label' => 'Nama Menu', 'icon' => 'svg_path_d']
             */
            $menuGroups = [
                [
                    'label' => null, // null = tanpa label group
                    'items' => [
                        [
                            'route' => 'dashboard.home',
                            'label' => 'Dashboard',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                        ],
                    ],
                ],
                [
                    'label' => 'Dokumen',
                    'items' => [
                        [
                            'route'          => 'dashboard.kelola-dokumen.index',
                            'routeIsPattern' => 'dashboard.kelola-dokumen.*',
                            'label'          => 'Kelola Dokumen',
                            'icon'           => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        ],
                        [
                            'route' => 'dashboard.knowledge-base',
                            'label' => 'Knowledge Base',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                        ],
                    ],
                ],
                [
                    'label' => 'Analitik',
                    'items' => [
                        [
                            'route' => 'dashboard.statistik-chatbot',
                            'label' => 'Statistik Chatbot',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
                        ],
                        [
                            'route' => 'dashboard.pertanyaan-populer',
                            'label' => 'Pertanyaan Populer',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        [
                            'route' => 'dashboard.statistik-pengunjung',
                            'label' => 'Statistik Pengunjung',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        ],
                    ],
                ],
                [
                    'label' => 'Akun',
                    'items' => [
                        [
                            'route' => 'dashboard.manajemen-staff',
                            'label' => 'Manajemen Staff',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
                        ],
                    ],
                ],
            ];
        @endphp

        @foreach ($menuGroups as $group)
            <div>
                @if ($group['label'])
                    <p class="px-4 mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-[#6D263C]">
                        {{ $group['label'] }}
                    </p>
                @endif
                <div class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @php $isActive = request()->routeIs($item['routeIsPattern'] ?? $item['route']); @endphp
                        <a href="{{ route($item['route']) }}"
                            class="{{ $isActive ? 'bg-[#F9F0F3] text-[#7A203A]' : 'text-[#4A4A4A] hover:text-[#7A203A] hover:bg-[#F9F0F3]/60' }}
                                          group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-150">
                            <span
                                class="{{ $isActive ? 'text-[#7A203A]' : 'text-[#4A4A4A] group-hover:text-[#7A203A]' }} transition-colors flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    {!! $item['icon'] !!}
                                </svg>
                            </span>
                            <span class="truncate">{{ $item['label'] }}</span>
                            @if ($isActive)
                                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-[#E8617A] flex-shrink-0"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    {{-- Logout --}}
    <div class="px-3 py-4 border-t border-black/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium
                       text-red-400/80 hover:text-red-500 hover:bg-red-50 transition-all duration-150">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 opacity-70 group-hover:opacity-100 transition-opacity flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>