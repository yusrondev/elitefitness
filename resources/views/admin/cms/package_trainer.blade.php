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
                            <button 
                            type="button" 
                            class="btn btn-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#createMemberModal">
                            Tambah Data
                        </button>
                        <div class="table-responsive" style="margin-top:10px;">
                            <table id="example" class="display">
                                <thead>
                                    <tr>
                                        <th>Nama Paket</th>
                                        <th>Jumlah Pertemuan</th>
                                        <th>Poin</th>
                                        <th>Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedule as $v_schedule)
                                    <tr>
                                        <td>{{ $v_schedule->packet_name }}</td>
                                        <td>{{ $v_schedule->pertemuan }}</td>
                                        <td>{{ $v_schedule->poin }}</td>
                                        <td>{{ $v_schedule->price }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-edit" 
                                                data-id="{{ $v_schedule->id }}" 
                                                data-packet_name="{{ $v_schedule->packet_name }}"
                                                data-pertemuan="{{ $v_schedule->pertemuan }}"
                                                data-poin="{{ $v_schedule->poin }}"
                                                data-price="{{ $v_schedule->price }}"
                                                data-bs-toggle="modal" data-bs-target="#editMemberModal">
                                                Edit
                                            </button>
                                            
                                            <button type="button" class="btn btn-danger btn-delete" 
                                                data-id="{{ $v_schedule->id }}" 
                                                data-name="{{ $v_schedule->pertemuan }}"
                                                data-bs-toggle="modal" data-bs-target="#deleteMemberModal">
                                                Hapus
                                            </button>
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
                    <form id="createMemberForm" action="{{ route('admin.trainers.addpoin') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <label>Nama Paket</label>
                                <input type="text" class="form-control" name="packet_name" id="packet_name" required="">
                                @error('packet_name')
                                    <small class="text-danger">Silahkan melengkapi form ini</small>
                                @enderror
                            </div>
                            <div class="col">
                                <label>Jumlah Pertemuan</label>
                                <input type="text" class="form-control" name="pertemuan" id="pertemuan" required="">
                                @error('pertemuan')
                                    <small class="text-danger">Silahkan melengkapi form ini</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <div class="col">
                                <label>Jumlah Poin</label>
                                <input type="text" class="form-control" name="poin" id="poin" required="">
                            </div>
                            <div class="col">
                                <label>Harga Paket</label>
                                <input type="text" class="form-control" name="price" id="price" required="">
                            </div>
                            <div class="modal-footer" style="margin-top:10px;">
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
                    <form id="editMemberForm" action="{{ route('admin.trainers.poin.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row">
                            <div class="col">
                                <label>Nama Paket</label>
                                <input type="text" class="form-control" name="packet_name" id="edit_packet_name">
                            </div>
                            <div class="col">
                                <label>Jumlah Pertemuan</label>
                                <input type="text" class="form-control" name="pertemuan" id="edit_pertemuan">
                            </div>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <div class="col">
                                <label>Jumlah Poin</label>
                                <input type="text" class="form-control" name="poin" id="edit_poin">
                            </div>
                            <div class="col">
                                <label>Harga Paket</label>
                                <input type="text" class="form-control" name="price" id="edit_price">
                            </div>
                            <div class="modal-footer" style="margin-top:10px;">
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
                    <h5>Apakah Anda yakin ingin menghapus data ini?</h5>
                    <p id="delete_info"></p>
                    <form id="deleteMemberForm" action="{{ route('admin.trainers.poin.delete') }}" method="POST">
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
        var paket = $(this).data('packet_name');
        var pertemuan = $(this).data('pertemuan');
        var poin = $(this).data('poin');
        var price = $(this).data('price');

        // Set data ke dalam form di modal edit
        $('#edit_id').val(id);
        $('#edit_packet_name').val(paket);
        $('#edit_pertemuan').val(pertemuan);
        $('#edit_poin').val(poin);
        $('#edit_price').val(price);
    });
});
</script>
<script>
$(document).ready(function () {
    // Ketika tombol delete diklik
    $('.btn-delete').click(function () {
        var id = $(this).data('id');

        // Set data ke dalam modal delete
        $('#delete_id').val(id);
    });
});
</script>
@include('admin.layout.footer')