@php

$testimonials = [

    [
        'initial' => 'AF',
        'name' => 'Ahmad Fadil Nugroho',
        'company' => 'S1 Teknik Komputer',
        'text' => 'Magang di Telkom Indonesia sebagai software engineer intern adalah pengalaman yang luar biasa.'
    ],

    [
        'initial' => 'BM',
        'name' => 'Bunga Maharani',
        'company' => 'S1 Teknik Telekomunikasi',
        'text' => 'Magang di Huawei memberi saya eksposur terhadap teknologi jaringan 5G.'
    ],

    [
        'initial' => 'SA',
        'name' => 'Sekar Ayu',
        'company' => 'S1 Sistem Informasi',
        'text' => 'Kerja praktik di Gojek sebagai Product Analyst sangat menantang.'
    ],

    [
        'initial' => 'KS',
        'name' => 'Kevin Rynaldi Santoso',
        'company' => 'S1 Informatika',
        'text' => 'Kesempatan kerja praktik ini memberikan pengalaman nyata sebagai software engineer.'
    ]

];

@endphp

<section
id="testimoni"
    class="relative pt-10 pb-24 bg-[#F9EEF2] overflow-hidden"
>

    <div
        class="absolute left-1/2 bottom-0
               -translate-x-1/2
               w-[1200px] h-[500px]
               bg-[#F5E5EA]
               rounded-full
               opacity-70"
    ></div>

    <div class="relative z-10 max-w-7xl mx-auto px-8">

        <h3 class="text-2xl font-black uppercase">
            Semua Testimoni
        </h3>

        <p class="text-gray-500 mt-2">
            Dari mahasiswa aktif & alumni Telkom University Surabaya
        </p>

        <div class="grid lg:grid-cols-2 gap-8 mt-12">

            @foreach ($testimonials as $item)

                <div
                    class="relative bg-[#FFF8FA]
                           rounded-[28px]
                           border-2 border-[#2E2E2E]
                           p-8
                           overflow-hidden
                           hover:-translate-y-2
                           hover:shadow-xl
                           transition duration-300"
                >

                    <div
                        class="absolute -right-10 top-10
                               w-72 h-72
                               bg-[#F7E9EE]
                               rounded-full
                               opacity-80"
                    ></div>

                    <div class="relative z-10">

                        <div class="flex gap-5">

                            <div
                                class="w-[88px] h-[88px]
                                       rounded-full
                                       border-2 border-[#7A203A]
                                       flex items-center justify-center
                                       text-3xl font-bold"
                            >
                                {{ $item['initial'] }}
                            </div>

                            <div>

                                <h4 class="font-bold text-2xl">
                                    {{ $item['name'] }}
                                </h4>

                                <div class="text-yellow-400 mt-2">
                                    ★★★★★
                                </div>

                                <span
                                    class="inline-block mt-3
                                           px-4 py-1
                                           rounded-full
                                           border border-[#7A203A]
                                           text-sm"
                                >
                                    {{ $item['company'] }}
                                </span>

                            </div>

                        </div>

                        <p
                            class="mt-8
                                   text-center
                                   text-lg
                                   leading-relaxed
                                   text-gray-700"
                        >
                            "{{ $item['text'] }}"
                        </p>

                        <div
                            class="border-t mt-8 pt-4
                                   flex justify-between
                                   text-sm text-gray-600"
                        >

                            <span>
                                🏢 Telkom Indonesia
                            </span>

                            <span>
                                🗓 4 Bulan
                            </span>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</section>
