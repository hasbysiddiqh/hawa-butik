{{-- Font & tema Hawa Butik (Modern Sage). Letakkan SETELAH semua CSS lain di <head>. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
{{-- Font lokal dimuat lebih awal (preload) supaya tidak ada kedipan font lain saat halaman dibuka. --}}
<link rel="preload" href="{{ asset('fonts/Manrope.ttf') }}" as="font" type="font/ttf" crossorigin>
<link rel="preload" href="{{ asset('fonts/DMSans.ttf') }}" as="font" type="font/ttf" crossorigin>
{{-- Google Fonts hanya cadangan: dimuat tanpa memblokir tampilan halaman. --}}
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Manrope:wght@600;700;800&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Manrope:wght@600;700;800&display=swap"></noscript>
<link rel="stylesheet" href="{{ asset('css/hawa-theme.css') }}?v={{ @filemtime(public_path('css/hawa-theme.css')) }}">
<link rel="stylesheet" href="{{ asset('css/hawa-dash.css') }}?v={{ @filemtime(public_path('css/hawa-dash.css')) }}">
<script src="{{ asset('js/hawa-ui.js') }}?v={{ @filemtime(public_path('js/hawa-ui.js')) }}" defer></script>
@include('partials.head-favicon')
