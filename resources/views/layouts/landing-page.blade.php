<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMAK AI</title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
])

<body

    class="bg-white flex flex-col min-h-screen">

    <main class="flex-1">

        <x-navbar />

        <x-home-section />

        <x-about-section />

        <x-program-flow-section />

        <x-benefit-section />

        <x-chatbot-preview-section />

        {{-- <x-achievement-section />

        <x-testimonial-highlight-section />

        <x-testimonial-section /> --}}

        <!-- Partners -->
       {{-- <x-partner-section /> --}}


    </main>

    <!-- Footer -->
    <x-footer-section />

</body>
