@extends('layouts.app')

@section('title', __('Knowledge Graph'))

@section('content')
<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Knowledge Graph') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Visual connections between your saved knowledge') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="zoom-in" class="p-2 bg-gray-100 dark:bg-surface-800 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            </button>
            <button id="zoom-out" class="p-2 bg-gray-100 dark:bg-surface-800 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" /></svg>
            </button>
            <button id="reset-zoom" class="p-2 bg-gray-100 dark:bg-surface-800 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden" style="height: 600px;">
        <div id="knowledge-graph" class="w-full h-full"></div>
    </div>

    {{-- Legend --}}
    <div class="mt-4 flex items-center gap-4 text-sm text-gray-500">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500"></span>{{ __('Bookmarks') }}</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500"></span>{{ __('Notes') }}</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-purple-500"></span>{{ __('Topics') }}</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span>{{ __('Tags') }}</span>
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

    // Sample/placeholder data (will be populated from API)
    const graphData = @json($graphData ?? ['nodes' => [], 'links' => []]);

    if (graphData.nodes.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="w-16 h-16 text-gray-200 dark:text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">${@json(__('Building Your Knowledge Graph'))}</h3>
                <p class="text-sm text-gray-500 max-w-md text-center">${@json(__('Save more bookmarks and notes to see connections appear. AI will automatically detect topics and relationships.'))}</p>
            </div>
        `;
        return;
    }

    const svg = d3.select('#knowledge-graph')
        .append('svg')
        .attr('width', width)
        .attr('height', height);

    const g = svg.append('g');

    // Zoom behavior
    const zoom = d3.zoom()
        .scaleExtent([0.2, 5])
        .on('zoom', (event) => g.attr('transform', event.transform));

    svg.call(zoom);

    document.getElementById('zoom-in').addEventListener('click', () => svg.transition().call(zoom.scaleBy, 1.3));
    document.getElementById('zoom-out').addEventListener('click', () => svg.transition().call(zoom.scaleBy, 0.7));
    document.getElementById('reset-zoom').addEventListener('click', () => svg.transition().call(zoom.transform, d3.zoomIdentity));

    const colorMap = {
        bookmark: '#3B82F6',
        note: '#22C55E',
        topic: '#8B5CF6',
        tag: '#F59E0B',
    };

    const simulation = d3.forceSimulation(graphData.nodes)
        .force('link', d3.forceLink(graphData.links).id(d => d.id).distance(80))
        .force('charge', d3.forceManyBody().strength(-200))
        .force('center', d3.forceCenter(width / 2, height / 2))
        .force('collision', d3.forceCollide().radius(30));

    const link = g.append('g')
        .selectAll('line')
        .data(graphData.links)
        .enter().append('line')
        .attr('stroke', isDark ? '#334155' : '#E2E8F0')
        .attr('stroke-width', 1.5)
        .attr('stroke-opacity', 0.6);

    const node = g.append('g')
        .selectAll('circle')
        .data(graphData.nodes)
        .enter().append('circle')
        .attr('r', d => d.type === 'topic' ? 12 : 8)
        .attr('fill', d => colorMap[d.type] || '#6366F1')
        .attr('stroke', isDark ? '#0F172A' : '#FFFFFF')
        .attr('stroke-width', 2)
        .style('cursor', 'pointer')
        .call(d3.drag()
            .on('start', (event, d) => {
                if (!event.active) simulation.alphaTarget(0.3).restart();
                d.fx = d.x; d.fy = d.y;
            })
            .on('drag', (event, d) => {
                d.fx = event.x; d.fy = event.y;
            })
            .on('end', (event, d) => {
                if (!event.active) simulation.alphaTarget(0);
                d.fx = null; d.fy = null;
            })
        );

    const label = g.append('g')
        .selectAll('text')
        .data(graphData.nodes)
        .enter().append('text')
        .text(d => d.label)
        .attr('font-size', '10px')
        .attr('font-family', 'Inter, sans-serif')
        .attr('fill', isDark ? '#94A3B8' : '#64748B')
        .attr('text-anchor', 'middle')
        .attr('dy', -14);

    // Tooltip
    node.append('title').text(d => `${d.type}: ${d.label}`);

    simulation.on('tick', () => {
        link
            .attr('x1', d => d.source.x)
            .attr('y1', d => d.source.y)
            .attr('x2', d => d.target.x)
            .attr('y2', d => d.target.y);

        node.attr('cx', d => d.x).attr('cy', d => d.y);
        label.attr('x', d => d.x).attr('y', d => d.y);
    });
});
</script>
@endpush
