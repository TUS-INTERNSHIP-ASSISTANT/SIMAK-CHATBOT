<nav
    x-data="{ active: 'beranda', open: false }"
    @scroll.window="
        let sections = ['beranda','tentang','alur-program','tanya-simak','testimoni'];

        sections.forEach(section => {
            let el = document.getElementById(section);

            if (el) {
                let rect = el.getBoundingClientRect();

                if (rect.top <= 150 && rect.bottom >= 150) {
                    active = section;
                }
            }
        });
    "
    class="fixed top-0 z-50 w-full bg-white/95 backdrop-blur-md shadow-sm"
>
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-10">
        <div class="h-20 flex items-center justify-between">
            <!-- Logo -->
            <img
                src="{{ asset('assets/images/logo-simak.svg') }}"
                alt="SIMAK"
                class="h-10 w-auto sm:h-14 md:h-16"
            >

            <!-- Desktop Menu -->
            <ul class="hidden md:flex items-center gap-12 font-medium">
                <li>
                    <a
                        href="#beranda"
                        @click="open = false"
                        :class="active === 'beranda'? 'text-[#7A203A] after:w-full': 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300 hover:text-[#7A203A] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-[#7A203A] after:transition-all after:duration-300 hover:after:w-full"
                    >
                        Beranda
                    </a>
                </li>

                <li>
                    <a
                        href="#tentang"
                        @click="open = false"
                        :class="active === 'tentang'? 'text-[#7A203A] after:w-full': 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300 hover:text-[#7A203A] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-[#7A203A] after:transition-all after:duration-300 hover:after:w-full"
                    >
                        Tentang
                    </a>
                </li>

                <li>
                    <a
                        href="#alur-program"
                        @click="open = false"
                        :class="active === 'alur-program'? 'text-[#7A203A] after:w-full': 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300 hover:text-[#7A203A] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-[#7A203A] after:transition-all after:duration-300 hover:after:w-full"
                    >
                        Alur Program
                    </a>
                </li>

                <li>
                    <a
                        href="#tanya-simak"
                        @click="open = false"
                        :class="active === 'tanya-simak'? 'text-[#7A203A] after:w-full': 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300 hover:text-[#7A203A] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-[#7A203A] after:transition-all after:duration-300 hover:after:w-full"
                    >
                        Tanya SIMAK
                    </a>
                </li>

                {{-- <li>
                    <a
                        href="#testimoni"
                        @click="open = false"
                        :class="active === 'testimoni'? 'text-[#7A203A] after:w-full': 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300 hover:text-[#7A203A] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:h-[2px] after:w-0 after:bg-[#7A203A] after:transition-all after:duration-300 hover:after:w-full"
                    >
                        Testimoni
                    </a>
                </li> --}}
            </ul>

            <!-- Mobile Hamburger -->
            <button
                @click="open = true"
                class="md:hidden inline-flex items-center justify-center rounded-lg p-2 text-gray-800 hover:bg-gray-100"
                aria-label="Open menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Overlay -->
    <div
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="fixed inset-0 z-40 bg-black/40 md:hidden"
        style="display: none;"
    ></div>

    <!-- Mobile Sidebar -->
    <aside
        x-show="open"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 z-50 h-screen w-1/2 bg-white shadow-2xl md:hidden"
        style="display: none;"
    >
        <div class="flex h-20 items-center justify-between border-b px-5">
            <span class="font-semibold text-[#7A203A]">Menu</span>

            <button
                @click="open = false"
                class="rounded-lg p-2 hover:bg-gray-100"
                aria-label="Close menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <ul class="flex flex-col gap-2 p-5 font-medium">
            <li>
                <a
                    href="#beranda"
                    @click="open = false"
                    :class="active === 'beranda' ? 'text-[#7A203A] bg-[#7A203A]/5' : 'text-gray-800'"
                    class="block rounded-lg px-4 py-3 transition hover:bg-gray-100"
                >
                    Beranda
                </a>
            </li>

            <li>
                <a
                    href="#tentang"
                    @click="open = false"
                    :class="active === 'tentang' ? 'text-[#7A203A] bg-[#7A203A]/5' : 'text-gray-800'"
                    class="block rounded-lg px-4 py-3 transition hover:bg-gray-100"
                >
                    Tentang
                </a>
            </li>

            <li>
                <a
                    href="#alur-program"
                    @click="open = false"
                    :class="active === 'alur-program' ? 'text-[#7A203A] bg-[#7A203A]/5' : 'text-gray-800'"
                    class="block rounded-lg px-4 py-3 transition hover:bg-gray-100"
                >
                    Alur Program
                </a>
            </li>

            <li>
                <a
                    href="#tanya-simak"
                    @click="open = false"
                    :class="active === 'tanya-simak' ? 'text-[#7A203A] bg-[#7A203A]/5' : 'text-gray-800'"
                    class="block rounded-lg px-4 py-3 transition hover:bg-gray-100"
                >
                    Tanya SIMAK
                </a>
            </li>

            <li>
                <a
                    href="#testimoni"
                    @click="open = false"
                    :class="active === 'testimoni' ? 'text-[#7A203A] bg-[#7A203A]/5' : 'text-gray-800'"
                    class="block rounded-lg px-4 py-3 transition hover:bg-gray-100"
                >
                    Testimoni
                </a>
            </li>
        </ul>
    </aside>
</nav>
