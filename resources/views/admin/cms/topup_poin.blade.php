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
                                        <th>Deskripsi</th>
                                        <th>Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packages as $v_packages)
                                    <tr>
                                        <td>{{ $v_packages->description }}</td>
                                        <td>{{ $v_packages->price }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-edit" 
                                                data-id="{{ $v_packages->id }}"
                                                data-description="{{ $v_packages->description }}"
                                                data-price="{{ $v_packages->price }}"
                                                data-bs-toggle="modal" data-bs-target="#editMemberModal">
                                                Edit
                                            </button>
                                            
                                            <button type="button" class="btn btn-danger btn-delete" 
                                                data-id="{{ $v_packages->id }}" 
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
                    <form id="createMemberForm" action="{{ route('package.store_topup') }}" method="POST">
                        @csrf
                        <div class="row" style="margin-top:10px;">
                            <div class="col">
                                <label>Deskripsi</label>
                                <input type="text" class="form-control" name="description" id="description" required="">
                            </div>
                            <div class="col">
                                <label>Harga</label>
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
                    <form id="editMemberForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row" style="margin-top:10px;">
                            <div class="col">
                                <label>Deskripsi</label>
                                <input type="text" class="form-control" name="description" id="edit_description">
                            </div>
                            <div class="col">
                                <label>Harga</label>
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
                    <form id="deleteMemberForm" method="POST">
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
    $('.btn-edit').click(function () {
        var id = $(this).data('id');
        var description = $(this).data('description');
        var price = $(this).data('price');

        // Set data ke dalam form
        $('#edit_id').val(id);
        $('#edit_description').val(description);
        $('#edit_price').val(price);

        // Set form action secara dinamis
        var formAction = `/admin/cms/update-top_up/${id}`;
        $('#editMemberForm').attr('action', formAction);
    });
});
</script>
<script>
$(document).ready(function () {
    $('.btn-delete').click(function () {
        var id = $(this).data('id');

        // Set form action ke URL delete sesuai ID
        var formAction = `/admin/cms/delete-top_up/${id}`;
        $('#deleteMemberForm').attr('action', formAction);


        // Set ID ke input hidden (jika masih ingin kirim via input juga)
        $('#delete_id').val(id);
    });
});
</script>
@include('admin.layout.footer')