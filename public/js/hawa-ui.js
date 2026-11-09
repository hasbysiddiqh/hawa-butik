/* Hawa Butik — interaksi UI kecil (tanpa library tambahan). */
(function () {
    var root = document.documentElement;
    var reduce = false;
    root.classList.add('hb-js');

    document.addEventListener('DOMContentLoaded', function () {
        /* Halaman dalam: kartu, judul, dan tabel ikut muncul bertahap */
        document.querySelectorAll('.page-pesanan h1, .page-pesanan .lead-text, .page-pesanan .subnav, .produk-card, .pilihan-card, .pesanan-card, .kelompok-judul, .page-pesanan .table-responsive, .dash-top, .dstat, .dc, .dhero, .dpromo').forEach(function (el, i) {
            if (el.classList.contains('reveal')) return;
            var idx = Array.prototype.indexOf.call(el.parentNode.children, el);
            el.classList.add('reveal');
            el.style.setProperty('--d', Math.min(idx, 6) * 0.07 + 's');
        });

        /* Elemen muncul bertahap saat masuk viewport; kelas reveal dilepas setelah selesai
           agar animasi hover tidak ikut tertunda */
        var items = document.querySelectorAll('.reveal');
        function selesai(el) {
            setTimeout(function () {
                el.classList.remove('reveal', 'is-in');
                el.style.removeProperty('--d');
            }, 1100 + (parseFloat(el.style.getPropertyValue('--d')) || 0) * 1000);
        }
        if (reduce || !('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('is-in'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('is-in');
                    selesai(e.target);
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        items.forEach(function (el) { io.observe(el); });
    });
})();

/* Cahaya mengikuti kursor pada kartu bento */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.bento-card').forEach(function (card) {
        card.addEventListener('mousemove', function (e) {
            var r = card.getBoundingClientRect();
            card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
            card.style.setProperty('--my', (e.clientY - r.top) + 'px');
        });
    });
});

/* Slider: geser (swipe) di layar sentuh */
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('carouselExampleIndicators');
    if (!el || !window.jQuery) return;
    var x0 = null;
    el.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
    el.addEventListener('touchend', function (e) {
        if (x0 === null) return;
        var dx = e.changedTouches[0].clientX - x0;
        x0 = null;
        if (Math.abs(dx) > 50) window.jQuery(el).carousel(dx < 0 ? 'next' : 'prev');
    }, { passive: true });
});

/* Gulir halus untuk menu (#anchor), memperhitungkan tinggi header.
   Dianimasikan sendiri (bukan scroll-behavior bawaan) supaya tetap mulus
   walau browser/OS mematikan animasi. Tombol Bootstrap tidak disentuh. */
(function () {
    var anim = null;
    function ease(t) { return t < .5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }
    function batal() { if (anim) { cancelAnimationFrame(anim); anim = null; } }
    ['wheel', 'touchstart', 'keydown'].forEach(function (n) { window.addEventListener(n, batal, { passive: true }); });

    document.addEventListener('click', function (e) {
        var a = e.target.closest && e.target.closest('a[href*="#"]');
        if (!a || a.target === '_blank' || e.defaultPrevented || e.button || e.ctrlKey || e.metaKey || e.shiftKey) return;
        if (a.matches('[data-slide],[data-slide-to],[data-toggle],[data-target],[data-dismiss],[role="tab"]')) return;
        var u;
        try { u = new URL(a.href, location.href); } catch (_) { return; }
        if (u.origin !== location.origin || u.pathname !== location.pathname || u.hash.length < 2) return;
        var tujuan = document.getElementById(decodeURIComponent(u.hash.slice(1)));
        if (!tujuan) return;
        e.preventDefault();
        e.stopPropagation();                       // lewati animasi scroll bawaan template
        batal();
        var dari = window.pageYOffset;
        var ke = Math.max(0, Math.round(tujuan.getBoundingClientRect().top + dari - 88));
        var durasi = Math.min(1200, 650 + Math.abs(ke - dari) * 0.2);
        var t0 = null;
        function langkah(t) {
            if (t0 === null) t0 = t;
            var p = Math.min((t - t0) / durasi, 1);
            window.scrollTo(0, dari + (ke - dari) * ease(p));
            if (p < 1) anim = requestAnimationFrame(langkah); else anim = null;
        }
        anim = requestAnimationFrame(langkah);
        if (history.replaceState) history.replaceState(null, '', u.hash);
    }, true);
})();

/* Muat & dekode gambar slider lebih awal agar tidak kosong sesaat saat slide berpindah */
(function () {
    function siap() {
        document.querySelectorAll('.slide-bg').forEach(function (el) {
            var m = (el.style.backgroundImage || '').match(/url\(["']?(.*?)["']?\)/);
            if (!m) return;
            var img = new Image();
            img.src = m[1];
            if (img.decode) img.decode().catch(function () {});
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', siap); else siap();
})();
