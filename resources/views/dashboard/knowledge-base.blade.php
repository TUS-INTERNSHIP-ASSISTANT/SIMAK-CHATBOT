@extends('dashboard.layouts.app')

@section('page-title', 'Knowledge Base & RAG Settings')
@section('mobile-title', 'Knowledge Base')
@section('breadcrumb', 'Dokumen')
@section('page-heading', 'Knowledge Base & RAG')
@section('page-subheading', 'Kelola data pengetahuan aktif dan konfigurasi parameter kecerdasan buatan (RAG) chatbot.')

@section('page-actions')
    <button
        id="btn-sync-rag"
        onclick="triggerRAGSync()"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#7A203A] text-white text-sm font-semibold hover:bg-[#9B2A4A] active:scale-95 transition-all shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 transition-transform duration-1000" id="sync-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z" />
        </svg>
        <span id="sync-text">Sinkronisasi & Re-index RAG</span>
    </button>
@endsection

@section('content')
    {{-- Notifikasi Toast Banner --}}
    <div id="toast-notification" class="hidden mb-6 flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium transition-all duration-300">
        <div id="toast-icon-container" class="shrink-0 mt-0.5"></div>
        <div id="toast-message" class="flex-1"></div>
        <button onclick="hideToast()" class="ml-auto text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    @if (session('success'))
        <div id="flash-success"
            class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
            <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- ── 1. Stats Overview Grid ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        {{-- Card 1: Active Datasets --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow duration-200">
            <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#7A203A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Dataset Aktif</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5" id="stat-active-docs">{{ $activeDocsCount }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Dokumen berstatus aktif</p>
            </div>
        </div>

        {{-- Card 2: Total Chunks --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow duration-200">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total Chunks</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5" id="stat-total-chunks">{{ $totalChunks }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Fragmen teks terindeks</p>
            </div>
        </div>

        {{-- Card 3: Sync Status --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow duration-200">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Pengindeksan</p>
                <p class="text-lg font-bold text-gray-900 mt-0.5 truncate" id="stat-last-sync">{{ $lastSyncTime }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Waktu sinkronisasi RAG</p>
            </div>
        </div>

        {{-- Card 4: KB Last Updated --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow duration-200">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Basis Pengetahuan</p>
                <p class="text-sm font-bold text-gray-900 mt-0.5 truncate" id="stat-kb-last-updated">{{ $kbLastUpdated ?? 'Belum diperbarui' }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Terakhir diperbarui</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8 items-start">
        {{-- Kolom Kiri: RAG Settings (5 Cols) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-semibold text-gray-900">Konfigurasi Parameter RAG</h3>
                <p class="text-xs text-gray-400 mt-0.5">Tuning data dan kecerdasan model chatbot.</p>
            </div>

            <form id="settings-form" onsubmit="saveRAGSettings(event)" class="p-6 space-y-5 flex-1">
                @csrf
                {{-- System Instruction / Persona --}}
                <div class="space-y-1.5">
                    <label for="system_prompt" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">System Prompt (Instruksi Chatbot)</label>
                    <textarea
                        name="system_prompt"
                        id="system_prompt"
                        rows="5"
                        required
                        class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50/30 px-4 py-3 outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition resize-none leading-relaxed text-gray-700"
                        placeholder="Ubah instruksi atau kepribadian chatbot di sini...">{{ $systemPrompt }}</textarea>
                    <span class="text-[10px] text-gray-400 block leading-normal">Instruksi utama yang mendikte nada, gaya bahasa, dan batasan chatbot dalam menjawab pertanyaan mahasiswa.</span>
                </div>

                    {{-- Knowledge Base Prompt --}}
                    <div class="space-y-1.5">
                        <label for="knowledge_base_prompt" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Knowledge Base Prompt (Tuning Domain Context)</label>
                        <textarea
                            name="knowledge_base_prompt"
                            id="knowledge_base_prompt"
                            rows="5"
                            class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50/30 px-4 py-3 outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition resize-none leading-relaxed text-gray-700"
                            placeholder="Tuning domain context beyond raw documents...">{{ $knowledgeBasePrompt }}</textarea>
                        <span class="text-[10px] text-gray-400 block leading-normal">Tuning konteks domain untuk chatbot di luar dokumen mentah.</span>
                    </div>

                {{-- Model Selection --}}
                <div class="space-y-1.5">
                    <label for="model" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Model AI (LLM)</label>
                    <select
                        name="model"
                        id="model"
                        required
                        class="w-full text-sm rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition text-gray-700">
                        <option value="groq-llama3-8b" {{ $model === 'groq-llama3-8b' ? 'selected' : '' }}>Groq / Llama 3.1 8B Instant (Direkomendasikan)</option>
                        <option value="openai-gpt-4o-mini" {{ $model === 'openai-gpt-4o-mini' ? 'selected' : '' }}>OpenAI / GPT-4o Mini</option>
                    </select>
                </div>

                {{-- Temperature Slider --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="temperature" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Kreativitas (Temperature)</label>
                        <span id="temp-val-display" class="text-xs font-bold text-[#7A203A] bg-[#7A203A]/5 px-2 py-0.5 rounded-lg">0.5</span>
                    </div>
                    <input
                        type="range"
                        name="temperature"
                        id="temperature"
                        min="0"
                        max="1"
                        step="0.1"
                        value="{{ $temperature }}"
                        oninput="document.getElementById('temp-val-display').textContent = this.value"
                        class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-[#7A203A]">
                    <div class="flex justify-between text-[10px] text-gray-400 font-medium">
                        <span>Faktual & Kaku (0.0)</span>
                        <span>Seimbang (0.5)</span>
                        <span>Kreatif & Bebas (1.0)</span>
                    </div>
                </div>

                {{-- Chunk Settings --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="chunk_size" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Chunk Size (Token)</label>
                        <input
                            type="number"
                            name="chunk_size"
                            id="chunk_size"
                            min="500"
                            max="1000"
                            required
                            value="{{ $chunkSize }}"
                            class="w-full text-sm rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition text-gray-700">
                        <span class="text-[9px] text-gray-400 block leading-tight">Ukuran fragmen dokumen (500 - 1000).</span>
                    </div>

                    <div class="space-y-1.5">
                        <label for="chunk_overlap" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Chunk Overlap</label>
                        <input
                            type="number"
                            name="chunk_overlap"
                            id="chunk_overlap"
                            min="0"
                            required
                            value="{{ $chunkOverlap }}"
                            class="w-full text-sm rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition text-gray-700">
                        <span class="text-[9px] text-gray-400 block leading-tight">Kontekstual overlap antarsegmen.</span>
                    </div>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        id="btn-save-settings"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 active:scale-98 transition-all shadow-sm">
                        <span>Simpan Konfigurasi</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Kolom Kanan: Interactive Playground (7 Cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-[700px]">
            {{-- Chat Header --}}
            <div class="bg-gradient-to-r from-[#7A203A] to-[#9B2E4A] px-5 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm">
                        <img src="{{ asset('assets/images/robot-preview.png') }}" alt="Robot" class="w-6 h-6 object-contain">
                    </div>
                    <div>
                        <p class="text-white text-sm font-semibold leading-tight">Asisten Virtual SIMAK</p>
                        <p class="text-white/80 text-xs">Online • Siap membantu</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-white/15 text-white text-[10px] font-medium">
                    AI Chatbot
                </span>
            </div>

            {{-- Chat Messages Area --}}
            <div class="flex-1 bg-[#FCFAFB] p-5 overflow-y-auto space-y-4" id="chat-messages-container">
                {{-- Bot Welcome Message --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm">
                        <img src="{{ asset('assets/images/robot-preview.png') }}" alt="Bot" class="w-4.5 h-4.5 object-contain">
                    </div>
                    <div class="max-w-[85%]">
                        <div class="rounded-2xl rounded-tl-sm bg-white border border-gray-100 px-4 py-3 shadow-sm">
                            <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">
                                Halo! Saya Asisten RAG. Uji coba dengan mengetik pertanyaan seputar Magang atau Kerja Praktik di bawah. Saya akan mengekstrak jawaban langsung dari dokumen aktif Anda.
                            </p>
                        </div>
                        <p class="mt-1 text-[9px] text-gray-400">SIMAK • Sekarang</p>
                    </div>
                </div>
            </div>

            {{-- Chat Loading Indicator --}}
            <div class="hidden px-5 py-2 flex items-start gap-3 shrink-0" id="chat-typing-indicator">
                <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm">
                    <img src="{{ asset('assets/images/robot-preview.png') }}" alt="Bot" class="w-4.5 h-4.5 object-contain">
                </div>
                <div class="bg-white rounded-2xl rounded-tl-sm border border-gray-100 px-4 py-3 shadow-sm flex items-center gap-1.5 py-3.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0.2s"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0.4s"></span>
                </div>
            </div>

            {{-- Fast Questions Suggestions --}}
            <div class="px-5 py-3 bg-white border-t border-gray-100 shrink-0">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Saran Pertanyaan Uji Coba</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="insertPlaygroundQuery('Apa saja syarat Kerja Praktik?')" class="text-xs px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition text-gray-600">
                        Syarat Kerja Praktik
                    </button>
                    <button type="button" onclick="insertPlaygroundQuery('Bagaimana prosedur pengajuan Magang?')" class="text-xs px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition text-gray-600">
                        Prosedur Daftar Magang
                    </button>
                </div>
            </div>

            {{-- Chat Input Form --}}
            <form id="playground-chat-form" onsubmit="submitPlaygroundQuery(event)" class="border-t border-gray-100 bg-white p-4 shrink-0 flex gap-3">
                <input
                    type="text"
                    id="playground-query-input"
                    placeholder="Tanya seputar basis pengetahuan RAG..."
                    required
                    autocomplete="off"
                    class="flex-1 rounded-xl border border-gray-200 bg-[#FCFAFB] px-4 py-3 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition">
                <button
                    type="submit"
                    id="btn-chat-send"
                    class="inline-flex items-center justify-center gap-2 bg-[#7A203A] text-white px-5 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-[#5A182C] active:scale-95 transition shadow-sm shrink-0 whitespace-nowrap">
                    Kirim
                </button>
            </form>
        </div>
    </div>

    {{-- ── 3. Active Dataset Document Table ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Dataset Berkas Aktif (Dokumen Terindeks)</h3>
                <p class="text-xs text-gray-400 mt-0.5">Daftar berkas aktif di Kelola Dokumen yang dimasukkan dalam dataset RAG.</p>
            </div>
            <a href="{{ route('dashboard.kelola-dokumen.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7A203A] hover:underline">
                Kelola Dokumen Terkait
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/30 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="pl-6 pr-3 py-3.5 w-12 text-center">No</th>
                        <th class="px-3 py-3.5">Dokumen</th>
                        <th class="px-3 py-3.5 w-28">Tipe</th>
                        <th class="px-3 py-3.5 w-24">Ukuran</th>
                        <th class="px-3 py-3.5 w-28 text-center">Jumlah Chunks</th>
                        <th class="px-3 py-3.5 w-40">Status Indeks</th>
                        <th class="pl-3 pr-6 py-3.5 w-44">Tanggal Indeks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="docs-table-body">
                    @forelse ($documents as $index => $doc)
                        <tr class="hover:bg-gray-50/40 transition-colors group">
                            <td class="pl-6 pr-3 py-4 text-center text-xs font-medium text-gray-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center shrink-0 text-gray-400 group-hover:text-[#7A203A] group-hover:bg-[#7A203A]/5 transition-colors">
                                        @if ($doc->type === 'pdf')
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 truncate max-w-[280px]">{{ $doc->title }}</p>
                                        @if ($doc->description)
                                            <p class="text-xs text-gray-400 truncate max-w-[280px] mt-0.5">{{ $doc->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @if($doc->type === 'pdf') bg-red-50 text-red-600 ring-1 ring-red-100
                                    @elseif($doc->type === 'docx') bg-blue-50 text-blue-600 ring-1 ring-blue-100
                                    @elseif($doc->type === 'excel') bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100
                                    @else bg-amber-50 text-amber-600 ring-1 ring-amber-100 @endif">
                                    {{ $doc->type === 'excel' ? 'Excel' : strtoupper($doc->type) }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-gray-500 text-xs">
                                {{ $doc->formattedSize() }}
                            </td>
                            <td class="px-3 py-4 text-center font-semibold text-gray-700 text-xs" id="doc-chunks-{{ $doc->id }}">
                                {{ $doc->chunk_count ?? '0' }}
                            </td>
                            <td class="px-3 py-4">
                                <span id="doc-status-badge-{{ $doc->id }}" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $doc->indexed_at ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-amber-50 text-amber-600 ring-1 ring-amber-100' }}">
                                    <span id="doc-status-dot-{{ $doc->id }}" class="w-1.5 h-1.5 rounded-full {{ $doc->indexed_at ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse' }}"></span>
                                    <span id="doc-status-text-{{ $doc->id }}">{{ $doc->indexed_at ? 'Indexed' : 'Pending Sync' }}</span>
                                </span>
                            </td>
                            <td class="pl-3 pr-6 py-4 text-gray-400 text-xs" id="doc-indexed-{{ $doc->id }}">
                                {{ $doc->indexed_at ? $doc->indexed_at->format('d M Y, H:i') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="font-medium text-gray-500">Belum ada dokumen aktif yang terindeks.</p>
                                    <p class="text-xs">Silakan unggah dokumen baru atau aktifkan dokumen di Kelola Dokumen.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // ── Toast Notification Helper ──
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-notification');
        const iconContainer = document.getElementById('toast-icon-container');
        const messageContainer = document.getElementById('toast-message');

        toast.className = "mb-6 flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium transition-all duration-300";

        if (type === 'success') {
            toast.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
            iconContainer.innerHTML = `<svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else {
            toast.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
            iconContainer.innerHTML = `<svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        }

        messageContainer.textContent = message;
        toast.classList.remove('hidden');

        // Scroll to toast
        toast.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideToast() {
        document.getElementById('toast-notification').classList.add('hidden');
    }

    // ── Save RAG settings via AJAX ──
    function saveRAGSettings(e) {
        e.preventDefault();

        const form = document.getElementById('settings-form');
        const btn = document.getElementById('btn-save-settings');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = `<span>Menyimpan...</span>`;

        const formData = new FormData(form);

        fetch("{{ route('dashboard.knowledge-base.settings') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw err;
                }).catch(() => {
                    throw new Error('Gagal memproses permintaan pada server (Status: ' + response.status + ').');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Update active model stat if the element exists
                const activeModelEl = document.getElementById('stat-active-model');
                if (activeModelEl) {
                    const selectedModel = document.getElementById('model').value;
                    let formattedModel = selectedModel === 'groq-llama3-8b' ? 'Groq Llama 3.1 8B' : (selectedModel === 'openai-gpt-4o-mini' ? 'OpenAI GPT-4o Mini' : selectedModel);
                    activeModelEl.textContent = formattedModel;
                }
            } else {
                showToast(data.message || 'Gagal menyimpan konfigurasi.', 'error');
            }
        })
        .catch(err => {
            console.error('Settings save error:', err);
            let errorMsg = 'Terjadi kesalahan koneksi saat menyimpan konfigurasi.';
            if (err.errors) {
                const firstErrorKey = Object.keys(err.errors)[0];
                if (firstErrorKey && err.errors[firstErrorKey].length > 0) {
                    errorMsg = err.errors[firstErrorKey][0];
                }
            } else if (err.message) {
                errorMsg = err.message;
            }
            showToast(errorMsg, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // ── Sync RAG Dataset via AJAX ──
    function triggerRAGSync() {
        const btn = document.getElementById('btn-sync-rag');
        const icon = document.getElementById('sync-icon');
        const text = document.getElementById('sync-text');

        btn.disabled = true;
        icon.classList.add('animate-spin');
        text.textContent = "Sedang mensinkronisasi...";

        // Set all badges to "Processing"
        const docs = @json($documents->pluck('id'));
        docs.forEach(id => {
            const badge = document.getElementById('doc-status-badge-' + id);
            const dot = document.getElementById('doc-status-dot-' + id);
            const statusTxt = document.getElementById('doc-status-text-' + id);

            if (badge) {
                badge.className = "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 ring-1 ring-amber-100";
                dot.className = "w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse";
                statusTxt.textContent = "Syncing...";
            }
        });

        fetch("{{ route('dashboard.knowledge-base.sync') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw err; });
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Update stats
                document.getElementById('stat-last-sync').textContent = data.last_sync;
                document.getElementById('stat-total-chunks').textContent = data.total_chunks;
                // Update KB timestamp jika tersedia
                if (data.kb_last_updated) {
                    const kbEl = document.getElementById('stat-kb-last-updated');
                    if (kbEl) kbEl.textContent = data.kb_last_updated;
                }

                // Update table row badges
                docs.forEach(id => {
                    const badge = document.getElementById('doc-status-badge-' + id);
                    const dot = document.getElementById('doc-status-dot-' + id);
                    const statusTxt = document.getElementById('doc-status-text-' + id);
                    const dateCell = document.getElementById('doc-indexed-' + id);

                    if (badge) {
                        badge.className = "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100";
                        dot.className = "w-1.5 h-1.5 rounded-full bg-emerald-500";
                        statusTxt.textContent = "Indexed";
                    }
                    if (dateCell) {
                        dateCell.textContent = data.last_sync;
                    }
                });
            } else {
                showToast(data.message || 'Gagal sinkronisasi.', 'error');
                resetSyncBadges();
            }
        })
        .catch(err => {
            console.error('RAG Sync error:', err);
            showToast(err.message || 'Terjadi kesalahan sistem saat melakukan sinkronisasi.', 'error');
            resetSyncBadges();
        })
        .finally(() => {
            btn.disabled = false;
            icon.classList.remove('animate-spin');
            text.textContent = "Sinkronisasi & Re-index RAG";
        });
    }

    function resetSyncBadges() {
        const docs = @json($documents);
        docs.forEach(doc => {
            const badge = document.getElementById('doc-status-badge-' + doc.id);
            const dot = document.getElementById('doc-status-dot-' + doc.id);
            const statusTxt = document.getElementById('doc-status-text-' + doc.id);

            if (badge) {
                if (doc.indexed_at) {
                    badge.className = "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100";
                    dot.className = "w-1.5 h-1.5 rounded-full bg-emerald-500";
                    statusTxt.textContent = "Indexed";
                } else {
                    badge.className = "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 ring-1 ring-amber-100";
                    dot.className = "w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse";
                    statusTxt.textContent = "Pending Sync";
                }
            }
        });
    }

    // ── Playground Chatbot Query Testing ──
    function insertPlaygroundQuery(text) {
        const input = document.getElementById('playground-query-input');
        input.value = text;
        input.focus();
    }

    function submitPlaygroundQuery(e) {
        e.preventDefault();

        const input = document.getElementById('playground-query-input');
        const query = input.value.trim();
        if (!query) return;

        const chatContainer = document.getElementById('chat-messages-container');
        const typingIndicator = document.getElementById('chat-typing-indicator');

        // Disable input & button
        input.disabled = true;
        document.getElementById('btn-chat-send').disabled = true;

        // Render User Message
        appendUserMessage(query);
        input.value = '';

        // Show typing indicator
        typingIndicator.classList.remove('hidden');
        chatContainer.scrollTop = chatContainer.scrollHeight;

        fetch("{{ route('dashboard.knowledge-base.query') }}", {
            method: 'POST',
            body: JSON.stringify({ query: query }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                appendBotMessage(data.answer, data.source);
            } else {
                appendBotMessage('Gagal memproses pertanyaan. Silakan coba lagi.');
            }
        })
        .catch(err => {
            console.error('Playground query error:', err);
            appendBotMessage('Terjadi kesalahan jaringan dalam menghubungi chatbot.');
        })
        .finally(() => {
            // Hide typing indicator
            typingIndicator.classList.add('hidden');
            input.disabled = false;
            document.getElementById('btn-chat-send').disabled = false;
            input.focus();
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    }

    function appendUserMessage(text) {
        const chatContainer = document.getElementById('chat-messages-container');
        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + '.' + String(now.getMinutes()).padStart(2, '0');

        const html = `
            <div class="flex items-start justify-end gap-3">
                <div class="max-w-[85%]">
                    <div class="rounded-2xl rounded-tr-sm bg-[#7A203A] px-4 py-3 shadow-sm">
                        <p class="text-xs sm:text-sm text-white leading-relaxed">${escapeHTML(text)}</p>
                    </div>
                    <p class="mt-1 text-[9px] text-gray-400 text-right">Anda • ${timeStr}</p>
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML('beforeend', html);
    }

    function appendBotMessage(text, source = null) {
        const chatContainer = document.getElementById('chat-messages-container');
        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + '.' + String(now.getMinutes()).padStart(2, '0');

        let sourceHtml = '';
        if (source) {
            const sourceBadgeClass = source.type === 'pdf' ? 'bg-red-50 text-red-600 ring-1 ring-red-100' : (source.type === 'docx' ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-100' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100');
            sourceHtml = `
                <div class="mt-2 flex items-center gap-1.5 flex-wrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-[#7A203A]/10 text-[#7A203A] text-[10px] font-semibold">Sumber RAG</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold ${sourceBadgeClass}">${escapeHTML(source.title)}</span>
                </div>
            `;
        }

        const html = `
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm">
                    <img src="{{ asset('assets/images/robot-preview.png') }}" alt="Bot" class="w-4.5 h-4.5 object-contain">
                </div>
                <div class="max-w-[85%]">
                    <div class="rounded-2xl rounded-tl-sm bg-white border border-gray-100 px-4 py-3 shadow-sm text-xs sm:text-sm text-gray-700 leading-relaxed">
                        <div class="space-y-2 select-text">${formatMarkdown(text)}</div>
                        ${sourceHtml}
                    </div>
                    <p class="mt-1 text-[9px] text-gray-400">SIMAK • ${timeStr}</p>
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML('beforeend', html);
    }

    function escapeHTML(str) {
        return str.replace(/[&<>"']/g,
            tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[tag] || tag)
        );
    }

    function formatMarkdown(text) {
        if (!text) return '';

        // Split text by lines to handle block elements
        const lines = text.split('\n');
        let htmlResult = [];
        let currentListType = null;

        function closeList() {
            if (currentListType) {
                htmlResult.push(`</${currentListType}>`);
                currentListType = null;
            }
        }

        for (let i = 0; i < lines.length; i++) {
            let line = lines[i];
            let trimmed = line.trim();

            // 1. Heading Markdown
            const headingMatch = trimmed.match(/^(#{1,6})\s+(.+)$/);
            if (headingMatch) {
                closeList();
                const level = headingMatch[1].length;
                const formattedText = formatInlineMarkdown(headingMatch[2]);
                let headingClass = 'font-bold text-gray-900 my-2 block';
                if (level === 1) headingClass += ' text-lg text-[#7A203A]';
                else if (level === 2) headingClass += ' text-md text-[#7A203A]';
                else headingClass += ' text-sm';
                htmlResult.push(`<h${level} class="${headingClass}">${formattedText}</h${level}>`);
                continue;
            }

            // 2. Unordered List
            const ulMatch = trimmed.match(/^[-*]\s+(.+)$/);
            if (ulMatch) {
                const formattedContent = formatInlineMarkdown(ulMatch[1]);
                if (currentListType !== 'ul') {
                    closeList();
                    htmlResult.push('<ul class="list-disc pl-5 my-2 space-y-1">');
                    currentListType = 'ul';
                }
                htmlResult.push(`<li class="text-xs sm:text-sm text-gray-700 leading-relaxed">${formattedContent}</li>`);
                continue;
            }

            // 3. Ordered List
            const olMatch = trimmed.match(/^\d+\.\s+(.+)$/);
            if (olMatch) {
                const formattedContent = formatInlineMarkdown(olMatch[1]);
                if (currentListType !== 'ol') {
                    closeList();
                    htmlResult.push('<ol class="list-decimal pl-5 my-2 space-y-1">');
                    currentListType = 'ol';
                }
                htmlResult.push(`<li class="text-xs sm:text-sm text-gray-700 leading-relaxed">${formattedContent}</li>`);
                continue;
            }

            // 4. Empty lines
            if (trimmed === '') {
                if (currentListType) continue;
                closeList();
                htmlResult.push('<div class="h-2"></div>');
                continue;
            }

            // 5. Normal text line
            closeList();
            htmlResult.push(`<p class="text-xs sm:text-sm text-gray-700 leading-relaxed mb-2">${formatInlineMarkdown(line)}</p>`);
        }

        closeList();
        return htmlResult.join('\n');
    }

    function formatInlineMarkdown(text) {
        if (!text) return '';

        const placeholders = [];
        let processed = text;

        // 1. Ekstrak markdown links [teks](url)
        processed = processed.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, (match, linkText, url) => {
            const idx = placeholders.length;
            placeholders.push({ type: 'mdlink', text: linkText, url: url });
            return `\x00PH${idx}\x00`;
        });

        // 2. Ekstrak bare URLs
        processed = processed.replace(/(https?:\/\/[^\s<>"{}|\\^`[\]]*[^\s<>"{}|\\^`[\].,;:!?()'])/g, (match) => {
            const idx = placeholders.length;
            placeholders.push({ type: 'url', url: match });
            return `\x00PH${idx}\x00`;
        });

        // 3. Escape HTML
        let result = processed.replace(/[&<>"']/g, tag => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[tag] || tag));

        // 4. Bold dan italic
        result = result.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900">$1</strong>');
        result = result.replace(/\*([^*]+)\*/g, '<em class="italic">$1</em>');
        result = result.replace(/_([^_]+)_/g, '<em class="italic">$1</em>');

        // 5. Kembalikan URL placeholder sebagai hyperlink
        result = result.replace(/\x00PH(\d+)\x00/g, (match, idx) => {
            const p = placeholders[parseInt(idx)];
            const safeUrl = p.url.replace(/"/g, '%22').replace(/'/g, '%27').replace(/</g, '%3C').replace(/>/g, '%3E');
            if (p.type === 'mdlink') {
                const safeText = p.text.replace(/[&<>"']/g, tag => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[tag] || tag));
                return `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer" class="text-[#7A203A] underline hover:text-[#5A182C] break-all">${safeText}</a>`;
            } else {
                const displayUrl = p.url.replace(/[&<>"']/g, tag => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[tag] || tag));
                return `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer" class="text-[#7A203A] underline hover:text-[#5A182C] break-all">${displayUrl}</a>`;
            }
        });

        return result;
    }
</script>
@endpush
