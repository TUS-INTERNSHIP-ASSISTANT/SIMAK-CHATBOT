<section
    id="tanya-simak"
    class="py-24 bg-[#FAF7F8]"
>

    <div
        class="w-20 h-1
               bg-[#7A203A]
               mx-auto
               rounded-full
               mb-6"
    ></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-8">

        <!-- Heading -->
        <div class="text-center">

            <h2 class="text-4xl font-bold">
                Masih Ada Pertanyaan?
            </h2>

            <p class="mt-4 text-gray-600 max-w-3xl mx-auto">
                Dapatkan informasi mengenai Magang dan Kerja Praktik
                secara cepat melalui chatbot berbasis AI yang siap
                membantu menjawab pertanyaan Anda kapan saja.
            </p>

        </div>

        <!-- Chat Preview -->
        <div
            class="mt-16
                   bg-white
                   border border-[#B77A8A]
                   rounded-[28px]
                   overflow-hidden"
        >

            <!-- Header -->
            <div
                class="bg-[#7A203A]
                       px-6 py-4
                       flex items-center gap-3"
            >

                <div
                    class="w-10 h-10
                           bg-white
                           rounded-full
                           flex items-center justify-center"
                >

                    <img
                        src="{{ asset('assets/images/robot-preview.png') }}"
                        alt="Robot Icon"
                        class="w-6"
                    >

                </div>

                <span class="text-white font-semibold">
                    Asisten Virtual SIMAK
                </span>

            </div>

            <!-- Body -->
            <div
                class="flex flex-col
                    items-center justify-center
                    py-10 sm:py-16
                    px-4 sm:px-8"
            >

                <a
                    href="/chatbot"
                    class="group"
                >

                    <img
                        src="{{ asset('assets/images/robot-preview.png') }}"
                        alt="Robot"
                        class="w-40 opacity-60
                               cursor-pointer
                               transition duration-300
                               group-hover:scale-110"
                    >

                </a>

                <h3
                    class="mt-6
                           text-xl font-bold
                           text-center"
                >
                    Halo! 👋 Saya Asisten SIMAK.
                </h3>

                <p
                    class="mt-3
                           text-center
                           text-gray-600
                           max-w-lg"
                >
                    Silakan ketik pertanyaan Anda atau pilih salah
                    satu pertanyaan yang sering diajukan di bawah ini.
                </p>

                <a
                    href="/chatbot"
                    class="mt-6
                           inline-flex items-center gap-2
                           bg-[#7A203A]
                           text-white
                           px-6 py-3
                           rounded-xl
                           hover:shadow-lg
                           hover:-translate-y-1
                           transition duration-300"
                >
                    💬 Mulai Bertanya
                </a>

                <!-- Quick Questions -->
                <div
                    class="flex flex-wrap
                           justify-center
                           gap-4
                           mt-10"
                >

                        <button
                            class="w-full sm:w-auto
                                bg-white border
                                shadow-sm
                                px-5 py-3
                                rounded-xl
                                hover:shadow-md
                                transition"
                        >
                    >
                        Apa saja syarat Kerja Praktik?
                    </button>

                    <button
                        class="bg-white border
                               shadow-sm
                               px-5 py-3
                               rounded-xl
                               hover:shadow-md
                               transition"
                    >
                        Berapa durasi Kerja Praktik?
                    </button>

                    <button
                        class="bg-white border
                               shadow-sm
                               px-5 py-3
                               rounded-xl
                               hover:shadow-md
                               transition"
                    >
                        Bagaimana alur seminar KP?
                    </button>

                    <button
                        class="bg-white border
                               shadow-sm
                               px-5 py-3
                               rounded-xl
                               hover:shadow-md
                               transition"
                    >
                        Bagaimana prosedur pengajuan Magang?
                    </button>

                </div>

            </div>

            <!-- Footer Input -->
            <div
                class="border-t
                    px-4 py-4
                    flex flex-col sm:flex-row
                    gap-3"
            >

                <input
                    type="text"
                    placeholder="Tanya seputar program..."
                    class="flex-1
                           border
                           rounded-xl
                           px-4 py-3
                           outline-none
                           focus:ring-2
                           focus:ring-[#7A203A]"
                >

                <button
                    class="bg-[#7A203A]
                        text-white
                        px-6 py-3
                        rounded-xl
                        whitespace-nowrap
                        hover:opacity-90
                        transition"
                >
                    Kirim
                </button>

            </div>

            <!-- Disclaimer -->
            <div class="flex justify-center pb-4 px-6">

                <p
                    class="text-xs
                           text-gray-400
                           text-center
                           max-w-3xl"
                >
                    Asisten Virtual SIMAK dapat memberikan informasi yang
                    kurang akurat. Pastikan kembali persyaratan dan prosedur
                    melalui sumber resmi SSC.
                </p>

            </div>

        </div>

    </div>

</section>
