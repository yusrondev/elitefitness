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
                <div class="project-page d-flex justify-content-between align-items-center flex-wrap">
					<div class="project mb-4">
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-bs-toggle="tab" href="#AllStatus" role="tab">Kalender</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-bs-toggle="tab" href="#Calender" role="tab">Semua Data</a>
							</li>
						</ul>
					</div>
                    @can('view-trainer-add-schedule')
					<div class="mb-4">
                        <a href="javascript:void(0);" 
                            class="btn btn-primary btn-rounded fs-18" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalTambahInformasi">
                            + Tambah Informasi
                        </a>					
					</div>
                    @endcan
				</div>
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="AllStatus">
                        <div class="card">
                            <div class="card-body">
                                <div id="calendar" class="fullcalendar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="Calender">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Informasi Jadwal Training</h4>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table id="example" class="display">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Pelanggan</th>
                                                        <th>Nama Trainer</th>
                                                        <th>Tanggal Mulai Member</th>
                                                        <th>Tanggal Selesai Member</th>
                                                        <th>Jumlah Pertemuan</th>
                                                        <th>Sisa Pertemuan</th>
                                                        @can('view-member-add-schedule')
                                                        <th>Aksi</th>
                                                        @endcan
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($schedule as $row)
                                                        <tr>
                                                            <td>{{ $row->customer_name }}</td>
                                                            <td>{{ $row->name }}</td>
                                                            <td>{{ $row->training_date }}</td>
                                                            <td>{{ $row->training_end }}</td>
                                                            <td>{{ $row->sessions_taken }}</td>
                                                            <td>{{ $row->remaining_sessions }}</td>
                                                            @can('view-member-add-schedule')
                                                            <td>
                                                                <a href="javascript:void(0);" 
                                                                class="btn btn-primary fs-18" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalDetailInformasi"
                                                                data-iduser="{{ $row->iduser }}">
                                                                Detail
                                                                </a>
                                                            </td>
                                                            @endcan
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="modalTambahInformasi" tabindex="-1" aria-labelledby="modalTambahInformasiLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahInformasiLabel">Tambah Informasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="notification" class="alert alert-danger d-none" role="alert">
          Member ini telah mencapai batas maksimal jadwal.
        </div>
        <!-- Isi form atau informasi yang ingin Anda tampilkan -->
        <form action="{{ route('admin.trainers.storeSchedule') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Silahkan Pilih Member</label>
                <select id="memberSelect" name="iduser" class="form-control" onchange="updateDateRange(this)">
                    <option selected disabled>Pilih Member</option>
                    @foreach($member as $v_member2)
                        <option value="{{ $v_member2->idmember }}" 
                                data-start="{{ $v_member2->start_training }}" 
                                data-end="{{ $v_member2->end_training }}"
                                data-trainer-id="{{ $v_member2->idtrainer ?? '' }}"
                                data-trainer-name="{{ $v_member2->trainer_name ?? 'Tidak Ada Trainer' }}">
                            {{ $v_member2->name }}
                        </option>
                    @endforeach
                </select>
                <input type="date" class="form-control mt-3" name="date_trainer" id="date_trainer" disabled>
                @error('date_trainer')
                    <small class="text-danger">Tanggal Latihan tidak boleh kosong</small>
                @enderror
            </div>
        
            <div class="mb-3">
                <label class="form-label">Trainer</label>
                <input type="text" class="form-control" id="trainerName" readonly placeholder="Pilih member dulu">
                <input type="hidden" name="idtrainer" id="idtrainer">
                @error('idtrainer')
                    <small class="text-danger">Silahkan pilih trainer</small>
                @enderror
            </div>
        
            <div class="mb-3">
                <label class="form-label">Jam Mulai Latihan</label>
                <input type="time" class="form-control" name="start_time" id="start_time">
                @error('start_time')
                    <small class="text-danger">Jam Mulai Latihan tidak boleh kosong</small>
                @enderror
            </div>
        
            <div class="mb-3">
                <label class="form-label">Jam Selesai Latihan</label>
                <input type="time" class="form-control" name="end_time" id="end_time">
                @error('end_time')
                    <small class="text-danger">Jam Selesai Latihan tidak boleh kosong</small>
                @enderror
            </div>
        
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalDetailInformasi" tabindex="-1" aria-labelledby="modalDetailInformasiLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 80vw; margin: auto;">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalDetailInformasiLabel">Detail Informasi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <!-- Isi detail di sini -->
            <div class="row" id="scheduleCards">
            <!-- Data card akan ditambahkan lewat JavaScript -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this schedule?</p>
            </div>
            <div class="modal-footer">
                <form id="deleteScheduleForm" method="POST" action="">
                    @csrf
                    @method('DELETE') <!-- Method spoofing untuk DELETE -->
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const memberSelect = document.getElementById('memberSelect');
        const dateTrainer = document.getElementById('date_trainer');

        memberSelect.addEventListener('change', function () {
            updateDateRange(this);
            updateTrainer(this);
        });
    });

    function updateDateRange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const startDate = selectedOption.getAttribute('data-start');
        const endDate = selectedOption.getAttribute('data-end');

        const dateTrainer = document.getElementById('date_trainer');

        if (startDate && endDate) {
            dateTrainer.disabled = false;
            dateTrainer.min = startDate;
            dateTrainer.max = endDate;
            dateTrainer.value = ''; // Reset nilai input
        } else {
            dateTrainer.disabled = true;
            dateTrainer.value = '';
        }
    }

    function updateTrainer(select) {
        const selectedOption = select.options[select.selectedIndex];
        const trainerId = selectedOption.getAttribute('data-trainer-id');
        const trainerName = selectedOption.getAttribute('data-trainer-name');

        document.getElementById('trainerName').value = trainerName || 'Tidak Ada Trainer';
        document.getElementById('idtrainer').value = trainerId || '';
    }
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
    // Ketika tombol detail diklik
    document.querySelectorAll('.btn-primary').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-iduser');
            
            const url = '{{ route("admin.trainers.detailschedule", ":id") }}'.replace(':id', userId);
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const scheduleCardsContainer = document.getElementById('scheduleCards');
                    scheduleCardsContainer.innerHTML = ''; // Kosongkan card sebelumnya

                    // Loop untuk menampilkan setiap data dalam card, dan setiap card berisi form input
                    data.forEach(schedule => {
                        const card = `
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Trainer : ${schedule.trainer_name}</h5>
                                        <p>( Tanggal : ${schedule.training_date} )</p>
                                        <form action="{{ route('admin.trainers.updateschedule', '') }}/${schedule.id}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <!-- Hidden input untuk ID -->
                                            <input type="hidden" name="id" value="${schedule.id}">

                                            <!-- Trainer Select -->
                                            <div class="mb-3">
                                                <label for="trainerName-${schedule.id}" class="form-label">Pilih Trainer</label>
                                                <select class="default-select form-control wide" name="idtrainer" id="trainerName-${schedule.id}" required>
                                                    <option value="${schedule.idtrainer}">
                                                        ${schedule.trainer_name}
                                                    </option>
                                                    @foreach($trainers as $trainer)
                                                        <option value="{{ $trainer->id }}">
                                                            {{ $trainer->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Tanggal Latihan -->
                                            <div class="mb-3">
                                                <label for="trainingDate-${schedule.id}" class="form-label">Tanggal Latihan</label>
                                                <input type="date" class="form-control" id="trainingDate-${schedule.id}" name="training_date" 
                                                value="${(new Date(schedule.training_date)).toLocaleDateString('en-CA')}" required>
                                            </div>

                                            <!-- Jam Mulai -->
                                            <div class="mb-3">
                                                <label for="startTime-${schedule.id}" class="form-label">Jam Mulai</label>
                                                <input type="time" class="form-control" id="startTime-${schedule.id}" name="start_time" value="${schedule.start_time}" required>
                                            </div>

                                            <!-- Jam Selesai -->
                                            <div class="mb-3">
                                                <label for="endTime-${schedule.id}" class="form-label">Jam Selesai</label>
                                                <input type="time" class="form-control" id="endTime-${schedule.id}" name="end_time" value="${schedule.end_time}" required>
                                            </div>

                                            <div class="mb-4 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success btn-rounded fs-18">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-rounded fs-18 ms-3"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="${schedule.id}"
                                                    data-route="{{ route('admin.trainers.deleteschedule', ['id' => ':id']) }}">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        `;
                        scheduleCardsContainer.innerHTML += card; // Tambahkan card ke dalam baris
                    });
                })
                .catch(error => console.log('Error:', error));
        });
    });
</script>
<script>
    // Tangkap event klik tombol delete
    $(document).on('click', '[data-bs-target="#deleteModal"]', function () {
        var scheduleId = $(this).data('id');  // Ambil id dari data-id tombol
        var route = $(this).data('route');    // Ambil route dari data-route tombol

        // Update form action di modal dengan route yang sesuai
        var formAction = route.replace(':id', scheduleId);  // Gantikan :id dengan scheduleId
        $('#deleteScheduleForm').attr('action', formAction); // Update action form
    });
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