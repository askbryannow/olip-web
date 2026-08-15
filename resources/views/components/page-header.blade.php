@props(['title', 'subtitle' => null])
<div class="mb-4">
    <h1 class="text-[19px] font-semibold text-[var(--text-primary)]">{{ $title }}</h1>
    @if ($subtitle)
        <p class="text-[13px] text-[var(--text-muted)] mt-0.5">{{ $subtitle }}</p>
    @endif
</div>
