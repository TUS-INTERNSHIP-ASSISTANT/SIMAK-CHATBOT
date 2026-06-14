@extends('dashboard.layouts.app')

@section('page-title', 'Pertanyaan Populer')
@section('mobile-title', 'Pertanyaan Populer')
@section('breadcrumb', 'Analitik')
@section('page-heading', 'Pertanyaan Populer')
@section('page-subheading', 'Daftar pertanyaan yang paling sering diajukan oleh mahasiswa kepada chatbot.')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-5 border-b border-gray-100 gap-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">
                    {{ $showAll ? 'Semua Pertanyaan Populer' : '10 Pertanyaan Terpopuler' }}
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">Diurutkan berdasarkan jumlah tertinggi</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if($showAll)
                    <a href="{{ route('dashboard.pertanyaan-populer') }}" 
                       class="inline-flex items-center gap-1.5 bg-white border border-[#7A203A]/20 text-[#7A203A] px-4 py-2 rounded-xl text-xs font-semibold hover:border-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300">
                        Tampilkan 10 Teratas
                    </a>
                @else
                    <a href="{{ route('dashboard.pertanyaan-populer', ['all' => 1]) }}" 
                       class="inline-flex items-center gap-1.5 bg-[#7A203A] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-[0_4px_4px_rgba(205,108,108,0.15)] hover:bg-[#5A182C] hover:-translate-y-0.5 transition-all duration-300">
                        Tampilkan Semua
                    </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-6 py-3.5 w-16">
                            No
                        </th>
                        <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wide px-4 py-3.5">
                            Pertanyaan
                        </th>
                        <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wide px-6 py-3.5 w-32">
                            Ditanyakan
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($popularQuestions as $item)
                        <tr class="hover:bg-gray-50/40 transition-colors group">
                            <td class="px-6 py-4 text-gray-400 text-xs font-medium">
                                {{ $item['no'] }}
                            </td>
                            <td class="px-4 py-4 text-gray-700 font-medium">
                                {{ $item['question'] }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center justify-center min-w-[2.5rem] text-xs font-bold text-[#7A203A] bg-[#7A203A]/8 rounded-lg px-2.5 py-1">
                                    {{ number_format($item['count']) }} kali
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-400 text-sm">Belum ada data pertanyaan chatbot yang terekam.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
