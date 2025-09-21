@include('admin.layout.menu.tdashboard')
<body>

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
                <div class="card">
                        <div class="card-body">
                            @can('edit-schedule-trainer')
                            <button 
                            type="button" 
                            class="btn btn-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#createMemberModal">
                            Tambah Data
                        </button>
                        @endcan
                        <div class="table-responsive" style="margin-top:10px;">
                            <table id="example" class="display">
                                <thead>
                                    <tr>
                                        <th>Nama Trainer</th>
                                        <th>Jam Mulai</th>
                                        <th>Jam Mulai Istirahat</th>
                                        <th>Jam Selesai Istirahat</th>
                                        <th>Jam Selesai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedule as $v_schedule)
                                    <tr>
                                        <td>{{ $v_schedule->name }}</td>
                                        <td>Pkl.{{ $v_schedule->start_time }} WIB</td>
                                        <td>Pkl.{{ $v_schedule->start_break }} WIB</td>
                                        <td>Pkl.{{ $v_schedule->end_break }} WIB</td>
                                        <td>Pkl.{{ $v_schedule->end_time }} WIB</td>
                                        <td>
                                            @can('edit-schedule-trainer')
                                            <button type="button" class="btn btn-warning btn-edit" 
                                                data-id="{{ $v_schedule->id }}" 
                                                data-name="{{ $v_schedule->name }}"
                                                data-service_price="{{ $v_schedule->service_price }}"
                                                data-start_time="{{ $v_schedule->start_time }}"
                                                data-start_break="{{ $v_schedule->start_break }}"
                                                data-end_break="{{ $v_schedule->end_break }}"
                                                data-end_time="{{ $v_schedule->end_time }}"
                                                data-bs-toggle="modal" data-bs-target="#editMemberModal">
                                                Edit
                                            </button>
                                            
                                            <button type="button" class="btn btn-danger btn-delete" 
                                                data-id="{{ $v_schedule->id }}" 
                                                data-name="{{ $v_schedule->name }}"
                                                data-bs-toggle="modal" data-bs-target="#deleteMemberModal">
                                                Hapus
                                            </button>
                                            @endcan
                                        </td>
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
    <div class="modal fade" id="createMemberModal" tabindex="-1" aria-labelledby="createMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="createMemberForm" action="{{ route('admin.trainers.scheduleinformation') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <label>Nama trainer</label>
                                <select id="gender" class="default-select form-control wide" name="idtrainer">
                                    <option selected="" disabled>Silahkan Pilih Trainer</option>
                                    @foreach($trainers as $v_trainers)
                                        <option value="{{ $v_trainers->id }}">{{ $v_trainers->name }}</option>
                                    @endforeach
                                </select>
                                @error('idtrainer')
                                    <small class="text-danger">Silahkan pilih trainer</small>
                                @enderror
                            </div>
                            <div class="col">
                                <label>Harga Jasa</label>
                                <select id="gender" class="default-select form-control wide" name="service_price">
                                    <option selected="" disabled>Silahkan Pilih Paket</option>
                                        <option value="85000">Intermediate</option>
                                        <option value="110000">Advance</option>
                                        <option value="150000">Master</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <div class="col">
                                <label>Jam Mulai</label>
                                <input type="time" class="form-control" name="start_time" id="start_time">
                            </div>
                            <div class="col">
                                <label>Jam Mulai Istirahat</label>
                                <input type="time" class="form-control" name="start_break" id="start_break">
                            </div>
                            <div class="col">
                                <label>Jam Selesai Istirahat</label>
                                <input type="time" class="form-control" name="end_break" id="end_break">
                            </div>
                            <div class="col">
                                <label>Jam Selesai</label>
                                <input type="time" class="form-control" name="end_time" id="end_time">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="editMemberForm" action="{{ route('admin.trainers.scheduleinformation.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row">
                            <div class="col">
                                <label>Nama Trainer</label>
                                <input type="text" class="form-control" name="name" id="edit_name" readonly>
                            </div>
                            <div class="col">
                                <label>Harga Jasa</label>
                                <select id="gender" class="default-select form-control wide" name="service_price" id="edit_service_price">
                                    <option selected="" disabled>Silahkan Pilih Paket</option>
                                    @foreach($paket as $v_paket)
                                        <option value="{{ $v_paket->id }}">{{ $v_paket->packet_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <div class="col">
                                <label>Jam Mulai</label>
                                <input type="time" class="form-control" name="start_time" id="edit_start_time">
                            </div>
                            <div class="col">
                                <label>Jam Mulai Istirahat</label>
                                <input type="time" class="form-control" name="start_break" id="edit_start_break">
                            </div>
                            <div class="col">
                                <label>Jam Selesai Istirahat</label>
                                <input type="time" class="form-control" name="end_break" id="edit_end_break">
                            </div>
                            <div class="col">
                                <label>Jam Selesai</label>
                                <input type="time" class="form-control" name="end_time" id="edit_end_time">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-labelledby="deleteMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h5>Apakah Anda yakin ingin menghapus jadwal ini?</h5>
                    <p id="delete_info"></p>
                    <form id="deleteMemberForm" action="{{ route('admin.trainers.scheduleinformation.delete') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" id="delete_id">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Ketika tombol edit diklik
    $('.btn-edit').click(function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var service_price = $(this).data('service_price');
        var start_time = $(this).data('start_time');
        var start_break = $(this).data('start_break');
        var end_break = $(this).data('end_break');
        var end_time = $(this).data('end_time');

        // Set data ke dalam form di modal edit
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_service_price').val(service_price);
        $('#edit_start_time').val(start_time);
        $('#edit_start_break').val(start_break);
        $('#edit_end_break').val(end_break);
        $('#edit_end_time').val(end_time);
    });
});
</script>
<script>
$(document).ready(function () {
    // Ketika tombol delete diklik
    $('.btn-delete').click(function () {
        var id = $(this).data('id');
        var name = $(this).data('name');

        // Set data ke dalam modal delete
        $('#delete_id').val(id);
        $('#delete_info').text('Trainer: ' + name);
    });
});
</script>
<script>
function formatNumber(input) {
    // Hapus semua karakter non-digit
    let value = input.value.replace(/\D/g, '');

    // Format dengan titik sebagai pemisah ribuan
    input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function unformatNumber(value) {
    return value.replace(/\./g, ''); // hilangkan titik untuk kirim ke server
}

// Tambahkan event listener saat user mengetik
document.addEventListener('DOMContentLoaded', function () {
    const inputs = ['service_price', 'edit_service_price'];

    inputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function () {
                formatNumber(this);
            });
        }
    });
});
</script>
@include('admin.layout.footer')