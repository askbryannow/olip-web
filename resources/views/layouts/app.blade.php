<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[var(--surface-2)]">
    <div class="flex h-screen w-screen overflow-hidden bg-[var(--surface-2)]">
        <x-sidebar />
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <livewire:filter-bar />
            <main class="flex-1 overflow-y-auto scrollbar-thin px-5 py-5">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
