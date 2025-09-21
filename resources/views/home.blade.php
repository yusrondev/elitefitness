<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('assets/frontend/styles.css') }}" />
    <title>Home Page | {{ @$content['company']['company_name'] }}</title>

    <style>
      .logo-container {
          display: flex;
          align-items: center;
          text-decoration: none; /* Menghilangkan garis bawah link */
      }

      .logo-container img {
          margin-right: 10px; /* Spasi antara gambar dan teks */
      }

      .logo-container span {
          font-size: 18px; /* Atur ukuran font */
          color: #000; /* Warna teks */
      }
  </style>
  </head>
  <body>
    <nav>
      <div class="nav__logo">
      <a href="#" class="logo-container">
        <img src="{{ asset('uploads/company_logo.png') }}" alt="logo" style="width:25%;" />
        <span style="color:white"><strong>{{ @$content['company']['company_name'] }}</strong></span>
      </a>
      </div>
      <ul class="nav__links" id="menu-nav">
        <li class="link" id="menu-home"><a href="#header">Home</a></li>
        <li class="link" id="menu-program"><a href="#program">Program</a></li>
        <li class="link" id="menu-service"><a href="#service">Service</a></li>
        <li class="link" id="menu-about"><a href="#about">About</a></li>
        <li class="link" id="menu-community"><a href="#community">Community</a></li>
    </ul>

      <a href="https://forms.gle/VgTLn9x4R62NCUKa7">
        <button class="btn" style="color:black">Join Now</button>
    </a>
    <a href="{{ route('login') }}">
        <button class="btn" style="color:black">login</button>
    </a>
    </nav>

    <header class="section__container header__container">
      <div class="header__content">
        <span class="bg__blur"></span>
        <span class="bg__blur header__blur"></span>
        <h4>{{ @$content['hero']['hero_mini_text'] }}</h4>
        <h1>{{ @$content['hero']['hero_description'] }}</h1>
        <p>
          {{ @$content['hero']['hero_short_description'] }}
        </p>
        <!-- <button class="btn">Get Started</button> -->
      </div>
      <div class="header__image">
        <img src="{{ asset('uploads/hero_image.png') }}" alt="header" />
      </div>
    </header>

    <section id="program" class="section__container explore__container">
      <div class="explore__header">
        <h2 class="section__header">JELAJAHI PROGRAM KAMI</h2>
        <!-- <div class="explore__nav">
          <span><i class="ri-arrow-left-line"></i></span>
          <span><i class="ri-arrow-right-line"></i></span>
        </div> -->
      </div>
      <div class="explore__grid">
        <div class="explore__card">
          <span><i class="{{ @$content['jelajahi']['program1_icon'] }} style="color:black"></i></span>
          <h4>{{ @$content['jelajahi']['program1_title'] }}</h4>
          <p>
            {{ @$content['jelajahi']['program1_description'] }}
          </p>
          <!-- <a href="#">Join Now <i class="ri-arrow-right-line"></i></a> -->
        </div>
        <div class="explore__card">
          <span><i class="{{ @$content['jelajahi']['program2_icon'] }}" style="color:black"></i></span>
          <h4>{{ @$content['jelajahi']['program2_title'] }}</h4>
          <p>
            {{ @$content['jelajahi']['program2_description'] }}
          </p>
        </div>
        <div class="explore__card">
          <span><i class="{{ @$content['jelajahi']['program3_icon'] }} style="color:black"></i></span>
          <h4>{{ @$content['jelajahi']['program3_title'] }}</h4>
          <p>
            {{ @$content['jelajahi']['program3_description'] }}
          </p>
        </div>
        <div class="explore__card">
          <span><i class="{{ @$content['jelajahi']['program4_icon'] }}" style="color:black"></i></span>
          <h4>{{ @$content['jelajahi']['program4_title'] }}</h4>
          <p>
            {{ @$content['jelajahi']['program4_description'] }}
          </p>
        </div>
      </div>
    </section>

    <section id="service" class="section__container class__container">
      <div class="class__image">
        <span class="bg__blur"></span>
        <img src="{{ asset('uploads/service_image1.png') }}" alt="class" class="class__img-1" />
        <img src="{{ asset('uploads/service_image2.png') }}" alt="class" class="class__img-2" />
      </div>
      <div class="class__content">
        <h2 class="section__header">{{ @$content['service']['service_title'] }}</h2>
        <p>
          {{ @$content['service']['service_description'] }}
        </p>
        <!-- <button class="btn">Book A Class</button> -->
      </div>
    </section>

    <section id="about" class="section__container join__container">
      <h2 class="section__header">{{ @$content['join']['join_title'] }}</h2>
      <p class="section__subheader">
        {{ @$content['join']['join_description'] }}
      </p>
      <div class="join__image">
        <img src="{{ asset('uploads/join_image1.png') }}" alt="Join" />
        <div class="join__grid">
          <div class="join__card">
            <span><i class="{{ @$content['join']['why_join_title1'] }}" style="color:black"></i></span>
            <div class="join__card__content">
              <h4>{{ @$content['join']['why_join_description1'] }}</h4>
              <p>{{ @$content['join']['why_join_long_description1'] }}</p>
            </div>
          </div>
          <div class="join__card">
            <span><i class="{{ @$content['join']['why_join_title2'] }}" style="color:black"></i></span>
            <div class="join__card__content">
              <h4>{{ @$content['join']['why_join_description2'] }}</h4>
              <p>{{ @$content['join']['why_join_long_description2'] }}</p>
            </div>
          </div>
          <div class="join__card">
            <span><i class="{{ @$content['join']['why_join_title3'] }}" style="color:black"></i></span>
            <div class="join__card__content">
              <h4>{{ @$content['join']['why_join_description3'] }}</h4>
              <p>{{ @$content['join']['why_join_long_description3'] }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section__container price__container">
      <h2 class="section__header">OUR PRICING PLAN</h2>
      <p class="section__subheader">
        Berikut paket harga kami dengan berbagai tingkatan sesuai kebutuhan Anda, masing-masing disesuaikan untuk memenuhi preferensi dan aspirasi kebugaran yang berbeda.
      </p>
      <div class="price__grid">
        @foreach ($package as $item)
          <div class="price__card">
            <div class="price__card__content">
              <h4>{{ $item->packet_name }}</h4>
              <h3>
                @foreach ($package as $item)
                    @if ($item->promote != 0)
                        <span style="text-decoration: line-through; color: red; font-size: 20px">
                            Rp{{ number_format((float) $item->price, 0, ',', '.') }}
                        </span>
                        &nbsp;
                        <span style="font-size:30px;">
                            Rp{{ number_format((float) $item->promote, 0, ',', '.') }}
                        </span>
                    @else
                        Rp{{ number_format((float) $item->price, 0, ',', '.') }}
                    @endif
                @endforeach

              </h3>
              @if ($item->description)
                  @foreach ($item->description as $itemdesc)    
                    <p>
                      <i class="ri-checkbox-circle-line"></i>
                      {{ $itemdesc }}
                    </p>
                  @endforeach
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </section>

    <footer class="section__container footer__container">
      <span class="bg__blur"></span>
      <span class="bg__blur footer__blur"></span>
      <div class="footer__col">
        <div class="footer__logo">
          <a href="#" class="logo-container">
            <img src="{{ asset('uploads/company_logo.png') }}" alt="logo" style="width:25%;" />
            <span style="color:white"><strong>{{ @$content['company']['company_name'] }}</strong></span>
          </a>
        </div>
        <p>
          {{ @$content['about']['about_description'] }}
        </p>
        <div class="footer__socials">
          <a href="{{ @$content['about']['about_sosmed_link1'] }}"><i class="{{ @$content['about']['about_sosmed_icon1'] }}"></i></a>
          <a href="{{ @$content['about']['about_sosmed_link2'] }}"><i class="{{ @$content['about']['about_sosmed_icon2'] }}"></i></a>
          <a href="{{ @$content['about']['about_sosmed_link3'] }}"><i class="{{ @$content['about']['about_sosmed_icon3'] }}"></i></a>
        </div>
      </div>
      <div class="footer__col">
        <h4>Company</h4>
        <a href="#">Business</a>
        <a href="#">Franchise</a>
        <a href="#">Partnership</a>
        <a href="#">Network</a>
      </div>
      <div class="footer__col">
        <h4>About Us</h4>
        <a href="#">Blogs</a>
        <a href="#">Security</a>
        <a href="#">Careers</a>
      </div>
      <div class="footer__col">
        <h4>Contact</h4>
        <a href="#">Contact Us</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Terms & Conditions</a>
        <a href="#">BMI Calculator</a>
      </div>
    </footer>
    <div class="footer__bar">
      Copyright © {{ date('Y') }} - {{ @$content['company']['company_name'] }}. All rights reserved.
    </div>
    <script src="{{ asset('assets/frontend/script.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let currentIndex = 0; // Mulai dari review pertama
            const reviews = document.querySelectorAll('.review__content');

            // Fungsi untuk memperbarui tampilan
            function showReview(index) {
                reviews.forEach((review, i) => {
                    review.style.display = i === index ? 'block' : 'none';
                });
            }

            // Navigasi ke kiri
            document.querySelectorAll('.nav-left').forEach(button => {
                button.addEventListener('click', () => {
                    currentIndex = (currentIndex - 1 + reviews.length) % reviews.length;
                    showReview(currentIndex);
                });
            });

            // Navigasi ke kanan
            document.querySelectorAll('.nav-right').forEach(button => {
                button.addEventListener('click', () => {
                    currentIndex = (currentIndex + 1) % reviews.length;
                    showReview(currentIndex);
                });
            });

            // Tampilkan review pertama kali
            showReview(currentIndex);
        });
    </script>
  </body>

</html>