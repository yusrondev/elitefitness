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
                                ]) }}"
                               class="btn btn-danger ms-2" target="_blank">Cetak Excel</a>
                               
                             <button 
                                type="button" 
                                class="btn btn-success ms-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#createIncomeModal">
                                Tambah Data
                            </button>
                        </div>
                    </div>
                </form>

                @php
                    $totalTopup = $report->sum('price') + $report->sum('total_price');
                @endphp

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="class="table table-bordered table-striped"" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>Nama Member</th>
                                        <th>Nama Kasir</th>
                                        <th>Tanggal</th>
                                        <th>Total Transaksi</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report as $row)
                                        <tr class="{{ $row->type === 'expense' ? 'table-danger' : '' }}">
                                            <td>{{ $row->namamember }}</td>
                                            <td>{{ $row->namakasir }}</td>
                                            <td>{{ $row->tanggal_full }}</td>
                                            @php
                                                $jumlah = (int)($row->price ?? 0) + (int)($row->total_price ?? 0);
                                            @endphp
                                            <td>Rp {{ number_format($jumlah, 0, ',', '.') }}</td>
                                            <td>{{ $row->keterangan }}</td>
                                            <td>
                                            @if($row->id && $row->description && $row->money)
                                                <button 
                                                    type="button" 
                                                    class="btn btn-warning btn-sm btn-edit-income"
                                                    data-id="{{ $row->id }}"
                                                    data-iduser="{{ $row->iduser }}"
                                                    data-description="{{ $row->description }}"
                                                    data-money="{{ $row->money }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editIncomeModal">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.report_allmoney.destroy', $row->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Pemasukan</th>
                                        <th>Rp {{ number_format($totalTopupincome, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Pengeluaran</th>
                                        <th>Rp {{ number_format($totalExpense, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Sisa Saldo</th>
                                        <th>Rp {{ number_format($selisih, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <!-- Modal Tambah Data -->
    <div class="modal fade" id="createIncomeModal" tabindex="-1" aria-labelledby="createIncomeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('admin.report_allmoney.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createExpenseModalLabel">Tambah Pemasukan</h5>
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
    
    <div class="modal fade" id="editIncomeModal" tabindex="-1" aria-labelledby="editIncomeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form id="formEditIncome" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editIncomeModalLabel">Edit Pemasukan</h5>
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
            const editButtons = document.querySelectorAll('.btn-edit-income');
        
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
                    document.getElementById('formEditIncome').action = `/admin/report_allmoney/update/${id}`;
                });
            });
        });
        
        $('#example').DataTable({
            createdRow: function(row, data, dataIndex) {
                if (data[4] && data[4].includes('Pengeluaran')) {
                    $(row).css('background-color', '#ffe5e5');
                }
            }
        });

    </script>

    @include('admin.layout.footer')