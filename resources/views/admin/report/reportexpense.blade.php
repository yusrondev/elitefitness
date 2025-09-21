@include('admin.layout.menu.tdashboard')

<body>
    <div id="preloader">
        <div class="lds-ripple"><div></div><div></div></div>
    </div>

    <div id="main-wrapper">
        @include('admin.layout.menu.navbar')

        <div class="content-body">
            @yield('content')
            @php
                $today = \Carbon\Carbon::today()->format('Y-m-d');
            @endphp
            <div class="container-fluid">
                <form action="{{ route('admin.report_allmoney') }}" method="GET">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Dari Tanggal:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="{{ request('start_date', $start_date ?? $today) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Sampai Tanggal:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="{{ request('end_date', $end_date ?? $today) }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.reportmoneyPDF', [
                                'start_date' => request('start_date', $start_date ?? $today),
                                'end_date' => request('end_date', $end_date ?? $today)
                            ]) }}" class="btn btn-danger ms-2" target="_blank">Cetak Excel</a>
                        
                            <!-- Tombol Tambah Data -->
                            <button 
                                type="button" 
                                class="btn btn-success ms-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#createExpenseModal">
                                Tambah Data
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>Pembuat</th>
                                        <th>Tanggal</th>
                                        <th>Total Pengeluaran</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $row)
                                        <tr>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->created_at }}</td>
                                            <td>Rp {{ number_format($row->money, 0, ',', '.') }}</td>
                                            <td>{{ $row->description }}</td>
                                            <td>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-warning btn-sm btn-edit-expense"
                                                    data-id="{{ $row->id }}"
                                                    data-iduser="{{ $row->iduser }}"
                                                    data-description="{{ $row->description }}"
                                                    data-money="{{ $row->money }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editExpenseModal">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.expense.destroy', $row->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
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
    
    <!-- Modal Tambah Data -->
    <div class="modal fade" id="createExpenseModal" tabindex="-1" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('admin.expense.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createExpenseModalLabel">Tambah Pengeluaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="money" class="form-label">Jumlah Uang</label>
                        <input type="number" class="form-control" name="money" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
      </div>
    </div>
    
    <!-- Modal Edit Pengeluaran -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditExpense" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExpenseModalLabel">Edit Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">
                <div class="mb-3">
                    <input type="hidden" class="form-control" name="iduser" id="edit-iduser" required>
                </div>
                <div class="mb-3">
                    <label for="edit-description" class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="description" id="edit-description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="edit-money" class="form-label">Jumlah Uang</label>
                    <input type="number" class="form-control" name="money" id="edit-money" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.btn-edit-expense');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const iduser = this.dataset.iduser;
            const description = this.dataset.description;
            const money = this.dataset.money;

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-iduser').value = iduser;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-money').value = money;

            // Set action untuk form update
            document.getElementById('formEditExpense').action = `/admin/report_allexpensemoney/update/${id}`;
        });
    });
});
</script>

    
@include('admin.layout.footer')