<section
id="alur-program"
    class="bg-[#FAF7F8] py-24"
>

    <div class="max-w-7xl mx-auto px-8">

        <!-- Aksen -->
        <div
            class="w-20 h-1 bg-[#7A203A] mx-auto rounded-full mb-6"
        ></div>

        <!-- Heading -->
        <div class="text-center">

            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                Pahami Alur Program
            </h2>

            <p
                class="mt-4 text-gray-500 max-w-2xl mx-auto"
            >
                Mulai perjalanan Magang atau Kerja Praktik Anda dengan
                memahami setiap langkah yang perlu dipersiapkan.
            </p>

        </div>

        <!-- Tab + Content -->
        <div
            x-data="{ activeTab: 'magang' }"
            class="mt-12"
        >

            <!-- Toggle -->
            <div class="flex justify-center">

                <div
                    class="inline-flex p-1 bg-[#F5ECEF] rounded-xl gap-4"
                >

                    <button
                        @click="activeTab = 'magang'"
                        :class="activeTab === 'magang'
                            ? 'bg-[#7A203A] text-white'
                            : 'text-[#7A203A]'"
                        class="px-6 py-2 rounded-lg font-medium transition duration-300"
                    >
                        Magang
                    </button>

                    <button
                        @click="activeTab = 'kp'"
                        :class="activeTab === 'kp'
                            ? 'bg-[#7A203A] text-white'
                            : 'text-[#7A203A]'"
                        class="px-6 py-2 rounded-lg font-medium transition duration-300"
                    >
                        Kerja Praktik
                    </button>

                </div>

            </div>

            <!-- Card -->
            <div
                class="mt-10 bg-white rounded-[32px] p-8 md:p-12 shadow-[0_10px_40px_rgba(122,32,58,0.08)]"
            >

                <!-- Magang -->
                <div
                    x-show="activeTab === 'magang'"
                    x-transition
                >

                    <img
                        src="{{ asset('assets/images/alur-program-magang.png') }}"
                        alt="Alur Program Magang"
                        class="max-w-5xl w-full mx-auto"
                    >

                </div>

                <!-- Kerja Praktik -->
                <div
                    x-show="activeTab === 'kp'"
                    x-transition
                >

                    <img
                        src="{{ asset('assets/images/alur-program-kp.png') }}"
                        alt="Alur Program Kerja Praktik"
                        class="max-w-5xl w-full mx-auto"
                    >

                </div>

            </div>

        </div>

    </div>

</section>
