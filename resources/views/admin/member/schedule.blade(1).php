@include('admin.layout.menu.tdashboard')
<body>
    <style>
        .nav-pills.light .nav-link.active, .nav-pills.light .show > .nav-link {
            background: var(--rgba-primary-1);
            color: black;
            box-shadow: none;
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
                <div class="tab-content">
                    <div class="card">
                        <div class="card-body">
                            <div id="calendar" class="fullcalendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trainingDateInput = document.getElementById('trainingDate');
        const memberSelect = document.getElementById('iduser');
        
        memberSelect.addEventListener('change', function () {
            const selectedMember = this.options[this.selectedIndex];
            const startDate = selectedMember.getAttribute('data-start');
            const endDate = selectedMember.getAttribute('data-end');

            if (startDate && endDate) {
                trainingDateInput.disabled = false;
                trainingDateInput.min = startDate;
                trainingDateInput.max = endDate;

                // Reset nilai input
                trainingDateInput.value = '';

                // Cek tanggal yang sudah digunakan dan disable input jika tanggal dipilih sudah ada
                const disabledDates = new Set(usedDates);
                trainingDateInput.addEventListener('input', function () {
                    const selectedDate = this.value;
                    if (disabledDates.has(selectedDate)) {
                        alert('Tanggal ini sudah dipilih. Pilih tanggal lain.');
                        this.value = ''; // Reset input
                    }
                });
            } else {
                trainingDateInput.disabled = true;
                trainingDateInput.value = '';
            }
        });
    });
</script>
<script>
    // Jika Anda melakukan permintaan AJAX, tambahkan CSRF token ke header permintaan
    function submitFormWithAjax() {
        const trainerId = document.getElementById('idtrainer').value;
        const dateTrainer = document.getElementById('date_trainer').value;
        const sessionNumber = document.getElementById('session_number').value;

        fetch('/trainers/store-schedule', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken, // Sertakan CSRF token di header
            },
            body: JSON.stringify({
                trainer_id: trainerId,
                date_trainer: dateTrainer,
                session_number: sessionNumber,
            }),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            alert('Data berhasil dikirim!');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim data.');
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trainingDateInput = document.getElementById('trainingDate');
        const memberSelect = document.getElementById('iduser');

        memberSelect.addEventListener('change', function () {
            const selectedMember = this.options[this.selectedIndex];
            const startDate = selectedMember.getAttribute('data-start');
            const endDate = selectedMember.getAttribute('data-end');

            if (startDate && endDate) {
                trainingDateInput.disabled = false;
                trainingDateInput.min = startDate;
                trainingDateInput.max = endDate;

                // Reset nilai input
                trainingDateInput.value = '';

                // Cek tanggal yang sudah digunakan
                trainingDateInput.addEventListener('input', function () {
                    const selectedDate = this.value;
                    if (usedDates.includes(selectedDate)) {
                        alert('Tanggal ini sudah digunakan. Pilih tanggal lain.');
                        this.value = ''; // Reset input
                    }
                });
            } else {
                trainingDateInput.disabled = true;
                trainingDateInput.value = '';
            }
        });
    });
</script>
<script>

    document.addEventListener('DOMContentLoaded', function () {
        fullCalender(events); // Menambahkan data events ke kalender
    });
</script>
<script>
    function updateDateRange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const startDate = selectedOption.getAttribute('data-start');
        const endDate = selectedOption.getAttribute('data-end');
        
        const dateInput = document.getElementById('date_trainer');
        if (startDate && endDate) {
            dateInput.min = startDate;
            dateInput.max = endDate;
            dateInput.disabled = false; // Aktifkan input tanggal
        } else {
            dateInput.disabled = true; // Nonaktifkan input tanggal jika tidak ada range
            dateInput.value = ''; // Reset value
        }
    }
</script>
<script>
  function checkScheduleLimit(select) {
    const selectedOption = select.options[select.selectedIndex];
    const limit = parseInt(selectedOption.getAttribute("data-limit"));
    const current = parseInt(selectedOption.getAttribute("data-current"));

    const notification = document.getElementById("notification");
    const dateInput = document.getElementById("date_trainer");
    const saveButton = document.getElementById("saveButton");

    if (current >= limit) {
      notification.classList.remove("d-none"); // Tampilkan notifikasi
      dateInput.disabled = true; // Nonaktifkan input tanggal
      saveButton.disabled = true; // Nonaktifkan tombol simpan
    } else {
      notification.classList.add("d-none"); // Sembunyikan notifikasi
      dateInput.disabled = false; // Aktifkan input tanggal
      saveButton.disabled = false; // Aktifkan tombol simpan
    }

    // Set range tanggal berdasarkan start dan end dari member yang dipilih
    const startDate = selectedOption.getAttribute("data-start");
    const endDate = selectedOption.getAttribute("data-end");
    dateInput.min = startDate;
    dateInput.max = endDate;
  }
</script>
<script>
"use strict"

function fullCalender(){
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
            @foreach($member_gym_schedule as $schedule)
                {
                    title: '{{ $schedule->name }} ( {{ $schedule->customer_name }} ) <br> ( {{ $schedule->start_time }} - {{ $schedule->end_time }} )', // Menggunakan <br> di title
                    start: '{{ $schedule->date_trainer }}',
                    color: 'blue', // Menentukan warna biru untuk event
                },
            @endforeach
        ],
        editable: false, // Nonaktifkan edit event
        droppable: false, // Nonaktifkan dragging event
        selectable: false, // Nonaktifkan memilih area untuk membuat event baru
        nowIndicator: true,
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