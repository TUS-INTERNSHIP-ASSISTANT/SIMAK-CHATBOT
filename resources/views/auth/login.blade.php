<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Staff Portal</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-[#F8F4F5]">
    <div class="min-h-screen flex">

        {{-- sisi kiri --}}
        <div class="hidden md:flex w-1/2 bg-[#7A2034] items-center justify-center p-10">

            {{-- robot image --}}
            <img src=" {{ asset('assets/images/robot-login.png') }}" alt="Robot SIMAK" class="max-w-md w-90 object-contain">
        </div>

        <!-- sisi kanan -->
        <div class="w-full md:w-1/2 flex items-center justify-center px-6 py-10">

            <div class="w-full max-w-md">

                <!-- LOGO -->
                <div class="flex justify-center">

                    <!-- GANTI DENGAN LOGO SIMAK -->
                    <img src="{{ asset('assets/images/simak-logo-transparent.png') }}" alt="SIMAK" class="h-18 w-auto">

                </div>

                <!-- TITLE -->
                <div class="text-center mt-8">
                    <h1 class="text-4xl font-bold text-[#7A203A]">
                        Portal Staff
                    </h1>

                    <p class="mt-4 text-gray-700 text-lg">
                        Akses dashboard untuk mengelola dokumen
                        dan informasi layanan.
                    </p>

                </div>

                <!-- Divider -->
                <div class="w-full h-px bg-gray-300 my-8"></div>

                <!-- FORM -->
                <form class="space-y-6" x-data="loginForm()" @submit.prevent="submit">

                    <!-- Alert Error -->
                    <div x-show="errorMessage" style="display: none;" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline" x-text="errorMessage"></span>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block mb-2 font-semibold text-[#7A203A]">
                            Email Staff
                        </label>

                        <input type="email" x-model="email" required class="w-full h-14 px-5 rounded-2xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A203A]">
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block mb-2 font-semibold text-[#7A203A]">
                            Password
                        </label>

                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" x-model="password" required class="w-full h-14 px-5 pr-12 rounded-2xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A203A]">

                            <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-[#7A203A] transition">

                                <!-- Eye -->
                                <svg
                                    x-show="!showPassword"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-5 h-5"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>

                                <!-- Eye Slash -->
                                <svg
                                    x-show="showPassword"
                                    style="display: none;"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-5 h-5"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m3 3 18 18" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.73 5.08A10.45 10.45 0 0 1 12 4.5c6 0 9.75 7.5 9.75 7.5a18.92 18.92 0 0 1-2.62 3.53M6.61 6.61C4.25 8.27 2.25 12 2.25 12s3.75 7.5 9.75 7.5a9.7 9.7 0 0 0 4.29-.96" />
                                </svg>

                            </button>

                        </div>
                    </div>

                    <!-- Button -->
                    <div class="pt-4 flex justify-center">
                        <button type="submit" :disabled="isLoading" class="w-48 h-12 rounded-2xl border border-[#7A203A] text-[#7A203A] font-semibold hover:bg-[#7A203A] hover:text-white transition-all duration-300 disabled:opacity-50">
                            <span x-show="!isLoading">Masuk</span>
                            <span x-show="isLoading" style="display: none;">Loading...</span>
                        </button>
                    </div>
                </form>

                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('loginForm', () => ({
                            email: '',
                            password: '',
                            showPassword: false,
                            errorMessage: '',
                            isLoading: false,

                            async submit() {
                                this.isLoading = true;
                                this.errorMessage = '';
                                try {
                                    const response = await axios.post('/api/login', {
                                        email: this.email,
                                        password: this.password
                                    });
                                    
                                    // Save the API token to localStorage
                                    localStorage.setItem('access_token', response.data.access_token);
                                    
                                    // Redirect to the dashboard
                                    window.location.href = '/dashboard';
                                } catch (error) {
                                    if (error.response && error.response.status === 401) {
                                        this.errorMessage = 'Kredensial salah. Pastikan email dan password benar.';
                                    } else {
                                        this.errorMessage = 'Terjadi kesalahan pada server.';
                                    }
                                } finally {
                                    this.isLoading = false;
                                }
                            }
                        }));
                    });
                </script>
            </div>
        </div>

    </div>
</body>
</html>
