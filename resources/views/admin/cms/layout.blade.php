@extends('layouts.backoffice')
@section('content')
    <form action="{{ route('cms.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="container-fluid">
            <div class="card card-jelajahi">
                <div class="card-header">
                    <h4 class="card-title"><b>Section : Info Company</b></h4>
                </div>
                <div class="card-body">
                    <div class="card card-body">
                        <div class="form-group">
                            <input value="{{ @$content['company']['company_name'] }}" type="text" class="input-field-t-sm"
                                placeholder="BEST FITNESS IN THE TOWN" name="company_name">
                        </div>
                        <div class="form-group mt-2">
                            <label for="" class="bg-text"><b>Logo</b></label><br>
                            <img src="{{ asset('uploads/company_logo.png') }}" class="service_img">
                            <input type="file" name="company_logo" class="_company-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><b>Section : Hero</b></h4>
                </div>
                <div class="card-body card-hero">
                    <div class="form-group">
                        <input value="{{ @$content['hero']['hero_mini_text'] }}" type="text" class="_hero-mini-text"
                            placeholder="BEST FITNESS IN THE TOWN" name="hero_mini_text">
                    </div>
                    <div class="form-group mt-2">
                        <textarea rows="3" class="_hero-description" placeholder="MAKE YOUR BODY SHAPE" name="hero_description">{{ @$content['hero']['hero_description'] }}</textarea>
                    </div>
                    <div class="form-group mt-2">
                        <textarea rows="3" col="10" name="hero_short_description" class="_hero-short-description"
                            placeholder="Bebaskan potensi Anda dan mulailah bentuk tubuh yang lebih kuat, lebih bugar, dan lebih percaya diri. Daftar 'Bentuk Tubuh Anda' sekarang dan saksikan transformasi luar biasa yang dapat dilakukan oleh tubuh Anda!">{{ @$content['hero']['hero_short_description'] }}</textarea>
                    </div>
                    <div class="form-group mt-2">
                        <label for="" class="bg-text">Background</label>
                        <input type="file" name="hero_image" class="_hero-image">
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card card-jelajahi">
                <div class="card-header">
                    <h4 class="card-title"><b>Section : Jelajahi Program Kami</b></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <select name="program1_icon" class="_program1_icon icon-program">
                                        <option value="">Pilih Icon</option>
                                        @foreach ($icons as $value => $label)
                                            <option value="{{ $value }}"
                                                @if (@$content['jelajahi']['program1_icon'] == $value) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <h1 class="card-title">
                                        <input type="text" name="program1_title"
                                            value="{{ @$content['jelajahi']['program1_title'] }}"
                                            class="_program1 field-program" placeholder="Program 1...">
                                    </h1>
                                </div>
                                <div class="card-body">
                                    <textarea name="program1_description" class="_program1_description textarea-program" placeholder="Misalnya...">{{ @$content['jelajahi']['program1_description'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <select name="program2_icon" class="_program2_icon icon-program">
                                        <option value="">Pilih Icon</option>
                                        @foreach ($icons as $value => $label)
                                            <option value="{{ $value }}"
                                                @if (@$content['jelajahi']['program2_icon'] == $value) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <h1 class="card-title">
                                        <input type="text" name="program2_title" class="_program2 field-program"
                                            value="{{ @$content['jelajahi']['program2_title'] }}"
                                            placeholder="Program 2...">
                                    </h1>
                                </div>
                                <div class="card-body">
                                    <textarea name="program2_description" class="_program2_description textarea-program" placeholder="Misalnya...">{{ @$content['jelajahi']['program2_description'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <select name="program3_icon" class="_program3_icon icon-program">
                                        <option value="">Pilih Icon</option>
                                        @foreach ($icons as $value => $label)
                                            <option value="{{ $value }}"
                                                @if (@$content['jelajahi']['program3_icon'] == $value) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <h1 class="card-title">
                                        <input type="text" name="program3_title" class="_program3 field-program"
                                            value="{{ @$content['jelajahi']['program3_title'] }}"
                                            placeholder="Program 3...">
                                    </h1>
                                </div>
                                <div class="card-body">
                                    <textarea name="program3_description" class="_program3_description textarea-program" placeholder="Misalnya...">{{ @$content['jelajahi']['program3_description'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <select name="program4_icon" class="_program4_icon icon-program">
                                        <option value="">Pilih Icon</option>
                                        @foreach ($icons as $value => $label)
                                            <option value="{{ $value }}"
                                                @if (@$content['jelajahi']['program4_icon'] == $value) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <h1 class="card-title">
                                        <input type="text" name="program4_title" class="_program4 field-program"
                                            value="{{ @$content['jelajahi']['program4_title'] }}"
                                            placeholder="Program 4...">
                                    </h1>
                                </div>
                                <div class="card-body">
                                    <textarea name="program4_description" class="_program4_description textarea-program" placeholder="Misalnya...">{{ @$content['jelajahi']['program4_description'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card card-service">
                <div class="card-header">
                    <h4 class="card-title"><b>Section : Service</b></h4>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for=""><b>Gambar 1</b></label><br>
                                    <img src="{{ asset('uploads/service_image1.png') }}" class="service_img">
                                    <input type="file" name="service_image1" class="_service-image1">
                                </div>
                                <div class="form-group mt-1">
                                    <label for=""><b>Gambar 2</b></label><br>
                                    <img src="{{ asset('uploads/service_image2.png') }}" class="service_img">
                                    <input type="file" name="service_image2" class="_service-image2">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <input type="text" class="_service-title" name="service_title"
                                        placeholder="THE CLASS YOU WILL GET HERE"
                                        value="{{ @$content['service']['service_title'] }}">
                                </div>
                                <div class="form-group mt-1">
                                    <textarea rows="3" col="10" name="service_description" class="_service-description"
                                        placeholder="Dipimpin oleh tim instruktur ahli dan motivator kami, “Kelas yang Akan Anda Dapatkan di Sini” adalah sesi berenergi tinggi dan berorientasi pada hasil yang menggabungkan perpaduan sempurna antara latihan kardio, latihan kekuatan, dan latihan fungsional. Setiap kelas dirancang dengan cermat untuk membuat Anda tetap terlibat dan tertantang, memastikan Anda tidak pernah mencapai titik terendah dalam upaya kebugaran Anda.">{{ @$content['service']['service_description'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card card-jelajahi">
                <div class="card-header">
                    <h4 class="card-title"><b>Section : Why Join Us</b></h4>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <center>
                                    <div class="form-group">
                                        <input type="text" name="join_title"
                                            value="{{ @$content['join']['join_title'] }}" class="_join-title"
                                            placeholder="WHY JOIN US ?">
                                    </div>
                                    <div class="form-group mt-1">
                                        <textarea rows="3" col="10" name="join_description" class="_join-description"
                                            placeholder="Basis keanggotaan kami yang beragam menciptakan suasana yang bersahabat dan suportif, di mana Anda dapat menjalin pertemanan dan tetap termotivasi.">{{ @$content['join']['join_description'] }}</textarea>
                                    </div>
                                </center>
                            </div>
                        </div>
                        <div class="row mt-2 mb-2">
                            <center>
                                <div class="form-group">
                                    <label for=""><b>Gambar</b></label><br>
                                    <img src="{{ asset('uploads/join_image1.png') }}" class="service_img" alt=""><br>
                                    <input type="file" name="join_image1" class="_join-image1">
                                </div>
                            </center>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <select name="why_join_title1" id=""
                                            class="select-field _why-title-join1">
                                            <option value="">Pilih icon</option>
                                            @foreach ($icons as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (@$content['join']['why_join_title1'] == $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="why_join_description1"
                                            value="{{ @$content['join']['why_join_description1'] }}"
                                            class="input-field _why-desc-join">
                                    </div>
                                    <div class="card-body">
                                        <textarea name="why_join_long_description1" class="textarea-desc">{{ @$content['join']['why_join_long_description1'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <select name="why_join_title2" id=""
                                            class="select-field _why-title-join2">
                                            <option value="">Pilih icon</option>
                                            @foreach ($icons as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (@$content['join']['why_join_title2'] == $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="why_join_description2"
                                            value="{{ @$content['join']['why_join_description2'] }}"
                                            class="input-field _why-desc-join">
                                    </div>
                                    <div class="card-body">
                                        <textarea name="why_join_long_description2" class="textarea-desc">{{ @$content['join']['why_join_long_description2'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <select name="why_join_title3" id=""
                                            class="select-field _why-title-join3">
                                            <option value="">Pilih icon</option>
                                            @foreach ($icons as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (@$content['join']['why_join_title3'] == $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="why_join_description3"
                                            value="{{ @$content['join']['why_join_description3'] }}"
                                            class="input-field _why-desc-join">
                                    </div>
                                    <div class="card-body">
                                        <textarea name="why_join_long_description3" class="textarea-desc">{{ @$content['join']['why_join_long_description3'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid" style="display: none">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><b>Section : Pricing Plan</b></h4>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <center>
                                    <div class="form-group">
                                        <input type="text" name="pricing_title"
                                            value="{{ @$content['price']['pricing_title'] }}" class="_pricing-title _join-title"
                                            placeholder="OUR PRICING PLAN">
                                    </div>
                                    <div class="form-group mt-1">
                                        <textarea rows="3" col="10" name="pricing_description" class="_pricing-description _join-description"
                                            placeholder="Berikut paket harga kami dengan berbagai tingkatan sesuai kebutuhan Anda, masing-masing disesuaikan untuk memenuhi preferensi dan aspirasi kebugaran yang berbeda.">{{ @$content['price']['pricing_description'] }}</textarea>
                                    </div>
                                </center>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <select name="pricing_title1" id=""class="select-field">
                                            <option value="">Pilih icon</option>
                                            @foreach ($icons as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (@$content['price']['pricing_title1'] == $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="pricing_description1"
                                            value="{{ @$content['price']['pricing_description1'] }}"
                                            class="input-field">
                                    </div>
                                    <div class="card-body">
                                        <textarea name="pricing_long_description1" class="textarea-desc">{{ @$content['price']['pricing_long_description1'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <select name="pricing_title2" id=""
                                            class="select-field _why-title-join2">
                                            <option value="">Pilih icon</option>
                                            @foreach ($icons as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (@$content['price']['pricing_title2'] == $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="pricing_description2"
                                            value="{{ @$content['price']['pricing_description2'] }}"
                                            class="input-field">
                                    </div>
                                    <div class="card-body">
                                        <textarea name="pricing_long_description2" class="textarea-desc">{{ @$content['price']['pricing_long_description2'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <select name="pricing_title3" id=""
                                            class="select-field _why-title-join3">
                                            <option value="">Pilih icon</option>
                                            @foreach ($icons as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (@$content['price']['pricing_title3'] == $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="pricing_description3"
                                            value="{{ @$content['price']['pricing_description3'] }}"
                                            class="input-field">
                                    </div>
                                    <div class="card-body">
                                        <textarea name="pricing_long_description3" class="textarea-desc">{{ @$content['price']['pricing_long_description3'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><b>Section : About</b></h4>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <center>
                                    <div class="form-group mt-1">
                                        <textarea rows="3" col="10" name="about_description" class="_about-description _join-description"
                                            placeholder="Berikut paket harga kami dengan berbagai tingkatan sesuai kebutuhan Anda, masing-masing disesuaikan untuk memenuhi preferensi dan aspirasi kebugaran yang berbeda.">{{ @$content['about']['about_description'] }}</textarea>
                                    </div>
                                </center>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <select name="about_sosmed_icon1" id=""
                                            class="select-field _why-title-join1">
                                            <option value="">Pilih icon</option>
                                            @foreach ($sosmed as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (@$content['about']['about_sosmed_icon1'] == $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="about_sosmed_link1"
                                            value="{{ @$content['about']['about_sosmed_link1'] }}"
                                            class="input-field-t-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <select name="about_sosmed_icon2" id=""
                                            class="select-field _why-title-join2">
                                            <option value="">Pilih icon</option>
                                            @foreach ($sosmed as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (@$content['about']['about_sosmed_icon2'] == $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="about_sosmed_link2"
                                            value="{{ @$content['about']['about_sosmed_link2'] }}"
                                            class="input-field-t-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <select name="about_sosmed_icon3" id=""
                                            class="select-field _why-title-join3">
                                            <option value="">Pilih icon</option>
                                            @foreach ($sosmed as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if (@$content['about']['about_sosmed_icon3'] == $value) selected @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="about_sosmed_link3"
                                            value="{{ @$content['about']['about_sosmed_link3'] }}"
                                            class="input-field-t-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><b>Section : Template WA</b></h4>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mt-1">
                                    <textarea rows="3" col="10" name="whatsapp_message" class="_whatsapp_message"
                                        placeholder="">{{ @$content['whatsapp']['whatsapp_message'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success">Simpan semua perubahan</button>
                </div>
            </div>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let image_old_hero = "{{ asset('uploads/hero_image.png') }}";

            $(".card-body.card-hero").css({
                "background": `linear-gradient(rgba(47, 53, 66, 0), rgba(47, 53, 66, 0)), url(${image_old_hero})`,
                "background-size": "cover",
                "color" : "white !important",
                "background-position": "center",
                "background-repeat": "no-repeat"
            });

            $("._hero-image").on("change", function(event) {
                var file = event.target.files[0];

                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(".card-body.card-hero").css({
                            "background": `linear-gradient(rgba(47, 53, 66, 0), rgba(47, 53, 66, 0)), url(${e.target.result})`,
                            "background-size": "cover",
                            "color" : "white !important",
                            "background-position": "center",
                            "background-repeat": "no-repeat"
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
