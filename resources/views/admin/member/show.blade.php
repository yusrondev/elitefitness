<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Fillow : Fillow Saas Admin Bootstrap 5 Template">
    <meta property="og:title" content="Fillow : Fillow Saas Admin Bootstrap 5 Template">
    <meta property="og:description" content="Fillow : Fillow Saas Admin Bootstrap 5 Template">
    <meta property="og:image" content="https://fillow.dexignlab.com/xhtml/social-image.png">
    <meta name="format-detection" content="telephone=no">
    
    <title>@yield('title', 'Informasi')</title>
    
    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link href="{{ asset('assets/vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/nouislider/nouislider.min.css') }}">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet" />
    
    <!-- Custom CSS for resizing calendar -->
    <style>
        /* Kalender */
        #calendar {
            width: 100%; /* Kalender mengambil seluruh lebar kontainer */
            height: 800px; /* Tinggi kalender */
            margin: 0 auto; /* Menengahkan kalender */
            box-sizing: border-box; /* Untuk memastikan padding dan border tidak mempengaruhi ukuran */
        }

        /* Menghilangkan toolbar kalender (prev, next, dll.) */
        .fc-toolbar {
            display: none; /* Sembunyikan toolbar */
        }

        /* Menghilangkan toolbar kalender */
        .fc-toolbar {
            display: none;
        }

        /* Ukuran font tanggal */
        .fc-day-number {
            font-size: 14px; /* Ukuran font default */
        }

        /* Ukuran font untuk judul event */
        .fc-title {
            font-size: 10px; /* Ukuran font default */
        }

        /* Styling untuk event */
        .fc-event {
            border-radius: 50% !important;
            background-color: red !important;
            color: white !important;
            text-align: center;
            padding: 10px;
            width: 50px;
            height: 50px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 40px;
        }

        /* Menyesuaikan tampilan judul event */

        /* Optional: Membuat border lebih jelas */
        .fc-event-inner {
            border-radius: 50% !important;
        }

        @media (max-width: 768px) {
        /* Mengurangi ukuran font tanggal */
        .fc-day-number {
            font-size: 12px;
        }

        /* Mengurangi ukuran font untuk judul event */
        .fc-title {
            font-size: 10px;
        }

        /* Menyesuaikan ukuran event */
        .fc-event {
            width: 40px;
            height: 40px;
            font-size: 10px;
            margin-left: 25px; /* Mengurangi margin untuk layar kecil */
        }
    }

    /* Responsif: Mengurangi ukuran font dan event lebih kecil pada layar ekstra kecil */
    @media (max-width: 480px) {
        /* Mengurangi ukuran font tanggal */
        .fc-day-number {
            font-size: 10px;
        }

        /* Mengurangi ukuran font untuk judul event */
        .fc-title {
            font-size: 8px;
        }

        /* Menyesuaikan ukuran event */
        .fc-event {
            width: 30px;
            height: 30px;
            font-size: 8px;
            margin-left: 15px; /* Mengurangi margin untuk layar sangat kecil */
        }
    }
    </style>
</head>
<body>
<div class="container" style="border: 5px solid #000; padding: 40px 20px; border-radius: 10px; margin-top: 30px;">
    <h1 style="text-align:center"><strong>Informasi Member</strong></h1>
    @if (session('success'))
        <div class="alert alert-success" style="text-align:center">
            {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger" style="text-align:center">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-center">
    @if ($member)
        @if ($member->upload)
            @php
                $fileExtension = pathinfo($member->upload, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
            @endphp

            @if ($isImage)
                <img 
                    src="{{ asset('storage/app/public/' . $member->upload) }}" 
                    alt="Uploaded File" 
                    class="img-fluid rounded-circle" 
                    style="width: 200px; height: 200px; object-fit: cover;"
                >
            @else
                <a 
                    href="{{ asset('storage/app/public/' . $member->upload) }}" 
                    target="_blank" 
                    class="btn btn-primary"
                >
                    Lihat Berkas
                </a>
            @endif
        @elseif ($member->photo)
            <img 
                src="{{ asset('storage/app/public/' . $member->photo) }}" 
                alt="User Photo" 
                class="img-fluid rounded-circle" 
                style="width: 200px; height: 200px; object-fit: cover;"
            >
        @else
            <img 
                src="{{ asset('assets/images/default.png') }}" 
                alt="Default Photo" 
                class="img-fluid rounded-circle" 
                style="width: 200px; height: 200px; object-fit: cover;"
            >
        @endif

        <!-- Mengonversi string ke objek Carbon dan memformat tanggal -->
    @else
        <p>Member tidak ditemukan.</p>
    @endif
    </div>

    <div class="container mt-4">
        <div style="margin-left:20%;">
            <div class="row">
                <div class="col-12 col-md-6">
                    <p><strong>Nama:</strong> {{ $member->name }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <p><strong>Nomor Telepon:</strong> {{ $member->number_phone }}</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 col-md-6">
                    <p><strong>Registrasi:</strong> 
                        {{ \Carbon\Carbon::parse($member->created_at)->timezone('Asia/Jakarta')->translatedFormat('j F Y') }}
                    </p>
                </div>
                <div class="col-12 col-md-6">
                    <p><strong>Sisa Jadwal</strong> 
                        @if ($remainingDays > 0)
                            {{ $remainingDays }} hari lagi
                        @elseif ($remainingDays === 0)
                            Sisa waktu habis
                        @else
                            Tidak ada informasi paket
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 col-md-6">
                    <p><strong>Paket:</strong> {{ $member->packet_name }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <p><strong>Trainer:</strong> {{ $trainer->name ?? '-' }} </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <p><strong>Keterangan:</strong> {{ $member->description ?? '' }}</p>
                </div>
                <div class="col-12 col-md-6">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        @if ($remainingDays > 0)
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkinModal">Check in</button>
        @elseif ($remainingDays === 0)
            
        @else
            Tidak ada informasi paket
        @endif
        <!-- Tombol Check In (aktif jika dalam rentang waktu) -->
        <!--@if ($isInRange)-->
            
        <!--@else-->
        <!--@can('view-checkin-schedule')-->
        <!--    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkinModal">Check in</button>-->
        <!--@endcan-->
        <!--@endif-->
    </div>

    <!-- Kalender FullCalendar -->
    <div style="text-align:center; margin-top: 20px;">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="checkinModal" tabindex="-1" aria-labelledby="checkinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkinModalLabel">Check In</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('checkin') }}" method="POST">
                @csrf
                    <div class="mb-3">
                        <input type="hidden" class="form-control" id="idmember" name="idmember" value="{{ $member->idmember }}">
                        <label for="key_fob" class="form-label">Key Fob</label>
                        <input type="text" class="form-control" id="key_fob" name="key_fob" placeholder="Masukkan Kunci">
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi FullCalendar
        $('#calendar').fullCalendar({
            header: {
                left: '',
                center: '',
                right: ''
            },
            events: @json($events),  
            eventRender: function(event, element) {
                element.find('.fc-title').html(event.status);
                element.css({
                    'border-radius': '50%', 
                    'text-align': 'center', 
                    'background-color': 'red', 
                    'color': 'white', 
                    'padding': '0px', 
                    'font-size': '10px', 
                    'font-weight': 'bold', 
                    'align-items': 'center', 
                });
            }
        });
    });
</script>

</body>
</html>