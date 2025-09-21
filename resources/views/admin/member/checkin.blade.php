@include('admin.layout.menu.tdashboard')

<body>
    <style>
        .nav-pills.light .nav-link.active, .nav-pills.light .show > .nav-link {
            background: var(--rgba-primary-1);
            color: black;
            box-shadow: none;
        }
        .fc-event {
            border-radius: 50%; /* Membuat event berbentuk bulat */
            text-align: center; /* Menyusun teks agar berada di tengah */
            padding: 10px; /* Memberikan padding pada event */
            font-size: 12px; /* Ukuran font */
            font-weight: bold; /* Menebalkan teks */
            color: white; /* Warna teks putih */
            display: flex; /* Untuk memastikan konten event bisa diatur secara fleksibel */
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 100px;
            display: flex;
        }

        /* Menyesuaikan background color untuk event */
        .fc-event-red {
            background-color: red; /* Ubah warna latar belakang event sesuai kebutuhan */
        }

        /* Style untuk event title */
        .fc-event-title {
            color: white; /* Menjaga warna teks tetap putih */
            font-size: 15px;
            font-weight: bold;
        }

        /* Custom Style for Calendar Days */
        .fc-daygrid-day {
            font-size: 14px; /* Ukuran font untuk tanggal */
            font-weight: normal;
        }

        .fc-daygrid-day-number {
            font-size: 14px; /* Ukuran font untuk nomor hari */
            font-weight: bold;
        }

        /* Hover effect for events */
        .fc-event:hover {
            opacity: 0.8; /* Efek hover untuk menambah sedikit transparansi */
            cursor: pointer;
        }

        /* Style for active calendar day */
        .fc-daygrid-day.fc-day-today {
            background-color: #f5f5f5; /* Memberikan background light-gray untuk hari ini */
        }
    </style>

    <div id="preloader">
		<div class="lds-ripple">
			<div></div>
			<div></div>
		</div>
    </div>
   
    <div id="main-wrapper">
        @include('admin.layout.menu.navbar')

        <div class="content-body">
            @yield('content')
			<div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                <div class="tab-content">

                    <div class="card">
                        <div class="card-body">

                        <form action="{{ route('admin.checkin') }}" method="GET" class="mb-3 d-flex align-items-center gap-2">
                            <label for="filter_date">Filter Tanggal:</label>
                            <input type="date" id="filter_date" name="filter_date" value="{{ request('filter_date', date('Y-m-d')) }}" class="form-control" style="max-width: 200px;">

                            <label for="filter_name">Filter Nama:</label>
                            <input type="text" id="filter_name" name="filter_name" value="{{ request('filter_name') }}" placeholder="Cari nama user..." class="form-control" style="max-width: 200px;">

                            <button type="submit" class="btn btn-primary">Cari</button>
                            <a href="{{ route('admin.checkin') }}" class="btn btn-secondary">Reset</a>
                        </form>

                            <div class="table-responsive">
                                <table id="example" class="display" style="min-width: 845px">
                                    <thead>
                                        <tr>
                                            <th>Nama Member</th>
                                            <th>No. Loker</th>
                                            <th>Check In</th>
                                            <th>Check Out</th>
                                            <!-- <th>Paket</th> -->
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schedule_member as $row)
                                            <tr>
                                                <td>{{ $row->name }}</td>
                                                <td>{{ $row->key_fob }}</td>
                                                <td>{{ $row->created_at }}</td>
                                                <td>
                                                    @if ($row->created_at != $row->updated_at)
                                                        {{ $row->updated_at }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($row->created_at == $row->updated_at)
                                                        <form action="{{ route('admin.checkout', $row->id) }}" method="POST">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="btn btn-primary">CheckOut</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div><br>
                            <div id="calendar" class="fullcalendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
"use strict";

function fullCalender() {
    /* initialize the calendar */
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'title,prev,next',
            right: 'today',
            center: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'dayGridMonth',
        events: [
            @foreach($events as $event)
                {
                    title: '{{ $event['title'] }}',  // Status dari checkin_member
                    start: '{{ $event['start'] }}',  // Tanggal check-in
                    color: '{{ $event['color'] }}',  // Warna event
                },
            @endforeach
        ],
        editable: false, // Nonaktifkan edit event
        droppable: false, // Nonaktifkan dragging event
        selectable: false, // Nonaktifkan memilih area untuk membuat event baru
        nowIndicator: true,
        eventRender: function(event, element) {
            // Gantikan title dengan status event
            element.find('.fc-title').html(event.title);

            // Menambahkan class sesuai dengan warna event
            if (event.color === 'red') {
                element.addClass('fc-event-red'); // Menambahkan class untuk warna merah
            }

            // Custom Styling untuk event
            element.css({
                'border-radius': '50%', 
                'text-align': 'center',
                'font-size': '12px', 
                'font-weight': 'bold', 
                'color': 'white',
                'display': 'flex',
                'align-items': 'center',
                'justify-content': 'center',
            });
        },
        eventContent: function(arg) {
            // Menangani event content untuk merender HTML
            var titleHtml = arg.event.title.replace(/<br>/g, '<br/>'); // Memastikan <br> dirender dengan benar
            return { html: '<div class="fc-event-title">' + titleHtml + '</div>' };
        },
        eventClick: function(info) {
            // Jangan lakukan apa-apa ketika event diklik
            info.jsEvent.preventDefault(); // Mencegah aksi default seperti menampilkan detail
        }
    });
    calendar.render();
}

jQuery(window).on('load',function(){
    setTimeout(function(){
        fullCalender();    
    }, 1000);
});
</script>

@include('admin.layout.footer')