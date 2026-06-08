<section
    class="py-24 bg-[#F9EEF2]"
>

    <div class="max-w-7xl mx-auto px-8">

        <!-- Heading -->
        <h3
            class="text-center
                   text-[#7A203A]
                   text-3xl
                   font-bold
                   uppercase"
        >
            Di Percaya Oleh Mitra Industri Kami
        </h3>

        <!-- Partner Logos -->
        @php
            $partners = [
                'gojek-logo.png',
                'huawei-logo.png',
                'shopee-logo.png',
                'telkom-indonesia-logo.png',
                'pln-logo.png',
                'dnet-logo.png',
                'grab-logo.png',
                'mandiri-logo.png',
            ];
        @endphp

        <div
            class="grid grid-cols-2
                   md:grid-cols-4
                   gap-8
                   mt-10"
        >

            @foreach ($partners as $logo)

                <div
                    class="bg-[#F9EEF2]
                           rounded-xl
                           h-28
                           p-4
                           flex items-center justify-center
                           transition duration-300"
                >

                    <img
                        src="{{ asset('assets/images/' . $logo) }}"
                        alt="Partner"
                        class="h-24 w-auto object-contain"
                    >

                </div>

            @endforeach

        </div>

    </div>

</section>
