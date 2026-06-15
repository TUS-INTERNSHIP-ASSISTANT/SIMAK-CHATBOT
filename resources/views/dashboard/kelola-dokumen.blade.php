@extends('dashboard.layouts.app')

@section('page-title', 'Kelola Dokumen')
@section('mobile-title', 'Kelola Dokumen')
@section('breadcrumb', 'Dokumen')
@section('page-heading', 'Kelola Dokumen')
@section('page-subheading', 'Upload, kelola, dan arsipkan dokumen yang digunakan oleh chatbot.')

{{-- ─────────────────────────────────────────────────────────────────────────── --}}
{{-- Upload Button (top-right)                                                   --}}
{{-- ─────────────────────────────────────────────────────────────────────────── --}}
@section('page-actions')
    <button
        id="btn-open-upload-modal"
        onclick="openUploadModal()"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#7A203A] text-white text-sm font-semibold hover:bg-[#9B2A4A] active:scale-95 transition-all shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Upload Dokumen
    </button>
@endsection

@section('content')

{{-- ─── Flash Messages ─────────────────────────────────────────────────────── --}}
@if (session('success'))
    <div id="flash-success"
        class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success') }}
        <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
@endif

@if (session('error') || $errors->any())
    <div id="flash-error"
        class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            @if (session('error'))
                <p>{{ session('error') }}</p>
            @endif
            @foreach ($errors->all() as $err)
                <p>{{ $err }}</p>
            @endforeach
        </div>
        <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-red-400 hover:text-red-600 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
@endif

{{-- ─── Search + Filter Bar ────────────────────────────────────────────────── --}}
<div class="mb-5 flex flex-col sm:flex-row gap-3">
    <form method="GET" action="{{ route('dashboard.kelola-dokumen.index') }}" class="flex flex-1 gap-3 flex-wrap">
        {{-- Search --}}
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
            </svg>
            <input
                id="search-input"
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari dokumen…"
                class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition"
            />
        </div>

        {{-- Filter tipe — hanya 3 opsi --}}
        <select
            id="type-filter"
            name="type"
            onchange="this.form.submit()"
            class="px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition cursor-pointer">
            <option value="">Semua Tipe</option>
            <option value="pdf"   {{ request('type') === 'pdf'   ? 'selected' : '' }}>PDF</option>
            <option value="docx"  {{ request('type') === 'docx'  ? 'selected' : '' }}>DOCX</option>
            <option value="excel" {{ request('type') === 'excel' ? 'selected' : '' }}>Excel</option>
        </select>

        {{-- Filter status --}}
        <select
            id="status-filter"
            name="status"
            onchange="this.form.submit()"
            class="px-3 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition cursor-pointer">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        {{-- Search button --}}
        <button type="submit"
            class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-600 hover:bg-gray-50 transition hidden sm:block cursor-pointer">
            Cari
        </button>

        @if(request()->hasAny(['search','type','status']))
            <a href="{{ route('dashboard.kelola-dokumen.index') }}"
               class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-500 hover:bg-gray-50 transition">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- ─── Document Table ─────────────────────────────────────────────────────── --}}
@if ($documents->isEmpty())

    {{-- Empty State --}}
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 rounded-2xl bg-[#7A203A]/8 flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#7A203A]/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-800 mb-2">
            @if(request()->hasAny(['search','type','status']))
                Tidak ada dokumen yang cocok
            @else
                Belum ada dokumen
            @endif
        </h2>
        <p class="text-gray-400 text-sm max-w-sm">
            @if(request()->hasAny(['search','type','status']))
                Coba ubah filter pencarian Anda, atau
                <a href="{{ route('dashboard.kelola-dokumen.index') }}" class="text-[#7A203A] hover:underline">tampilkan semua</a>.
            @else
                Klik <strong>Upload Dokumen</strong> untuk mulai menambahkan dokumen ke knowledge base chatbot.
            @endif
        </p>
    </div>

@else

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="pl-6 pr-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">No</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Tipe</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Ukuran</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Status</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">Upload</th>
                        <th class="pl-3 pr-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($documents as $index => $doc)
                        <tr class="hover:bg-gray-50/40 transition-colors group">
                            {{-- No --}}
                            <td class="pl-6 pr-3 py-4 text-gray-400 font-medium">
                                {{ $documents->firstItem() + $loop->index }}
                            </td>

                            {{-- Judul + deskripsi --}}
                            <td class="px-3 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- File type icon --}}
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                                        @if($doc->type === 'pdf') bg-red-50 text-red-500
                                        @elseif($doc->type === 'docx') bg-blue-50 text-blue-500
                                        @elseif($doc->type === 'excel') bg-emerald-50 text-emerald-600
                                        @else bg-amber-50 text-amber-500 @endif">
                                        @if($doc->type === 'excel')
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M14 3v18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" /></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 truncate max-w-[240px]">{{ $doc->title }}</p>
                                        @if($doc->description)
                                            <p class="text-xs text-gray-400 truncate max-w-[240px] mt-0.5">{{ $doc->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Tipe --}}
                            <td class="px-3 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @if($doc->type === 'pdf') bg-red-50 text-red-600 ring-1 ring-red-100
                                    @elseif($doc->type === 'docx') bg-blue-50 text-blue-600 ring-1 ring-blue-100
                                    @elseif($doc->type === 'excel') bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100
                                    @else bg-amber-50 text-amber-600 ring-1 ring-amber-100 @endif">
                                    {{ $doc->type === 'excel' ? 'Excel' : strtoupper($doc->type) }}
                                </span>
                            </td>

                            {{-- Ukuran --}}
                            <td class="px-3 py-4 text-gray-500">
                                {{ $doc->formattedSize() }}
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-4">
                                @if($doc->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif
                                    </span>
                                @elseif($doc->status === 'inactive')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 ring-1 ring-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>Processing
                                    </span>
                                @endif
                            </td>

                            {{-- Tanggal Upload --}}
                            <td class="px-3 py-4 text-gray-500 text-xs">
                                <div>{{ $doc->created_at->format('d M Y') }}</div>
                                <div class="text-gray-400">{{ $doc->uploader->name ?? '—' }}</div>
                            </td>

                            {{-- Aksi --}}
                            <td class="pl-3 pr-6 py-4">
                                <div class="flex items-center justify-end gap-1.5 opacity-70 group-hover:opacity-100 transition-opacity">
                                    {{-- Download --}}
                                    @if($doc->file_path)
                                        <a href="{{ route('dashboard.kelola-dokumen.show', $doc) }}"
                                           title="Download"
                                           class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                    @else
                                        <span class="p-2 text-gray-200 cursor-not-allowed" title="File tidak tersedia">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </span>
                                    @endif

                                    {{-- Edit --}}
                                    <button
                                        title="Edit"
                                        data-action="{{ route('dashboard.kelola-dokumen.update', $doc) }}"
                                        data-doc="{{ json_encode(['id' => $doc->id, 'title' => $doc->title, 'description' => $doc->description, 'status' => $doc->status]) }}"
                                        onclick="openEditModal(this)"
                                        class="p-2 rounded-lg text-gray-400 hover:text-[#7A203A] hover:bg-[#7A203A]/8 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>

                                    {{-- Delete --}}
                                    <button
                                        title="Hapus"
                                        data-action="{{ route('dashboard.kelola-dokumen.destroy', $doc) }}"
                                        data-title="{{ $doc->title }}"
                                        onclick="openDeleteModal(this)"
                                        class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($documents->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-4">
                <p class="text-xs text-gray-400">
                    Menampilkan {{ $documents->firstItem() }}–{{ $documents->lastItem() }} dari {{ $documents->total() }} dokumen
                </p>
                {{ $documents->links('pagination::simple-tailwind') }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-gray-100">
                <p class="text-xs text-gray-400">{{ $documents->total() }} dokumen</p>
            </div>
        @endif
    </div>

@endif

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- UPLOAD MODAL                                                               --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="upload-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog" aria-modal="true" aria-labelledby="upload-modal-title">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         onclick="closeUploadModal()"></div>

    {{-- Panel --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h2 id="upload-modal-title" class="text-base font-bold text-gray-900">Upload Dokumen Baru</h2>
            <button onclick="closeUploadModal()"
                class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="upload-form"
              method="POST"
              action="{{ route('dashboard.kelola-dokumen.store') }}"
              enctype="multipart/form-data"
              class="px-6 py-5 space-y-4"
              onsubmit="return handleUploadSubmit(this)">
            @csrf

            {{-- Judul --}}
            <div>
                <label for="upload-title" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Judul Dokumen <span class="text-red-500">*</span>
                </label>
                <input
                    id="upload-title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    placeholder="Contoh: Panduan Akademik 2024"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition"
                />
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="upload-desc" class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea
                    id="upload-desc"
                    name="description"
                    rows="2"
                    placeholder="Deskripsi singkat isi dokumen (opsional)"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition resize-none"
                >{{ old('description') }}</textarea>
            </div>

            {{-- Tipe — hanya 3 opsi --}}
            <div>
                <label for="upload-type" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tipe Dokumen <span class="text-red-500">*</span>
                </label>
                <select
                    id="upload-type"
                    name="type"
                    required
                    onchange="onTypeChange(this.value)"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition">
                    <option value="">Pilih tipe…</option>
                    <option value="pdf"   {{ old('type') === 'pdf'   ? 'selected' : '' }}>PDF</option>
                    <option value="docx"  {{ old('type') === 'docx'  ? 'selected' : '' }}>DOCX (Word)</option>
                    <option value="excel" {{ old('type') === 'excel' ? 'selected' : '' }}>Excel (XLS / XLSX / CSV)</option>
                </select>
            </div>

            {{-- File Upload --}}
            <div id="file-field">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    File <span class="text-red-500">*</span>
                </label>

                {{-- Drop zone --}}
                <div id="drop-zone"
                    class="relative flex flex-col items-center justify-center gap-2 w-full min-h-[9rem] rounded-xl border-2 border-dashed border-gray-200 hover:border-[#7A203A]/40 hover:bg-[#7A203A]/2 cursor-pointer transition-colors group"
                    onclick="document.getElementById('upload-file').click()"
                    ondragover="handleDragOver(event)"
                    ondragleave="handleDragLeave(event)"
                    ondrop="handleDrop(event)">

                    {{-- Default state --}}
                    <div id="dz-default" class="flex flex-col items-center gap-2 pointer-events-none">
                        <svg class="w-8 h-8 text-gray-300 group-hover:text-[#7A203A]/40 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-600">Drag & drop atau <span class="text-[#7A203A]">pilih file</span></p>
                            <p id="drop-zone-hint" class="text-xs text-gray-400 mt-0.5">Pilih tipe dokumen terlebih dahulu</p>
                        </div>
                    </div>

                    {{-- Selected state (hidden until file picked) --}}
                    <div id="dz-selected" class="hidden flex-col items-center gap-2 pointer-events-none">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-center px-4">
                            <p id="dz-filename" class="text-sm font-semibold text-gray-800 break-all"></p>
                            <p id="dz-filesize" class="text-xs text-gray-400 mt-0.5"></p>
                        </div>
                        <button type="button"
                            onclick="event.stopPropagation(); resetFileInput()"
                            class="pointer-events-auto text-xs text-red-500 hover:text-red-700 hover:underline transition mt-1">
                            Ganti file
                        </button>
                    </div>
                </div>

                {{-- File type error --}}
                <p id="file-type-error" class="hidden text-xs text-red-600 mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span id="file-type-error-msg"></span>
                </p>

                {{-- Hidden file input --}}
                <input id="upload-file" name="file" type="file"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv"
                       class="sr-only"
                       onchange="onFileSelected(this)" />
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                    onclick="closeUploadModal()"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    id="upload-submit-btn"
                    class="px-5 py-2.5 rounded-xl bg-[#7A203A] text-white text-sm font-semibold hover:bg-[#9B2A4A] active:scale-95 transition-all shadow-sm">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- EDIT MODAL                                                                 --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="edit-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog" aria-modal="true" aria-labelledby="edit-modal-title">

    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         onclick="closeEditModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h2 id="edit-modal-title" class="text-base font-bold text-gray-900">Edit Dokumen</h2>
            <button onclick="closeEditModal()"
                class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <form id="edit-form" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" value="PUT">

            <div>
                <label for="edit-title" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Judul Dokumen <span class="text-red-500">*</span>
                </label>
                <input id="edit-title" type="text" name="title" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition" />
            </div>

            <div>
                <label for="edit-desc" class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea id="edit-desc" name="description" rows="2"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition resize-none"></textarea>
            </div>

            <div>
                <label for="edit-status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <select id="edit-status" name="status"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                    onclick="closeEditModal()"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" id="edit-submit-btn"
                    class="px-5 py-2.5 rounded-xl bg-[#7A203A] text-white text-sm font-semibold hover:bg-[#9B2A4A] active:scale-95 transition-all shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- DELETE CONFIRM MODAL                                                       --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="delete-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">

    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         onclick="closeDeleteModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="mx-auto w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h2 id="delete-modal-title" class="text-base font-bold text-gray-900 mb-1">Hapus Dokumen?</h2>
            <p class="text-sm text-gray-500 mb-6">
                Dokumen "<span id="delete-doc-title" class="font-semibold text-gray-700"></span>" akan dihapus permanen. Tindakan ini <strong>tidak dapat dibatalkan</strong>.
            </p>
            <form id="delete-form" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <div class="flex gap-3">
                    <button type="button"
                        onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="delete-submit-btn"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 active:scale-95 transition-all">
                        Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- PAGE SCRIPTS                                                               --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
// ── Peta tipe → ekstensi yang diizinkan ──────────────────────────────────────
const TYPE_EXT_MAP = {
    'pdf'   : ['pdf'],
    'docx'  : ['doc', 'docx'],
    'excel' : ['xls', 'xlsx', 'csv'],
};

const TYPE_HINT = {
    'pdf'   : 'File PDF — maks. 10 MB',
    'docx'  : 'File DOC / DOCX — maks. 10 MB',
    'excel' : 'File XLS / XLSX / CSV — maks. 10 MB',
    ''      : 'Pilih tipe dokumen terlebih dahulu',
};

// ── Upload Modal helpers ──────────────────────────────────────────────────────
function openUploadModal() {
    document.getElementById('upload-modal').classList.remove('hidden');
    // Restore jika ada old() input setelah validasi error
}

function closeUploadModal() {
    document.getElementById('upload-modal').classList.add('hidden');
}

// ── Saat tipe dipilih: update hint di drop zone ───────────────────────────────
function onTypeChange(type) {
    const hint = document.getElementById('drop-zone-hint');
    if (hint) hint.textContent = TYPE_HINT[type] || TYPE_HINT[''];

    // Reset file jika tipe berubah (menghindari mismatch)
    const fileInput = document.getElementById('upload-file');
    if (fileInput && fileInput.files.length > 0) {
        resetFileInput();
    }
}

// ── Saat file dipilih (via klik atau drop) ────────────────────────────────────
function onFileSelected(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];

    // Validasi tipe
    const type = document.getElementById('upload-type').value;
    if (!type) {
        showFileTypeError('Pilih tipe dokumen terlebih dahulu sebelum memilih file.');
        resetFileInput();
        return;
    }

    const ext = file.name.split('.').pop().toLowerCase();
    const allowedExts = TYPE_EXT_MAP[type] || [];
    if (!allowedExts.includes(ext)) {
        showFileTypeError(
            `File "${file.name}" (${ext.toUpperCase()}) tidak sesuai dengan tipe yang dipilih (${type.toUpperCase()}). ` +
            `Ekstensi yang diizinkan: ${allowedExts.map(e => e.toUpperCase()).join(', ')}.`
        );
        resetFileInput();
        return;
    }

    hideFileTypeError();
    showDropZoneSelected(file);
}

function showDropZoneSelected(file) {
    const size = file.size < 1048576
        ? (file.size / 1024).toFixed(1) + ' KB'
        : (file.size / 1048576).toFixed(1) + ' MB';

    document.getElementById('dz-filename').textContent = file.name;
    document.getElementById('dz-filesize').textContent = size;
    document.getElementById('dz-default').classList.add('hidden');
    document.getElementById('dz-selected').classList.remove('hidden');
    document.getElementById('dz-selected').classList.add('flex');
}

function resetFileInput() {
    const fileInput = document.getElementById('upload-file');
    fileInput.value = '';
    document.getElementById('dz-default').classList.remove('hidden');
    document.getElementById('dz-selected').classList.add('hidden');
    document.getElementById('dz-selected').classList.remove('flex');
    hideFileTypeError();
}

function showFileTypeError(msg) {
    const errEl  = document.getElementById('file-type-error');
    const msgEl  = document.getElementById('file-type-error-msg');
    msgEl.textContent = msg;
    errEl.classList.remove('hidden');
}

function hideFileTypeError() {
    document.getElementById('file-type-error').classList.add('hidden');
}

// ── Drag & Drop ───────────────────────────────────────────────────────────────
function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('drop-zone').classList.add('border-[#7A203A]/60', 'bg-[#7A203A]/5');
}

function handleDragLeave(e) {
    e.stopPropagation();
    document.getElementById('drop-zone').classList.remove('border-[#7A203A]/60', 'bg-[#7A203A]/5');
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('drop-zone').classList.remove('border-[#7A203A]/60', 'bg-[#7A203A]/5');

    const files = e.dataTransfer.files;
    if (!files || files.length === 0) return;

    const fileInput = document.getElementById('upload-file');
    // DataTransfer trick untuk set files pada input
    const dt = new DataTransfer();
    dt.items.add(files[0]);
    fileInput.files = dt.files;

    onFileSelected(fileInput);
}

// ── Upload form submit ────────────────────────────────────────────────────────
function handleUploadSubmit(form) {
    // Cek ada file yang dipilih
    const fileInput = document.getElementById('upload-file');
    if (!fileInput.files || fileInput.files.length === 0) {
        showFileTypeError('Harap pilih file terlebih dahulu.');
        return false;
    }
    // Cek error masih tampil
    if (!document.getElementById('file-type-error').classList.contains('hidden')) {
        return false;
    }
    const btn = document.getElementById('upload-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Mengupload…';
    return true;
}

// ── Edit Modal ────────────────────────────────────────────────────────────────
function openEditModal(btn) {
    try {
        const doc    = JSON.parse(btn.getAttribute('data-doc'));
        const action = btn.getAttribute('data-action');

        document.getElementById('edit-form').setAttribute('action', action);
        document.getElementById('edit-title').value  = doc.title  || '';
        document.getElementById('edit-desc').value   = doc.description || '';
        document.getElementById('edit-status').value = doc.status || 'active';
        document.getElementById('edit-modal').classList.remove('hidden');
        document.getElementById('edit-title').focus();
    } catch (err) {
        console.error('openEditModal error:', err);
        alert('Gagal membuka form edit. Silakan coba lagi.');
    }
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

// Submit edit dengan loading state
document.getElementById('edit-form').addEventListener('submit', function () {
    const btn = document.getElementById('edit-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Menyimpan…';
});

// ── Delete Modal ──────────────────────────────────────────────────────────────
function openDeleteModal(btn) {
    const action = btn.getAttribute('data-action');
    const title  = btn.getAttribute('data-title');

    document.getElementById('delete-form').setAttribute('action', action);
    document.getElementById('delete-doc-title').textContent = title;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

// Submit delete dengan loading state
document.getElementById('delete-form').addEventListener('submit', function () {
    const btn = document.getElementById('delete-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Menghapus…';
});

// ── Close modals on Escape ────────────────────────────────────────────────────
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeUploadModal();
        closeEditModal();
        closeDeleteModal();
    }
});

// ── Restore state setelah validation error ────────────────────────────────────
(function () {
    const typeEl = document.getElementById('upload-type');
    if (typeEl && typeEl.value) {
        onTypeChange(typeEl.value);
    }

    @if ($errors->any() && old('_token'))
        openUploadModal();
    @endif
})();
</script>
@endpush