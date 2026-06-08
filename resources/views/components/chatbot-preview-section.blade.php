<section id="tanya-simak" class="py-24 bg-[#FAF7F8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <div class="w-20 h-1 bg-[#7A203A] mx-auto rounded-full mb-6"></div>

            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                Masih Ada Pertanyaan?
            </h2>

            <p class="mt-4 text-gray-600 max-w-3xl mx-auto text-base md:text-lg leading-relaxed">
                Dapatkan informasi mengenai Magang dan Kerja Praktik secara cepat melalui chatbot berbasis AI
                yang siap membantu menjawab pertanyaan Anda kapan saja.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 items-stretch">
            <!-- Left Content -->
            <div class="flex flex-col justify-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#7A203A]/10 text-[#7A203A] w-fit mb-5">
                    <span class="text-sm font-medium">Asisten Virtual SIMAK</span>
                </div>

                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">
                    Tanya SIMAK dan temukan jawaban lebih cepat.
                </h3>

                <p class="mt-4 text-gray-600 leading-relaxed max-w-xl">
                    Silakan ketik pertanyaan Anda atau pilih pertanyaan yang sering diajukan di bawah ini untuk
                    langsung memulai percakapan dengan SIMAK.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a
                        href="#tanya-simak"
                        class="inline-flex items-center gap-2 bg-[#7A203A] text-white px-6 py-3 rounded-xl font-medium shadow-[0_4px_4px_rgba(205,108,108,0.25)] hover:bg-[#5A182C] hover:-translate-y-0.5 transition-all duration-300"
                    >
                        Mulai Bertanya
                    </a>

                    <a
                        href="#alur-program"
                        class="inline-flex items-center gap-2 bg-white text-[#7A203A] border border-[#7A203A]/20 px-6 py-3 rounded-xl font-medium hover:border-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300"
                    >
                        Lihat Alur Program
                    </a>
                </div>

                <div class="mt-8 grid sm:grid-cols-3 gap-4 max-w-xl">
                    <div class="rounded-2xl bg-white border border-gray-100 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-gray-900">Cepat</p>
                        <p class="mt-1 text-sm text-gray-500">Jawaban lebih ringkas dan praktis.</p>
                    </div>

                    <div class="rounded-2xl bg-white border border-gray-100 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-gray-900">Efisien</p>
                        <p class="mt-1 text-sm text-gray-500">Berbasis dokumen yang tersedia.</p>
                    </div>

                    <div class="rounded-2xl bg-white border border-gray-100 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-gray-900">24/7</p>
                        <p class="mt-1 text-sm text-gray-500">Bisa diakses kapan saja.</p>
                    </div>
                </div>
            </div>

            <!-- Right Chat Preview -->
            <div class="bg-white border border-[#B77A8A]/30 rounded-[32px] shadow-[0_20px_60px_rgba(122,32,58,0.08)] overflow-hidden">
                <!-- Chat Header -->
                <div class="bg-gradient-to-r from-[#7A203A] to-[#9B2E4A] px-5 sm:px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white rounded-full flex items-center justify-center shadow-sm">
                            <img
                                src="{{ asset('assets/images/robot-preview.png') }}"
                                alt="Robot Icon"
                                class="w-7 h-7 object-contain"
                            >
                        </div>

                        <div>
                            <p class="text-white font-semibold leading-tight">Asisten Virtual SIMAK</p>
                            <p class="text-white/80 text-sm">Online • Siap membantu</p>
                        </div>
                    </div>

                    <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full bg-white/15 text-white text-xs font-medium">
                        AI Chatbot
                    </span>
                </div>

                <!-- Chat Body -->
                <div class="bg-[#FCFAFB] px-4 sm:px-6 py-6">
                    <div class="space-y-4 max-h-[420px] overflow-y-auto pr-1">
                        <!-- Bot message -->
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm">
                                <img
                                    src="{{ asset('assets/images/robot-preview.png') }}"
                                    alt="Bot"
                                    class="w-5 h-5 object-contain"
                                >
                            </div>

                            <div class="max-w-[85%]">
                                <div class="rounded-2xl rounded-tl-sm bg-white border border-gray-100 px-4 py-3 shadow-sm">
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        Halo! 👋 Saya Asisten SIMAK. Silakan tanyakan seputar Magang dan Kerja Praktik.
                                    </p>
                                </div>
                                <p class="mt-1 text-[11px] text-gray-400">SIMAK • 09.00</p>
                            </div>
                        </div>

                        <!-- User message -->
                        <div class="flex items-start justify-end gap-3">
                            <div class="max-w-[85%]">
                                <div class="rounded-2xl rounded-tr-sm bg-[#7A203A] px-4 py-3 shadow-sm">
                                    <p class="text-sm text-white leading-relaxed">
                                        Apa saja syarat Kerja Praktik?
                                    </p>
                                </div>
                                <p class="mt-1 text-[11px] text-gray-400 text-right">Anda • 09.01</p>
                            </div>
                        </div>

                        <!-- Bot message -->
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm">
                                <img
                                    src="{{ asset('assets/images/robot-preview.png') }}"
                                    alt="Bot"
                                    class="w-5 h-5 object-contain"
                                >
                            </div>

                            <div class="max-w-[85%]">
                                <div class="rounded-2xl rounded-tl-sm bg-white border border-gray-100 px-4 py-3 shadow-sm">
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        Untuk syarat Kerja Praktik, mahasiswa umumnya perlu memenuhi ketentuan akademik tertentu dan menyiapkan dokumen pengajuan sesuai pedoman.
                                    </p>
                                </div>

                                <div class="mt-2 flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-[#7A203A]/10 text-[#7A203A] text-[11px] font-medium">
                                        Sumber dokumen
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-[11px] font-medium">
                                        Pedoman KP
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Questions -->
                    <div class="mt-6">
                        <p class="text-sm font-semibold text-gray-800 mb-3">Pertanyaan</p>

                        <div class="flex flex-wrap gap-3">
                            <button
                                type="button"
                                class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300"
                            >
                                Apa saja syarat Kerja Praktik?
                            </button>

                            <button
                                type="button"
                                class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300"
                            >
                                Berapa durasi Kerja Praktik?
                            </button>

                            <button
                                type="button"
                                class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300"
                            >
                                Bagaimana alur seminar KP?
                            </button>

                            <button
                                type="button"
                                class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300"
                            >
                                Bagaimana prosedur pengajuan Magang?
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Input -->
                <div class="border-t border-gray-100 bg-white px-4 sm:px-6 py-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input
                            type="text"
                            placeholder="Tanya seputar Magang dan Kerja Praktik..."
                            class="flex-1 rounded-xl border border-gray-200 bg-[#FCFAFB] px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition"
                        >

                        <button
                            class="inline-flex items-center justify-center gap-2 bg-[#7A203A] text-white px-6 py-3 rounded-xl font-medium hover:bg-[#5A182C] shadow-[0_4px_4px_rgba(205,108,108,0.25)] transition-all duration-300 whitespace-nowrap"
                        >
                            Kirim
                        </button>
                    </div>

                    <p class="mt-3 text-[11px] sm:text-xs text-gray-400 text-center leading-relaxed">
                        SIMAK dapat membuat kesalahan. Pastikan kembali informasi penting melalui SSC.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
