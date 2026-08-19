<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — BLIP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[var(--surface-2)]">
    <div class="flex min-h-screen w-screen items-center justify-center bg-[var(--surface-2)] px-4">
        <form method="POST" action="{{ route('portal.login.submit') }}" class="flex w-full max-w-sm flex-col gap-4 rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-6">
            @csrf
            <div>
                <span class="flex h-8 w-8 items-center justify-center rounded-md bg-[var(--series-1)] text-[14px] font-bold text-white">B</span>
                <div class="mt-2 text-[15px] font-semibold text-[var(--text-primary)]">BLIP Leadership Passport</div>
                <div class="text-[12px] text-[var(--text-muted)] mt-0.5">Sign in to see your stamp collection</div>
            </div>
            <label class="flex flex-col gap-1">
                <span class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" autofocus
                       class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px] text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]">
            </label>
            @error('email')
                <div class="text-[12px] text-[var(--status-critical)]">{{ $message }}</div>
            @enderror
            <label class="flex flex-col gap-1">
                <span class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Password</span>
                <input type="password" name="password"
                       class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px] text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--series-1)]">
            </label>
            <button type="submit" class="rounded-md bg-[var(--series-1)] px-3 py-2 text-[13px] font-medium text-white">
                Sign in
            </button>
            <div class="text-center text-[12px] text-[var(--text-muted)]">
                New to BLIP? <a href="{{ route('portal.register') }}" class="font-medium text-[var(--series-1)] hover:underline">Set up your account</a>
            </div>
        </form>
    </div>
</body>
</html>
