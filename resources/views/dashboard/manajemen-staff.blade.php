@extends('dashboard.layouts.app')

@section('page-title', 'Manajemen Staff')
@section('mobile-title', 'Manajemen Staff')
@section('breadcrumb', 'Akun')
@section('page-heading', 'Manajemen Staff')
@section('page-subheading', 'Kelola akun staff yang memiliki akses ke portal ini.')

@section('content')
    {{-- ─── Flash Messages ─────────────────────────────────────────────────────── --}}
    @if (session('success'))
        <div id="flash-success"
            class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium max-w-4xl">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
            <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div id="flash-error"
            class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium max-w-4xl">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                @foreach ($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </div>
            <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-red-400 hover:text-red-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 max-w-4xl">
        <form id="staff-form" action="{{ route('dashboard.manajemen-staff.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Nama Lengkap -->
            <div class="space-y-2">
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', auth()->user()->name) }}" required
                    class="w-full sm:w-2/3 md:w-1/2 px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] text-sm text-gray-800 transition-all duration-300">
            </div>

            <!-- Email (Read-only / Disabled) -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">Email (Tidak Dapat Diubah)</label>
                <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" disabled 
                    class="w-full sm:w-2/3 md:w-1/2 px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-400 cursor-not-allowed text-sm transition-all duration-300">
            </div>

            <!-- Password Baru -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password baru jika ingin mengubah"
                    class="w-full sm:w-2/3 md:w-1/2 px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] text-sm text-gray-800 transition-all duration-300">
                <p class="text-xs text-gray-400 mt-1">Kosongkan kolom ini jika Anda tidak ingin mengubah password.</p>
            </div>

            <!-- Konfirmasi Password Baru -->
            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru"
                    class="w-full sm:w-2/3 md:w-1/2 px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] text-sm text-gray-800 transition-all duration-300">
            </div>

            <!-- Tanggal Bergabung -->
            <div class="space-y-1 pt-2">
                <p class="text-sm font-medium text-gray-700">Tanggal Bergabung</p>
                <p class="text-sm text-gray-800 font-semibold">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d M Y') : '-' }}</p>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="button" onclick="openConfirmModal()" class="bg-[#7A203A] hover:bg-[#5A182C] text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- CONFIRM MODAL                                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="confirm-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
             onclick="closeConfirmModal()"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all duration-300 scale-95 opacity-0"
             id="confirm-modal-panel">
            <div class="p-6 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center mb-4 text-[#7A203A]">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h2 id="confirm-modal-title" class="text-base font-bold text-gray-900 mb-1">Simpan Perubahan?</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Apakah Anda yakin ingin menyimpan perubahan data profil Anda? Tindakan ini akan langsung memperbarui identitas Anda di dashboard.
                </p>
                <div class="flex gap-3">
                    <button type="button"
                        onclick="closeConfirmModal()"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                        onclick="submitStaffForm()"
                        id="confirm-submit-btn"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-[#7A203A] text-white text-sm font-semibold hover:bg-[#5A182C] active:scale-95 transition-all cursor-pointer">
                        Ya, Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openConfirmModal() {
        // Run native validation checks
        const form = document.getElementById('staff-form');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Validate password match if password is filled
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        if (password) {
            if (password.length < 8) {
                alert('Password minimal harus 8 karakter.');
                return;
            }
            if (password !== passwordConfirm) {
                alert('Konfirmasi password tidak cocok.');
                return;
            }
        }

        const modal = document.getElementById('confirm-modal');
        const panel = document.getElementById('confirm-modal-panel');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeConfirmModal() {
        const modal = document.getElementById('confirm-modal');
        const panel = document.getElementById('confirm-modal-panel');
        
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function submitStaffForm() {
        const btn = document.getElementById('confirm-submit-btn');
        btn.disabled = true;
        btn.textContent = 'Menyimpan…';
        document.getElementById('staff-form').submit();
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeConfirmModal();
        }
    });
</script>
@endpush
