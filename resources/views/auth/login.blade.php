<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[var(--surface-2)]">
    <div class="flex h-screen w-screen items-center justify-center bg-[var(--surface-2)]">
        <form method="POST" action="{{ route('auth.login') }}" class="flex w-full max-w-sm flex-col gap-4 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-6">
            @csrf
            <div>
                <div class="text-[15px] font-semibold text-[var(--text-primary)]">People Analytics</div>
                <div class="text-[12px] text-[var(--text-muted)] mt-0.5">Sign in to view the HR &amp; workforce dashboard</div>
            </div>
            <label class="flex flex-col gap-1">
                <span class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Password</span>
                <input type="password" name="password" autofocus
                       class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px] text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]">
            </label>
            @error('password')
                <div class="text-[12px] text-[var(--status-critical)]">{{ $message }}</div>
            @enderror
            <button type="submit" class="rounded-md bg-[var(--series-1)] px-3 py-2 text-[13px] font-medium text-white">
                Sign in
            </button>
        </form>
    </div>
</body>
</html>
