@props(['route', 'label' => 'Exporter Excel'])

<a href="{{ $route }}" class="btn-export">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
    {{ $label }}
</a>

@once
<style>
    .btn-export {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 16px; border-radius: 10px;
        background: linear-gradient(135deg, #065F46, #047857);
        color: #fff !important; font-weight: 600; font-size: 13px;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(6,95,70,0.25);
        transition: all .2s ease;
        border: none; cursor: pointer;
    }
    .btn-export:hover {
        background: linear-gradient(135deg, #047857, #059669);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(6,95,70,0.35);
    }
    .btn-export:active { transform: translateY(0); }
    .btn-export svg { flex-shrink: 0; }
</style>
@endonce