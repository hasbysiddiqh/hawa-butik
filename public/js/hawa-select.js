/* Dropdown bergaya Hawa: menggantikan tampilan bawaan <select> di area dashboard. <select> asli tetap ada (tersembunyi) sehingga form tetap terkirim seperti biasa. */
(function () {
    function buat(sel) {
        if (sel.dataset.hs) { return; }
        sel.dataset.hs = '1';
        var wrap = document.createElement('div');
        wrap.className = 'hs';
        sel.parentNode.insertBefore(wrap, sel);
        wrap.appendChild(sel);
        sel.classList.add('hs-asli');
        sel.tabIndex = -1;

        var tombol = document.createElement('button');
        tombol.type = 'button';
        tombol.className = 'hs-tombol';
        tombol.setAttribute('aria-haspopup', 'listbox');
        tombol.setAttribute('aria-expanded', 'false');
        var teks = document.createElement('span');
        tombol.appendChild(teks);
        var panah = document.createElement('i');
        panah.className = 'hs-panah';
        tombol.appendChild(panah);
        if (sel.disabled) { tombol.disabled = true; }

        var daftar = document.createElement('ul');
        daftar.className = 'hs-daftar';
        daftar.setAttribute('role', 'listbox');
        wrap.appendChild(tombol);
        wrap.appendChild(daftar);

        var item = [];
        var fokus = -1;

        function isi() {
            daftar.innerHTML = '';
            item = [];
            [].slice.call(sel.children).forEach(function (anak) {
                if (anak.tagName === 'OPTGROUP') {
                    var judul = document.createElement('li');
                    judul.className = 'hs-grup';
                    judul.textContent = anak.label;
                    daftar.appendChild(judul);
                    [].slice.call(anak.children).forEach(tambah);
                } else { tambah(anak); }
            });
            segarkan();
        }
        function tambah(opt) {
            var li = document.createElement('li');
            li.className = 'hs-opsi' + (opt.disabled ? ' mati' : '') + (opt.value === '' ? ' kosong' : '');
            li.setAttribute('role', 'option');
            li.textContent = opt.textContent.trim();
            li._opt = opt;
            li.addEventListener('mousedown', function (e) { e.preventDefault(); });
            li.addEventListener('click', function () { if (!opt.disabled) { pilih(opt); } });
            daftar.appendChild(li);
            item.push(li);
        }
        function segarkan() {
            var terpilih = sel.options[sel.selectedIndex];
            teks.textContent = terpilih ? terpilih.textContent.trim() : '';
            wrap.classList.toggle('hs-kosong', !terpilih || terpilih.value === '');
            item.forEach(function (li) {
                var on = li._opt === terpilih;
                li.classList.toggle('terpilih', on);
                li.setAttribute('aria-selected', on ? 'true' : 'false');
            });
        }
        function pilih(opt) {
            sel.value = opt.value;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
            segarkan();
            tutup();
            tombol.focus();
        }
        function buka() {
            if (tombol.disabled) { return; }
            document.querySelectorAll('.hs.buka').forEach(function (w) { if (w !== wrap) { w.classList.remove('buka'); } });
            wrap.classList.add('buka');
            if (sel.closest('table, .table-responsive')) {
                var r = tombol.getBoundingClientRect();
                wrap.classList.add('hs-fix');
                daftar.style.left = r.left + 'px';
                daftar.style.top = (r.bottom + 6) + 'px';
                daftar.style.width = Math.max(r.width, 160) + 'px';
            }
            tombol.setAttribute('aria-expanded', 'true');
            var terpilih = daftar.querySelector('.terpilih');
            fokus = item.indexOf(terpilih);
            sorot();
            if (terpilih) { daftar.scrollTop = terpilih.offsetTop - 60; }
        }
        function tutup() { wrap.classList.remove('buka'); tombol.setAttribute('aria-expanded', 'false'); }
        function sorot() {
            item.forEach(function (li, i) { li.classList.toggle('sorot', i === fokus); });
            if (item[fokus]) {
                var li = item[fokus];
                if (li.offsetTop < daftar.scrollTop) { daftar.scrollTop = li.offsetTop - 8; }
                else if (li.offsetTop + li.offsetHeight > daftar.scrollTop + daftar.clientHeight) { daftar.scrollTop = li.offsetTop + li.offsetHeight - daftar.clientHeight + 8; }
            }
        }
        function geser(arah) {
            var i = fokus;
            do { i += arah; } while (item[i] && item[i]._opt.disabled);
            if (item[i]) { fokus = i; sorot(); }
        }

        tombol.addEventListener('click', function () { wrap.classList.contains('buka') ? tutup() : buka(); });
        tombol.addEventListener('keydown', function (e) {
            var terbuka = wrap.classList.contains('buka');
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (!terbuka) { buka(); } else { geser(e.key === 'ArrowDown' ? 1 : -1); }
            } else if ((e.key === 'Enter' || e.key === ' ') && terbuka) {
                e.preventDefault();
                if (item[fokus]) { pilih(item[fokus]._opt); }
            } else if (e.key === 'Escape' || e.key === 'Tab') { tutup(); }
        });
        tombol.addEventListener('blur', function () { setTimeout(function () { if (!wrap.contains(document.activeElement)) { tutup(); } }, 120); });
        sel.addEventListener('change', segarkan);
        if (sel.form) { sel.form.addEventListener('reset', function () { setTimeout(segarkan, 0); }); }
        var label = sel.id ? document.querySelector('label[for="' + sel.id + '"]') : null;
        if (label) { label.addEventListener('click', function (e) { e.preventDefault(); tombol.focus(); }); }
        isi();
    }
    document.addEventListener('click', function (e) {
        document.querySelectorAll('.hs.buka').forEach(function (w) { if (!w.contains(e.target)) { w.classList.remove('buka'); } });
    });
    ['scroll', 'resize'].forEach(function (n) {
        window.addEventListener(n, function (e) {
            if (e.target && e.target.closest && e.target.closest('.hs-daftar')) { return; }
            document.querySelectorAll('.hs.hs-fix.buka').forEach(function (w) { w.classList.remove('buka'); });
        }, true);
    });
    function mulai() { document.querySelectorAll('.dash-main select:not([multiple]):not([size])').forEach(buat); }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', mulai); } else { mulai(); }
})();
