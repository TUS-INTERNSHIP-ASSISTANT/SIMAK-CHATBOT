<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMAK - Sistem Informasi Magang dan Kerja Praktik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        @include('review.components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <main class="flex-1">
                @yield('content')
            </main>
        </div>

    </div>
</body>
</html>
