@props(['config', 'height' => 260])
<div style="height: {{ $height }}px">
    <canvas wire:key="chart-{{ md5(json_encode($config)) }}" x-data x-init="renderChart($el, @js($config))"></canvas>
</div>
