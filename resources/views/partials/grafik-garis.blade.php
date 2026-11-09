{{-- Grafik garis SVG sederhana. Butuh $g (dari DataDashboard::bulanan) dan $gid unik. --}}
<div class="dchart">
    <svg viewBox="0 0 300 90" preserveAspectRatio="none" role="img" aria-label="Grafik pesanan per bulan">
        <defs>
            <linearGradient id="gr-{{ $gid }}" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="currentColor" stop-opacity=".28"/>
                <stop offset="1" stop-color="currentColor" stop-opacity="0"/>
            </linearGradient>
        </defs>
        <path d="{{ $g['area'] }}" fill="url(#gr-{{ $gid }})"/>
        <polyline points="{{ $g['line'] }}" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
    </svg>
    <div class="dchart-dots">
        @foreach ($g['pts'] as $i => $p)
            <i style="left:{{ $p[0] / 300 * 100 }}%;top:{{ $p[1] / 90 * 100 }}%" title="{{ $g['labels'][$i] }}: {{ $g['values'][$i] }}"></i>
        @endforeach
    </div>
    <div class="dchart-x">@foreach ($g['labels'] as $l)<span>{{ $l }}</span>@endforeach</div>
</div>
