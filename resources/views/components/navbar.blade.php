<nav
    x-data="{ active: 'beranda' }"
    @scroll.window="
        let sections = ['beranda','tentang','alur-program','tanya-simak','testimoni'];

        sections.forEach(section => {
            let el = document.getElementById(section);

            if(el){
                let rect = el.getBoundingClientRect();

                if(rect.top <= 150 && rect.bottom >= 150){
                    active = section;
                }
            }
        });
    "
    class="sticky top-0 z-50 bg-white/95 backdrop-blur-md shadow-sm"
>

    <div class="max-w-8xl mx-auto px-10">

        <div class="h-24 flex items-center justify-between">

            <!-- Logo -->
            <img
                src="{{ asset('assets/images/logo-simak.svg') }}"
                alt="SIMAK"
                class="h-18"
            >

            <!-- Menu -->
            <ul class="flex items-center gap-12 font-medium">

                <li>
                    <a
                        href="#beranda"
                        :class="active === 'beranda'
                            ? 'text-[#7A203A] after:w-full'
                            : 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300
                               hover:text-[#7A203A]
                               after:content-['']
                               after:absolute
                               after:left-0
                               after:-bottom-1
                               after:h-[2px]
                               after:w-0
                               after:bg-[#7A203A]
                               after:transition-all
                               after:duration-300
                               hover:after:w-full"
                    >
                        Beranda
                    </a>
                </li>

                <li>
                    <a
                        href="#tentang"
                        :class="active === 'tentang'
                            ? 'text-[#7A203A] after:w-full'
                            : 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300
                               hover:text-[#7A203A]
                               after:content-['']
                               after:absolute
                               after:left-0
                               after:-bottom-1
                               after:h-[2px]
                               after:w-0
                               after:bg-[#7A203A]
                               after:transition-all
                               after:duration-300
                               hover:after:w-full"
                    >
                        Tentang
                    </a>
                </li>

                <li>
                    <a
                        href="#alur-program"
                        :class="active === 'alur-program'
                            ? 'text-[#7A203A] after:w-full'
                            : 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300
                               hover:text-[#7A203A]
                               after:content-['']
                               after:absolute
                               after:left-0
                               after:-bottom-1
                               after:h-[2px]
                               after:w-0
                               after:bg-[#7A203A]
                               after:transition-all
                               after:duration-300
                               hover:after:w-full"
                    >
                        Alur Program
                    </a>
                </li>

                <li>
                    <a
                        href="#tanya-simak"
                        :class="active === 'tanya-simak'
                            ? 'text-[#7A203A] after:w-full'
                            : 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300
                               hover:text-[#7A203A]
                               after:content-['']
                               after:absolute
                               after:left-0
                               after:-bottom-1
                               after:h-[2px]
                               after:w-0
                               after:bg-[#7A203A]
                               after:transition-all
                               after:duration-300
                               hover:after:w-full"
                    >
                        Tanya SIMAK
                    </a>
                </li>

                <li>
                    <a
                        href="#testimoni"
                        :class="active === 'testimoni'
                            ? 'text-[#7A203A] after:w-full'
                            : 'text-gray-800'"
                        class="relative pb-2 transition-all duration-300
                               hover:text-[#7A203A]
                               after:content-['']
                               after:absolute
                               after:left-0
                               after:-bottom-1
                               after:h-[2px]
                               after:w-0
                               after:bg-[#7A203A]
                               after:transition-all
                               after:duration-300
                               hover:after:w-full"
                    >
                        Testimoni
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>
