<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    @livewireStyles

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>

<body class="font-sans antialiased">
    <x-jet-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            Alpineテスト
            <div x-data="{ open:true}">
                <div x-show="open">openがtrue</div>
                <div x-show="!open">openがfalse</div>
                <button class="px-4 py-2 bg-blue-400 text-white" x-on:click="open = !open">ボタン</button>
            </div>
            {{-- 効かなくなる --}}
            {{-- <div x-show="open">openがtrue</div>
                <div x-show="!open">openがfalse</div> --}}
        </main>
    </div>

    @stack('modals')

    @livewireScripts
</body>

</html>
