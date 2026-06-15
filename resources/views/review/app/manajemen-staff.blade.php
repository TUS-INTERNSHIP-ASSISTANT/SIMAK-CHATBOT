@extends('layouts.app')

@section('content')
<div class="px-8 py-6 h-full flex flex-col">
    <!-- Header -->
    <header class="mb-8">
        <p class="text-xs text-gray-400 mb-1">Selasa, 10 Juni 2026</p>
        <h2 class="text-lg font-semibold text-gray-800">Good Morning, Admin!</h2>
    </header>

    <!-- Page Title -->
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-800">Manajemen Staff</h3>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg border border-[#7a2035]/20 shadow-sm flex-1 p-8 max-w-4xl">
        <form action="#" method="POST" class="space-y-6">
            @csrf
            
            <!-- Nama Lengkap -->
            <div class="space-y-2">
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="Staff Akademik" 
                    class="w-full sm:w-2/3 md:w-1/2 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-[#9b1c31] focus:border-[#9b1c31] text-sm text-gray-800">
            </div>

            <!-- Email -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="staff@simak.com" 
                    class="w-full sm:w-2/3 md:w-1/2 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-[#9b1c31] focus:border-[#9b1c31] text-sm text-gray-800">
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Passowrd</label>
                <input type="password" id="password" name="password" value="***" 
                    class="w-full sm:w-2/3 md:w-1/2 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-[#9b1c31] focus:border-[#9b1c31] text-sm text-gray-800">
            </div>

            <!-- Tanggal Bergabung -->
            <div class="space-y-1 pt-2">
                <p class="text-sm font-medium text-gray-700">Tanggal Bergabung</p>
                <p class="text-sm text-gray-800">14/06/2026</p>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="bg-[#7a2035] hover:bg-[#5a1525] text-white px-6 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
