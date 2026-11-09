/* Ikon tab: logo dimuat ke kanvas persegi agar tidak gepeng/mengecil walau logonya memanjang */
(function () {
    var link = document.querySelector('link[rel="icon"]');
    if (!link) return;
    var img = new Image();
    img.onload = function () {
        try {
            var c = document.createElement('canvas'), n = 64;
            c.width = c.height = n;
            var x = c.getContext('2d'), pad = 6, k = Math.min((n - pad * 2) / img.width, (n - pad * 2) / img.height);
            var w = img.width * k, h = img.height * k, r = 14;
            x.fillStyle = '#FBF8F3';
            x.beginPath();
            x.moveTo(r, 0); x.arcTo(n, 0, n, n, r); x.arcTo(n, n, 0, n, r); x.arcTo(0, n, 0, 0, r); x.arcTo(0, 0, n, 0, r);
            x.closePath(); x.fill();
            x.drawImage(img, (n - w) / 2, (n - h) / 2, w, h);
            link.type = 'image/png';
            link.href = c.toDataURL('image/png');
        } catch (e) { /* tetap pakai ikon statis */ }
    };
    img.src = link.href;
})();
