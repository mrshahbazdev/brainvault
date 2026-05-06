@extends('layouts.app')

@section('title', __('Knowledge Graph'))

@section('content')
<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Knowledge Graph') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Visual connections between your saved knowledge') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="zoom-in" class="p-2 bg-gray-100 dark:bg-surface-800 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="{{ __('Zoom In') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            </button>
            <button id="zoom-out" class="p-2 bg-gray-100 dark:bg-surface-800 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="{{ __('Zoom Out') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" /></svg>
            </button>
            <button id="reset-zoom" class="p-2 bg-gray-100 dark:bg-surface-800 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="{{ __('Reset View') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden relative" style="height: clamp(350px, 65vh, 650px);">
        <div id="knowledge-graph" class="w-full h-full"></div>

        {{-- Floating Tooltip --}}
        <div id="graph-tooltip" class="absolute hidden pointer-events-none z-10 px-3 py-2 bg-white dark:bg-surface-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl text-xs max-w-[200px]">
            <p id="tooltip-label" class="font-semibold text-gray-900 dark:text-white truncate"></p>
            <p id="tooltip-type" class="text-gray-500 capitalize mt-0.5"></p>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mt-4 flex flex-wrap items-center gap-5 text-sm text-gray-500">
        <span class="flex items-center gap-2">
            <span class="w-3.5 h-3.5 rounded-full bg-blue-500 shadow-sm shadow-blue-500/40"></span>
            {{ __('Bookmarks') }}
        </span>
        <span class="flex items-center gap-2">
            <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/40"></span>
            {{ __('Notes') }}
        </span>
        <span class="flex items-center gap-2">
            <span class="w-3.5 h-3.5 rounded-full bg-violet-500 shadow-sm shadow-violet-500/40"></span>
            {{ __('Topics') }}
        </span>
        <span class="flex items-center gap-2">
            <span class="w-3.5 h-3.5 rounded-full bg-amber-500 shadow-sm shadow-amber-500/40"></span>
            {{ __('Tags') }}
        </span>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://d3js.org/d3.v7.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('knowledge-graph');
    const width = container.clientWidth;
    const height = container.clientHeight;
    const isDark = document.documentElement.classList.contains('dark');

    const graphData = @json($graphData ?? ['nodes' => [], 'links' => []]);

    if (graphData.nodes.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full px-4">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-violet-100 to-blue-100 dark:from-violet-900/30 dark:to-blue-900/30 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">${@json(__('Building Your Knowledge Graph'))}</h3>
                <p class="text-sm text-gray-500 max-w-sm text-center leading-relaxed">${@json(__('Save more bookmarks and notes to see connections appear. AI will automatically detect topics and relationships.'))}</p>
            </div>
        `;
        return;
    }

    const colorMap = {
        bookmark: { fill: '#3B82F6', glow: 'rgba(59,130,246,0.4)' },
        note:     { fill: '#10B981', glow: 'rgba(16,185,129,0.4)' },
        topic:    { fill: '#8B5CF6', glow: 'rgba(139,92,246,0.5)' },
        tag:      { fill: '#F59E0B', glow: 'rgba(245,158,11,0.4)' },
    };

    const sizeMap = { topic: 16, tag: 10, bookmark: 8, note: 8 };

    const svg = d3.select('#knowledge-graph')
        .append('svg')
        .attr('width', width)
        .attr('height', height)
        .style('background', isDark ? '#0f172a' : '#fafbfc');

    // Gradient definitions for links
    const defs = svg.append('defs');

    // Glow filter
    const glowFilter = defs.append('filter').attr('id', 'glow');
    glowFilter.append('feGaussianBlur').attr('stdDeviation', '3').attr('result', 'coloredBlur');
    const feMerge = glowFilter.append('feMerge');
    feMerge.append('feMergeNode').attr('in', 'coloredBlur');
    feMerge.append('feMergeNode').attr('in', 'SourceGraphic');

    // Soft shadow for nodes
    const shadow = defs.append('filter').attr('id', 'shadow').attr('x', '-50%').attr('y', '-50%').attr('width', '200%').attr('height', '200%');
    shadow.append('feDropShadow').attr('dx', '0').attr('dy', '1').attr('stdDeviation', '2').attr('flood-opacity', isDark ? '0.5' : '0.15');

    const g = svg.append('g');

    // Zoom
    const zoom = d3.zoom()
        .scaleExtent([0.15, 6])
        .on('zoom', (event) => g.attr('transform', event.transform));
    svg.call(zoom);

    document.getElementById('zoom-in').addEventListener('click', () => svg.transition().duration(300).call(zoom.scaleBy, 1.4));
    document.getElementById('zoom-out').addEventListener('click', () => svg.transition().duration(300).call(zoom.scaleBy, 0.7));
    document.getElementById('reset-zoom').addEventListener('click', () => svg.transition().duration(500).call(zoom.transform, d3.zoomIdentity));

    // Simulation
    const simulation = d3.forceSimulation(graphData.nodes)
        .force('link', d3.forceLink(graphData.links).id(d => d.id).distance(100))
        .force('charge', d3.forceManyBody().strength(-300))
        .force('center', d3.forceCenter(width / 2, height / 2))
        .force('collision', d3.forceCollide().radius(d => (sizeMap[d.type] || 8) + 10))
        .force('x', d3.forceX(width / 2).strength(0.05))
        .force('y', d3.forceY(height / 2).strength(0.05));

    // Links
    const link = g.append('g')
        .selectAll('line')
        .data(graphData.links)
        .enter().append('line')
        .attr('stroke', isDark ? 'rgba(148,163,184,0.15)' : 'rgba(148,163,184,0.3)')
        .attr('stroke-width', 1.5);

    // Node groups
    const nodeGroup = g.append('g')
        .selectAll('g')
        .data(graphData.nodes)
        .enter().append('g')
        .style('cursor', 'pointer')
        .call(d3.drag()
            .on('start', (event, d) => {
                if (!event.active) simulation.alphaTarget(0.3).restart();
                d.fx = d.x; d.fy = d.y;
            })
            .on('drag', (event, d) => { d.fx = event.x; d.fy = event.y; })
            .on('end', (event, d) => {
                if (!event.active) simulation.alphaTarget(0);
                d.fx = null; d.fy = null;
            })
        );

    // Outer glow ring
    nodeGroup.append('circle')
        .attr('r', d => (sizeMap[d.type] || 8) + 4)
        .attr('fill', d => (colorMap[d.type] || colorMap.bookmark).glow)
        .attr('opacity', 0.5)
        .attr('class', 'glow-ring');

    // Main circle
    nodeGroup.append('circle')
        .attr('r', d => sizeMap[d.type] || 8)
        .attr('fill', d => (colorMap[d.type] || colorMap.bookmark).fill)
        .attr('stroke', isDark ? '#1e293b' : '#ffffff')
        .attr('stroke-width', 2.5)
        .attr('filter', 'url(#shadow)')
        .attr('class', 'main-circle');

    // Icon inside topic nodes (larger ones)
    nodeGroup.filter(d => d.type === 'topic')
        .append('text')
        .attr('text-anchor', 'middle')
        .attr('dominant-baseline', 'central')
        .attr('font-size', '10px')
        .attr('fill', '#ffffff')
        .text('★');

    // Labels
    nodeGroup.append('text')
        .text(d => d.label)
        .attr('font-size', d => d.type === 'topic' ? '11px' : '9px')
        .attr('font-weight', d => d.type === 'topic' ? '600' : '500')
        .attr('font-family', 'Inter, system-ui, sans-serif')
        .attr('fill', isDark ? '#cbd5e1' : '#475569')
        .attr('text-anchor', 'middle')
        .attr('dy', d => -(sizeMap[d.type] || 8) - 8)
        .attr('opacity', 0.85)
        .style('pointer-events', 'none');

    // Tooltip
    const tooltip = document.getElementById('graph-tooltip');
    const tooltipLabel = document.getElementById('tooltip-label');
    const tooltipType = document.getElementById('tooltip-type');

    nodeGroup
        .on('mouseover', function(event, d) {
            // Highlight this node
            d3.select(this).select('.main-circle')
                .transition().duration(200)
                .attr('r', (sizeMap[d.type] || 8) + 3)
                .attr('stroke-width', 3);
            d3.select(this).select('.glow-ring')
                .transition().duration(200)
                .attr('r', (sizeMap[d.type] || 8) + 8)
                .attr('opacity', 0.8);

            // Highlight connected links
            link.transition().duration(200)
                .attr('stroke', l => (l.source.id === d.id || l.target.id === d.id)
                    ? (colorMap[d.type] || colorMap.bookmark).fill
                    : (isDark ? 'rgba(148,163,184,0.08)' : 'rgba(148,163,184,0.15)'))
                .attr('stroke-width', l => (l.source.id === d.id || l.target.id === d.id) ? 2.5 : 1);

            // Dim unconnected nodes
            const connectedIds = new Set();
            connectedIds.add(d.id);
            graphData.links.forEach(l => {
                const sid = typeof l.source === 'object' ? l.source.id : l.source;
                const tid = typeof l.target === 'object' ? l.target.id : l.target;
                if (sid === d.id) connectedIds.add(tid);
                if (tid === d.id) connectedIds.add(sid);
            });
            nodeGroup.transition().duration(200)
                .attr('opacity', n => connectedIds.has(n.id) ? 1 : 0.2);

            // Tooltip
            tooltipLabel.textContent = d.label;
            tooltipType.textContent = d.type;
            tooltip.classList.remove('hidden');
        })
        .on('mousemove', function(event) {
            const rect = container.getBoundingClientRect();
            tooltip.style.left = (event.clientX - rect.left + 12) + 'px';
            tooltip.style.top = (event.clientY - rect.top - 10) + 'px';
        })
        .on('mouseout', function(event, d) {
            d3.select(this).select('.main-circle')
                .transition().duration(300)
                .attr('r', sizeMap[d.type] || 8)
                .attr('stroke-width', 2.5);
            d3.select(this).select('.glow-ring')
                .transition().duration(300)
                .attr('r', (sizeMap[d.type] || 8) + 4)
                .attr('opacity', 0.5);

            link.transition().duration(300)
                .attr('stroke', isDark ? 'rgba(148,163,184,0.15)' : 'rgba(148,163,184,0.3)')
                .attr('stroke-width', 1.5);

            nodeGroup.transition().duration(300).attr('opacity', 1);
            tooltip.classList.add('hidden');
        });

    // Animate nodes appearing
    nodeGroup.attr('opacity', 0)
        .transition().duration(800).delay((d, i) => i * 20)
        .attr('opacity', 1);

    link.attr('opacity', 0)
        .transition().duration(600).delay(300)
        .attr('opacity', 1);

    // Tick
    simulation.on('tick', () => {
        link
            .attr('x1', d => d.source.x)
            .attr('y1', d => d.source.y)
            .attr('x2', d => d.target.x)
            .attr('y2', d => d.target.y);

        nodeGroup.attr('transform', d => `translate(${d.x},${d.y})`);
    });

    // Auto-fit after simulation settles
    simulation.on('end', () => {
        const bounds = g.node().getBBox();
        if (bounds.width > 0 && bounds.height > 0) {
            const dx = bounds.width, dy = bounds.height;
            const x = bounds.x + dx / 2, y = bounds.y + dy / 2;
            const scale = 0.85 / Math.max(dx / width, dy / height);
            const translate = [width / 2 - scale * x, height / 2 - scale * y];
            svg.transition().duration(750).call(
                zoom.transform,
                d3.zoomIdentity.translate(translate[0], translate[1]).scale(scale)
            );
        }
    });
});
</script>
@endpush
