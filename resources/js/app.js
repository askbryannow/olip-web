import Chart from 'chart.js/auto';

window.Chart = Chart;

window.cssVar = function cssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
};

const FORMATTERS = {
    number: (v) => Math.round(v).toLocaleString('en-MY'),
    percent1: (v) => `${Number(v).toFixed(1)}%`,
    percent0: (v) => `${Number(v).toFixed(0)}%`,
    currency: (v) => (v >= 1000 ? `RM ${(v / 1000).toFixed(1)}K` : `RM ${Math.round(v)}`),
    days: (v) => `${Number(v).toFixed(0)}d`,
};

function resolveConfig(value) {
    if (typeof value === 'string') {
        const colorMatch = value.match(/^var\((--[a-z0-9-]+)\)$/i);
        if (colorMatch) return window.cssVar(colorMatch[1]);

        const tickMatch = value.match(/^__tickfmt:(\w+)$/);
        if (tickMatch) return (v) => FORMATTERS[tickMatch[1]](v);

        const tooltipMatch = value.match(/^__tooltipfmt:(\w+)$/);
        if (tooltipMatch) {
            return (ctx) => {
                const raw = ctx.parsed?.y ?? ctx.parsed?.x ?? ctx.parsed ?? ctx.raw;
                return `${ctx.dataset.label ?? ''}: ${FORMATTERS[tooltipMatch[1]](raw)}`;
            };
        }

        return value;
    }
    if (Array.isArray(value)) {
        return value.map(resolveConfig);
    }
    if (value && typeof value === 'object') {
        const out = {};
        for (const k in value) out[k] = resolveConfig(value[k]);
        return out;
    }
    return value;
}

const chartRegistry = new WeakMap();

window.renderChart = function renderChart(canvas, config) {
    if (chartRegistry.has(canvas)) {
        chartRegistry.get(canvas).destroy();
    }
    const chart = new Chart(canvas, resolveConfig(config));
    chartRegistry.set(canvas, chart);
    return chart;
};
