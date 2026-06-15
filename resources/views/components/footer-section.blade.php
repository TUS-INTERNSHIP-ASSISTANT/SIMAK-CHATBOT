<footer>
    <!-- Main Footer -->
    <div class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 py-10 sm:py-16">
            <div class="grid grid-cols-3 gap-4 sm:gap-8 md:gap-12 items-start">

                <!-- Logo & Social Media -->
                <div class="min-w-0">
                    <img
                        src="{{ asset('assets/images/simak-logo-footer.svg') }}"
                        alt="SIMAK"
                        class="w-24 sm:w-40 md:w-64"
                    >

                    <p class="mt-2 text-[10px] sm:text-sm text-gray-600 leading-snug">
                        Sistem Informasi Magang dan Kerja Praktik
                    </p>

                    <div class="mt-4 sm:mt-8 flex flex-wrap gap-2 sm:gap-3">
                        <!-- Facebook -->
                        <a href="#"
                           class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-[#7A203A] flex items-center justify-center hover:scale-105 transition-transform duration-300"
                           aria-label="Facebook">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 sm:w-5 sm:h-5 text-white fill-current" aria-hidden="true">
                                <path d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.5 1.6-1.5H16.7V5c-.3 0-1.4-.1-2.6-.1-2.6 0-4.1 1.6-4.1 4.4V11H7.4v3H10v8h3.5z"/>
                            </svg>
                        </a>

                        <!-- Instagram -->
                        <a href="#"
                           class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-[#7A203A] flex items-center justify-center hover:scale-105 transition-transform duration-300"
                           aria-label="Instagram">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 sm:w-5 sm:h-5 text-white fill-current" aria-hidden="true">
                                <path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 0 1 12 7.5zm0 2A2.5 2.5 0 1 0 14.5 12 2.5 2.5 0 0 0 12 9.5zM17.75 6.2a1.05 1.05 0 1 1-1.05 1.05 1.05 1.05 0 0 1 1.05-1.05z"/>
                            </svg>
                        </a>

                        <!-- X / Twitter -->
                        {{-- <a href="#"
                           class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-[#7A203A] flex items-center justify-center hover:scale-105 transition-transform duration-300"
                           aria-label="X">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 sm:w-5 sm:h-5 text-white fill-current" aria-hidden="true">
                                <path d="M18.9 2H22l-6.8 7.8L23.2 22H17l-5-6.4L6.4 22H3.2l7.2-8.2L.8 2H7l4.6 5.8L18.9 2zm-1.1 18h1.8L6.1 3.9H4.2L17.8 20z"/>
                            </svg>
                        </a> --}}

                        <!-- Website / Browser -->
                        <a href="#"
                           class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-[#7A203A] flex items-center justify-center hover:scale-105 transition-transform duration-300"
                           aria-label="Website">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 sm:w-5 sm:h-5 text-white fill-current" aria-hidden="true">
                                <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm6.9 9h-3a15.7 15.7 0 0 0-1.1-5A8 8 0 0 1 18.9 11zM12 4c.7 1 1.5 2.8 1.9 7H10.1C10.5 6.8 11.3 5 12 4zM4 12a8 8 0 0 1 .1-1h3a15.7 15.7 0 0 0 0 2h-3A8 8 0 0 1 4 12zm1.1 3h3a15.7 15.7 0 0 0 1.1 5 8 8 0 0 1-4.1-5zM8.1 11h-3a8 8 0 0 1 4.1-5 15.7 15.7 0 0 0-1.1 5zm3.9 9c-.7-1-1.5-2.8-1.9-7h3.8c-.4 4.2-1.2 6-1.9 7zm2-2a15.7 15.7 0 0 0 1.1-5h3a8 8 0 0 1-4.1 5zM14 13h3a15.7 15.7 0 0 1-1.1 5 8 8 0 0 1-4.1-5H14zm-4 0c.4 4.2 1.2 6 2 7 .7-1 1.5-2.8 1.9-7H10z"/>
                            </svg>
                        </a>

                        <!-- YouTube -->
                        <a href="#"
                           class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-[#7A203A] flex items-center justify-center hover:scale-105 transition-transform duration-300"
                           aria-label="YouTube">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 sm:w-5 sm:h-5 text-white fill-current" aria-hidden="true">
                                <path d="M21.6 7.2a2.7 2.7 0 0 0-1.9-1.9C18 5 12 5 12 5s-6 0-7.7.3a2.7 2.7 0 0 0-1.9 1.9A28 28 0 0 0 2 12a28 28 0 0 0 .4 4.8 2.7 2.7 0 0 0 1.9 1.9C6 19 12 19 12 19s6 0 7.7-.3a2.7 2.7 0 0 0 1.9-1.9A28 28 0 0 0 22 12a28 28 0 0 0-.4-4.8zM10 15.2V8.8l5.5 3.2L10 15.2z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Tautan Penting -->
                <div class="min-w-0">
                    <h3 class="mb-3 text-sm sm:text-xl font-bold text-[#7A203A]">
                        Tautan Penting
                    </h3>

                    <ul class="space-y-2 sm:space-y-3 text-[11px] sm:text-sm text-gray-700 leading-snug">
                        <li><a href="#" class="hover:text-[#7A203A] transition">Pusat Teknologi Informasi</a></li>
                        <li><a href="#" class="hover:text-[#7A203A] transition">Service Desk</a></li>
                        <li><a href="#" class="hover:text-[#7A203A] transition">Bagian Administrasi Akademik</a></li>
                        <li><a href="#" class="hover:text-[#7A203A] transition">Kemahasiswaan</a></li>
                        <li><a href="#" class="hover:text-[#7A203A] transition">Career Development Center</a></li>
                    </ul>
                </div>

                <!-- Afiliasi -->
                <div class="min-w-0">
                    <h3 class="mb-3 text-sm sm:text-xl font-bold text-[#7A203A]">
                        Afiliasi
                    </h3>

                    <ul class="space-y-2 sm:space-y-3 text-[11px] sm:text-sm text-gray-700 leading-snug">
                        <li>Yayasan Pendidikan Telkom</li>
                        <li>Tel-U Bandung</li>
                        <li>Tel-U Jakarta</li>
                        <li>Tel-U Surabaya</li>
                        <li>Tel-U Purwokerto</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="bg-[#7A203A] py-3 sm:py-4 text-center text-xs sm:text-sm font-semibold text-white">
        © 2026 - Telkom University Surabaya
    </div>
</footer>
