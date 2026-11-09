<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    <!-- Mobile Specific Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Author Meta -->
    <meta name="author" content="CodePixar">
    <!-- Meta Description -->
    <meta name="description" content="Hawa Butik - Sewa busana elegan untuk berbagai momen spesial">
    <!-- Meta Keyword -->
    <meta name="keywords" content="sewa busana, butik, gaun, kebaya, hawa butik">
    <!-- meta character set -->
    <meta charset="UTF-8">
    <!-- Site Title -->
    <title>Hawa Butik — Sewa Busana Elegan</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/linearicons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simplelightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @include('partials.head-tema')
</head>
<body class="hb-home">

    <!-- Start Header Area -->
    <header class="default-header">
        <div class="container">
            <div class="header-wrap">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="Hawa Butik"></a>
                    </div>
                    <div class="main-menubar d-flex align-items-center">
                        <nav class="hide">
                            @include('partials.menu-beranda')
                        </nav>
                        <div class="menu-bar"><span class="lnr lnr-menu"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- End Header Area -->

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-center mb-0" role="alert"
             style="position: fixed; top: 100px; left: 0; right: 0; z-index: 1050; border-radius: 0;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <!-- Start Banner Area -->
    <section class="banner-area relative" id="home">
        <div class="slider">
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="6500" data-pause="false">
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                        <div class="slide-bg" style="background-image: url('{{ asset('img/slider1.jpg') }}')"></div>
                        <div class="carousel-caption d-md-block">
                            <span class="eyebrow">Sewa Busana Eksklusif</span>
                            <h2>Elegance in Every <em>Moment.</em></h2>
                            <p>Temukan busana pilihan untuk tampil anggun di setiap kesempatan.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="slide-bg" style="background-image: url('{{ asset('img/slider2.jpg') }}')"></div>
                        <div class="carousel-caption d-md-block">
                            <span class="eyebrow">Ready for Rent</span>
                            <h2>Find Your Style. Own Your <em>Moment.</em></h2>
                            <p>Sewa busana favoritmu dengan mudah untuk berbagai acara spesial. Tampil cantik tanpa harus membeli.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="slide-bg" style="background-image: url('{{ asset('img/slider3.jpg') }}')"></div>
                        <div class="carousel-caption d-md-block">
                            <span class="eyebrow">Look Your Best</span>
                            <h2>Discover Your Signature <em>Look.</em></h2>
                            <p>Pilihan busana elegan untuk kondangan, wisuda, pesta, dan momen istimewa.</p>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </section>
    <!-- End Banner Area -->



    <!-- Start About Area -->
    <section class="About-area section-gap" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 about-left reveal">
                    <div class="about-frame"><img class="img-fluid" src="{{ asset('img/about-img.jpg') }}" alt="Hawa Butik"><span class="about-badge"><span class="lnr lnr-diamond"></span>Rental &amp; Jahit Custom</span></div>
                </div>
                <div class="col-lg-6 about-right reveal" style="--d:.12s">
                    <span class="eyebrow">Tentang Kami</span>
                    <h2>We Believe that Every Woman Deserves to Look Elegant</h2>
                    <p>Hawa Butik hadir untuk menyediakan pilihan busana elegan yang dapat disewa untuk berbagai momen spesial. Dari acara formal hingga pesta dan perayaan, kami membantu Anda tampil anggun, percaya diri, dan memukau dengan busana yang tepat.</p>
                    <ul class="tag-list">
                        <li>Busana Elegan</li><li>Rental &amp; Jahit Custom</li><li>Untuk Setiap Momen</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- End About Area -->

    <!-- Start Fitur Area -->
    <section class="fitur-area section-gap" id="layanan">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Layanan Kami</span>
                <h2>Semua yang Kamu Butuhkan untuk Tampil Sempurna</h2>
            </div>
            <div class="bento">
                <div class="bento-card bento-besar reveal">
                    <span class="ikon lnr lnr-tag"></span>
                    <h4>Rental Busana</h4>
                    <p>Sewa busana pilihan dari Collection Hawa Butik untuk acara spesialmu, tanpa harus membeli.</p>
                </div>
                <div class="bento-card bento-lebar reveal" style="--d:.08s">
                    <span class="ikon lnr lnr-pencil"></span>
                    <h4>Pesan Jahit Custom</h4>
                    <p>Pilih model, ukuran, bahan, dan warna sesuai keinginanmu.</p>
                </div>
                <div class="bento-card bento-rose reveal" style="--d:.16s">
                    <span class="ikon lnr lnr-diamond"></span>
                    <h4>Koleksi Pilihan</h4>
                    <p>Kondangan, wisuda, hingga pesta.</p>
                </div>
                <div class="bento-card reveal" style="--d:.24s">
                    <span class="ikon lnr lnr-bubble"></span>
                    <h4>Konsultasi Mudah</h4>
                    <p>Tanya langsung lewat Instagram &amp; TikTok.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- End Fitur Area -->

    <!-- Start Gallery Area -->
    <section class="gallery-area section-gap" id="gallery">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Koleksi</span>
                <h2>Our Collection</h2>
                <p>Pilih busana favoritmu, lalu sewa langsung dari halaman Collection.</p>
            </div>
            @php($galeri = $produkTerbaru->filter(fn ($p) => $p->fotoUrl())->take(7)->map(fn ($p) => ['src' => $p->fotoUrl(), 'alt' => $p->nama_produk]))
            @if ($galeri->isEmpty())
                @php($galeri = collect(range(1, 7))->map(fn ($i) => ['src' => asset('img/p' . $i . '.jpg'), 'alt' => 'Koleksi ' . $i]))
            @endif
            <div class="gal reveal">
                @foreach ($galeri as $foto)
                    <div><img src="{{ $foto['src'] }}" alt="{{ $foto['alt'] }}" loading="lazy" draggable="false"></div>
                @endforeach
            </div>

            <div class="text-center mt-5 reveal">
                <a href="{{ route('katalog.index') }}" class="primary-btn">Lihat Semua Collection<i class="arr"></i></a>
            </div>
        </div>
    </section>
    <!-- End Gallery Area -->

    <!-- Start CTA -->
    <section class="cta-area">
        <div class="container">
            <div class="cta-banner reveal" style="background-image: url('{{ asset('img/slider3.jpg') }}')">
                <div class="cta-isi">
                    <span class="eyebrow terang">Siap Tampil Memukau?</span>
                    <h2>Satu Busana yang Tepat Mengubah Seluruh Momen.</h2>
                    <p>Pilih dari Collection kami atau pesan jahit sesuai seleramu.</p>
                    <div class="hero-cta">
                        <a href="{{ route('pelanggan.pemesanan') }}" class="primary-btn btn-krem">Pesan Sekarang<i class="arr"></i></a>
                        <a href="#contact" class="primary-btn btn-terang">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End CTA -->

    <!-- Start Cara Sewa -->
    <section class="cara-area section-gap" id="cara-sewa">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Cara Memesan</span>
                <h2>Empat Langkah Mudah</h2>
                <p>Dari memilih busana sampai memantau pesanan, semuanya bisa dilakukan online.</p>
            </div>
            <ol class="langkah">
                <li class="reveal"><span class="nomor">01</span><span class="ikon lnr lnr-eye"></span><h5>Pilih Busana</h5><p>Lihat Collection dan temukan busana yang cocok untuk acaramu.</p></li>
                <li class="reveal" style="--d:.08s"><span class="nomor">02</span><span class="ikon lnr lnr-user"></span><h5>Masuk atau Daftar</h5><p>Buat akun gratis agar pesananmu tersimpan rapi.</p></li>
                <li class="reveal" style="--d:.16s"><span class="nomor">03</span><span class="ikon lnr lnr-pencil"></span><h5>Isi Pemesanan</h5><p>Pilih Rental atau Pesan jahit, lalu lengkapi formulirnya.</p></li>
                <li class="reveal" style="--d:.24s"><span class="nomor">04</span><span class="ikon lnr lnr-checkmark-circle"></span><h5>Pantau Pesanan</h5><p>Cek status pesananmu kapan saja di halaman Pesanan Saya.</p></li>
            </ol>
        </div>
    </section>
    <!-- End Cara Sewa -->

    <!-- Start Contact Area -->
    <section class="contact-area" id="contact">
        <div class="container">
            <div class="row no-gutters align-items-stretch contact-card">
                <div class="col-lg-5 contact-left"><img src="{{ asset('img/contact-img.jpg') }}" alt="Hawa Butik" loading="lazy"></div>
                <div class="col-lg-7 contact-right reveal">
                    <span class="eyebrow">Kontak</span>
                    <h2>Hubungi Hawa Butik</h2>
                    <p>Ikuti dan hubungi kami lewat akun resmi Hawa Butik untuk melihat koleksi terbaru, bertanya soal busana, atau berkonsultasi sebelum memesan.</p>
                    <ul class="list-unstyled contact-akun">
                        <li><a href="https://www.instagram.com/hawa_butikk/" target="_blank" rel="noopener"><i class="fa fa-instagram"></i><span><b>Instagram</b><br><small>@hawa_butikk</small></span></a></li>
                        <li><a href="https://www.tiktok.com/@hawa_butikk" target="_blank" rel="noopener">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                            <span><b>TikTok</b><br><small>@hawa_butikk</small></span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- End Contact Area -->

    <!-- Start Footer Area -->
    @include('partials.footer-situs')
    <!-- End Footer Area -->

    <script src="{{ asset('js/vendor/jquery-2.2.4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/jquery.nice-select.min.js') }}"></script>
    <script>jQuery.fn.sticky = function () { return this; }; /* header sudah fixed lewat CSS; plugin sticky membuat menu bergeser */</script>
    <script src="{{ asset('js/parallax.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('js/simple-lightbox.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

</body>
</html>