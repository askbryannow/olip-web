<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BLIP — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[var(--surface-2)]">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-[var(--border-hairline)] bg-[var(--surface-1)]">
            <div class="mx-auto flex max-w-3xl items-center justify-between px-5 py-3">
                <a href="{{ route('portal.passport') }}" class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-md bg-[var(--series-1)] text-[13px] font-bold text-white">B</span>
                    <span>
                        <span class="block text-[13px] font-semibold leading-tight text-[var(--text-primary)]">BLIP</span>
                        <span class="block text-[10px] leading-tight text-[var(--text-muted)]">Leadership Passport Academy</span>
                    </span>
                </a>
                @auth('employee')
                    <div class="flex items-center gap-3">
                        <span class="hidden text-[12px] text-[var(--text-secondary)] sm:inline">{{ Auth::guard('employee')->user()->name }}</span>
                        <form method="POST" action="{{ route('portal.logout') }}">
                            @csrf
                            <button type="submit" class="rounded-md px-3 py-1.5 text-[12px] font-medium text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">Sign out</button>
                        </form>
                    </div>
                @endauth
            </div>
        </header>
        <main class="mx-auto w-full max-w-3xl flex-1 px-5 py-8">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
