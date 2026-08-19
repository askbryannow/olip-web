<div class="mx-auto max-w-md">
    <x-portal-steps :steps="['Your outlet', 'Your name', 'Set login']" :current="$step" />

    <div class="rounded-lg border border-[var(--border-hairline)] bg-[var(--surface-card)] p-6">
        @if ($step === 1)
            <div wire:key="step-1">
            <h1 class="text-[16px] font-semibold text-[var(--text-primary)]">Where do you work?</h1>
            <p class="mt-1 text-[13px] text-[var(--text-muted)]">We'll match you to your existing employee record.</p>

            <select wire:model.live="outletId" class="mt-4 w-full rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px]">
                <option value="">Select your outlet…</option>
                @foreach ($outlets as $o)
                    <option value="{{ $o->id }}">{{ $o->name }}</option>
                @endforeach
            </select>

            @if ($error)
                <div class="mt-3 text-[12px] text-[var(--status-critical)]">{{ $error }}</div>
            @endif

            <button wire:click="chooseOutlet" class="mt-5 w-full rounded-md bg-[var(--series-1)] px-3 py-2 text-[13px] font-medium text-white">Continue</button>
            </div>
        @elseif ($step === 2)
            <div wire:key="step-2">
            <h1 class="text-[16px] font-semibold text-[var(--text-primary)]">Which name is yours?</h1>
            <p class="mt-1 text-[13px] text-[var(--text-muted)]">If you don't see your name, it may already be registered, or ask HR to check your record.</p>

            <select wire:model="employeeId" class="mt-4 w-full rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px]">
                <option value="">Select your name…</option>
                @foreach ($employees as $e)
                    <option value="{{ $e->id }}">{{ $e->name }} — {{ $e->department }}</option>
                @endforeach
            </select>

            @if ($error)
                <div class="mt-3 text-[12px] text-[var(--status-critical)]">{{ $error }}</div>
            @endif

            <div class="mt-5 flex gap-2">
                <button wire:click="back" class="rounded-md px-3 py-2 text-[13px] text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">Back</button>
                <button wire:click="chooseIdentity" class="flex-1 rounded-md bg-[var(--series-1)] px-3 py-2 text-[13px] font-medium text-white">Continue</button>
            </div>
            </div>
        @else
            <div wire:key="step-3">
            <h1 class="text-[16px] font-semibold text-[var(--text-primary)]">Set up your login</h1>
            <p class="mt-1 text-[13px] text-[var(--text-muted)]">This is how you'll sign in to check your stamp collection.</p>

            <div class="mt-4 flex flex-col gap-3">
                <label class="flex flex-col gap-1">
                    <span class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Email</span>
                    <input type="email" wire:model="email" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px]">
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Password</span>
                    <input type="password" wire:model="password" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px]">
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[11px] font-medium uppercase tracking-wide text-[var(--text-muted)]">Confirm password</span>
                    <input type="password" wire:model="passwordConfirmation" class="rounded-md border border-[var(--border-hairline)] bg-[var(--surface-1)] px-3 py-2 text-[13px]">
                </label>
            </div>

            @if ($error)
                <div class="mt-3 text-[12px] text-[var(--status-critical)]">{{ $error }}</div>
            @endif

            <div class="mt-5 flex gap-2">
                <button wire:click="back" class="rounded-md px-3 py-2 text-[13px] text-[var(--text-secondary)] hover:bg-[var(--surface-2)]">Back</button>
                <button wire:click="register" class="flex-1 rounded-md bg-[var(--series-1)] px-3 py-2 text-[13px] font-medium text-white">Create account</button>
            </div>
            </div>
        @endif
    </div>

    <div class="mt-4 text-center text-[12px] text-[var(--text-muted)]">
        Already registered? <a href="{{ route('portal.login') }}" class="font-medium text-[var(--series-1)] hover:underline">Sign in</a>
    </div>
</div>
