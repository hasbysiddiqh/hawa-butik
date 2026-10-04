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
    <title>Hawa Butik</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/linearicons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simplelightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
</head>
<body>

    <!-- Start Header Area -->
    <header class="default-header">
        <div class="container">
            <div class="header-wrap">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <a href="#home"><img src="{{ asset('img/logo.png') }}" alt="Hawa Butik"></a>
                    </div>
                    <div class="main-menubar d-flex align-items-center">
                        <nav class="hide">
                            <a href="#home">Home</a>
                            <a href="#about">About Me</a>
                            <a href="#gallery">Collection</a>
                            <a href="#contact">Contact</a>
                        </nav>
                        <div class="menu-bar"><span class="lnr lnr-menu"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- End Header Area -->

    <!-- Start Banner Area -->
    <section class="banner-area relative" id="home">
        <div class="slider">
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" role="listbox">
                    <!-- Slide One -->
                    <div class="carousel-item active" style="background-image: url('{{ asset('img/slider1.jpg') }}')">
                        <div class="carousel-caption d-md-block">
                            <h2 class="text-uppercase">KOLEKSI HAWA BUTIK</h2>
                            <p>
                                Temukan busana pilihan untuk tampil anggun di setiap kesempatan <br> Elegan • Modern • Berkelas
                            </p>
                        </div>
                    </div>
                    <!-- Slide Two -->
                    <div class="carousel-item" style="background-image: url('{{ asset('img/slider2.jpg') }}')">
                        <div class="carousel-caption d-md-block">
                            <h2 class="text-uppercase">READY FOR RENT</h2>
                            <p>
                                Sewa busana favoritmu dengan mudah untuk berbagai acara spesial <br> Tampil Cantik Tanpa Harus Membeli.
                            </p>
                        </div>
                    </div>
                    <!-- Slide Three -->
                    <div class="carousel-item" style="background-image: url('{{ asset('img/slider3.jpg') }}')">
                        <div class="carousel-caption d-md-block">
                            <h2 class="text-uppercase">LOOK YOUR BEST</h2>
                            <p>
                                Pilihan busana elegan untuk kondangan, wisuda, pesta, dan momen istimewa <br> Karena Setiap Momen Layak Dirayakan.
                            </p>
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
            <div class="row d-flex align-items-center">
                <div class="col-lg-6 about-left">
                    <img class="img-fluid" src="{{ asset('img/about-img.jpg') }}" alt="">
                </div>
                <div class="col-lg-6 about-right">
                    <h1>
                        We Believe that <br>
                        Every Woman Deserves to Look Elegant
                    </h1>
                    <p>
                        Hawa Butik hadir untuk menyediakan pilihan busana elegan yang dapat disewa untuk berbagai momen spesial. Dari acara formal hingga pesta dan perayaan, kami membantu Anda tampil anggun, percaya diri, dan memukau dengan busana yang tepat.
                    </p>
                    <a href="#contact" class="submit-btn primary-btn mt-20 text-uppercase">RENT NOW<span class="lnr lnr-arrow-right"></span></a>
                </div>
            </div>
        </div>
    </section>
    <!-- End About Area -->

    <!-- Start Gallery Area -->
    <section class="gallery-area section-gap" id="gallery">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 pb-30 header-text">
                    <h1 class="text-white">Our Collection</h1>
                    <p>
                        Jelajahi koleksi busana elegan Hawa Butik untuk melengkapi berbagai momen spesial Anda
                    </p>
                </div>
            </div>
            <div class="gal">
                @for ($i = 1; $i <= 16; $i++)
                    <a href="{{ asset('img/p' . $i . '.jpg') }}"><img src="{{ asset('img/p' . $i . '.jpg') }}" alt="Koleksi {{ $i }}"></a>
                @endfor
            </div>
        </div>
    </section>
    <!-- End Gallery Area -->

    <!-- Start Contact Area -->
    <section class="contact-area" id="contact">
        <div class="container-fluid">
            <div class="row d-flex justify-content-end align-items-center">
                <div class="col-lg-5 col-md-12 contact-left no-padding">
                    <img class="img-fluid" src="{{ asset('img/contact-img.jpg') }}" alt="">
                </div>
                <div class="col-lg-7 col-md-12 contact-right no-padding">
                    <h1>Hubungi Hawa Butik</h1>
                    <p>
                        Ingin menyewa busana untuk acara spesial? Kirimkan pesan kepada kami untuk informasi koleksi, ketersediaan, dan proses pemesanan.
                    </p>

                    <form class="booking-form" id="myForm" action="{{ route('pesanan.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 d-flex flex-column">
                                <input name="nama" type="text" placeholder="Nama Anda"
                                       class="form-control mt-20" value="{{ old('nama') }}" required>
                            </div>
                            <div class="col-lg-12 d-flex flex-column">
                                <input name="whatsapp" type="text" placeholder="Nomor WhatsApp"
                                       class="common-input mt-10" value="{{ old('whatsapp') }}" required>
                            </div>
                            <div class="col-lg-12 flex-column">
                                <textarea name="detail_pesanan" class="form-control mt-20"
                                          placeholder="Pesan/Detail Pesanan" required>{{ old('detail_pesanan') }}</textarea>
                            </div>

                            <div class="col-lg-12 d-flex justify-content-end send-btn">
                                <button type="submit" class="submit-btn primary-btn mt-20 text-uppercase">KONFIRMASI PEMESANAN<span class="lnr lnr-arrow-right"></span></button>
                            </div>

                            <div class="col-lg-12">
                                @if (session('success'))
                                    <div class="alert alert-success mt-20">{{ session('success') }}</div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger mt-20">{{ $errors->first() }}</div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- End Contact Area -->

    <!-- Start Footer Area -->
    <footer class="footer-area">
        <div class="container">
            <div class="row footer-bottom d-flex justify-content-between">
                <div class="col-lg-4 col-sm-12 footer-social">
                    <a href="https://www.instagram.com/hawa_butikk/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@hawa_butikk" target="_blank" rel="noopener" aria-label="TikTok">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    <!-- End Footer Area -->

    <script src="{{ asset('js/vendor/jquery-2.2.4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('js/jquery.sticky.js') }}"></script>
    <script src="{{ asset('js/parallax.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('js/simple-lightbox.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

    <script>
        // main.js punya handler AJAX lama (ke mail.php) yang memblokir submit form.
        // Handler itu dilepas supaya form dikirim normal ke Laravel.
        $(window).on('load', function () {
            $('#myForm').off('submit');
        });

        // Setelah submit, kembali ke bagian kontak agar pesan sukses/error terlihat
        @if (session('success') || $errors->any())
            window.location.hash = 'contact';
        @endif
    </script>
</body>
</html>