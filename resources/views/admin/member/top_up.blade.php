@include('admin.layout.menu.tdashboard')
<body>

    <style>
  .ts-wrapper.single-select1 {
      display: block;
      width: 100%;
      border: 1px solid #f5f5f5;
      border-radius: 1.25rem;
      background-color: #fff;
      transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
      font-size: 0.875rem;
      padding: 0rem 0.75rem;
      color: #393939;
  }

  .ts-wrapper.single-select1 .ts-control {
    background-color: transparent;
    border: none;
    box-shadow: none;
    min-height: 3.5rem;
    padding-left: 0;
    display: flex;
    align-items: center; /* Ini penting untuk menengahkan teks secara vertikal */
    }

  .ts-wrapper.single-select1 .ts-control input {
      padding: 0;
      margin: 0;
      line-height: 2rem;
  }

  .ts-wrapper.single-select1 .item {
      background-color: transparent;
      color: #393939;
      font-weight: 400;
  }

  .ts-wrapper.single-select1 .dropdown-content {
      border-radius: 0.75rem;
      border: 1px solid #eee;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }

  .ts-wrapper.single-select1 .dropdown-content .option {
      padding: 10px;
      cursor: pointer;
  }

  .ts-wrapper.single-select1 .dropdown-content .option:hover {
      background-color: #f0f0f0;
  }

  .ts-wrapper.single-select1 .no-results {
      color: #999;
      padding: 10px;
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
                <div class="card">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="card-header border-0 flex-wrap">
                        <h4 class="fs-20 font-w700 mb-2">Top Up Member</h4>
                    </div>
                    <div class="card-body">
                        <button 
                            type="button" 
                            class="btn btn-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#createMemberModal">
                            Tambah Data
                        </button><br><br>
                        <table id="example" class="display">
                            <thead>
                                <tr>
                                    <th>Nama User</th>
                                    <th>Tanggal Top Up</th>
                                    <th>Batas Penggunaan (Hari)</th>
                                    <th>Total Poin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $v_data)
                                @php
                                    try {
                                        $tanggalTopUp = \Carbon\Carbon::parse($v_data->datetop_up)->startOfDay();
                                        $batasPenggunaan = $tanggalTopUp->copy()->addDays((int) $v_data->day)->startOfDay();
                                        $hariIni = \Carbon\Carbon::now()->startOfDay();
                                        $sisaHari = $hariIni->diffInDays($batasPenggunaan, false);
                                    } catch (\Exception $e) {
                                        \Log::error("TopUp error: " . $e->getMessage());
                                        $tanggalTopUp = null;
                                        $sisaHari = null;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $v_data->name }}</td>
                                    <td>
                                        {{ $tanggalTopUp ? $tanggalTopUp->translatedFormat('d F Y') : 'Tanggal tidak valid' }}
                                    </td>
                                    <td>
                                        @if($sisaHari !== null && $sisaHari >= 0)
                                            {{ $sisaHari }} Hari
                                        @elseif($sisaHari !== null)
                                            <span style="color: red;">Expired</span>
                                        @else
                                            <span class="text-warning">Tanggal tidak valid</span>
                                        @endif
                                    </td>
                                    <td>{{ $v_data->total_poin }}</td>
                                    <td>
                                        <button
                                            type="button" 
                                            class="btn btn-primary"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editMemberModal"
                                            data-idtop="{{ $v_data->id }}"
                                            data-id="{{ $v_data->iduser }}"
                                            data-name="{{ $v_data->name }}"
                                            data-datetopup="{{ $tanggalTopUp ? $tanggalTopUp->format('Y-m-d') : '' }}"
                                            data-day="{{ $v_data->day }}"
                                            data-total_poin="{{ $v_data->total_poin }}"
                                            onclick="editTopup(this)">
                                            <i class="fas fa-eye"></i> Lihat Riwayat
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
    <div class="modal fade" id="createMemberModal" tabindex="-1" aria-labelledby="createMemberModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="createMemberModalLabel">Form Top Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form id="topupForm" action="{{ route('admin.members.add_topupmember') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                  <div class="row mb-3">
                    <div class="col">
                      <label class="form-label">Data Member</label>
                      <select class="single-select1" name="iduser" id="iduser" required>
                          <option value="">-- Pilih Member --</option>
                          @foreach ($data_user as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="col">
                      <label class="form-label">Data Top-up</label>
                      <select class="form-control" name="total_poin" id="total_poin" required>
                        <option value="">-- Pilih Paket Top-up --</option>
                        @foreach ($data_topup as $m)
                          <option value="{{ $m->id }}">{{ $m->packet_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col">
                      <select class="form-control" name="idtrainer" id="idtrainer" required>
                        <option value="">-- Pilih Trainer --</option>
                        @foreach ($trainer as $v_trainer)
                          <option value="{{ $v_trainer->id }}">{{ $v_trainer->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col">
                      <input type="date" class="form-control" name="datetop_up" id="datetop_up" required>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                  <button type="submit" class="btn btn-primary" id="submitButton">Simpan Data</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    
    <div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editMemberModalLabel">Riwayat Top Up</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                  <tr>
                    <th>Tanggal Top Up</th>
                    <th>Trainer</th>
                    <th>Batas (Hari)</th>
                    <th>Total Poin</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
              <tbody id="topupDetailBody">
                <!-- akan diisi oleh JS -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Terapkan Tom Select ke semua select dengan class 'single-select'
    document.querySelectorAll('.single-select1').forEach(function(selectEl) {
      new TomSelect(selectEl, {
        placeholder: "Cari atau pilih...",
        allowEmptyOption: true,
        maxOptions: 1000
      });
    });
  });
</script>

<script>
  const baseUrl = "{{ url('admin') }}";

  function setSelectValue(selectId, value) {
    const select = document.getElementById(selectId);
    let option = select.querySelector(`option[value="${value}"]`);
    if (!option && value !== "") {
      option = document.createElement('option');
      option.value = value;
      option.text = `Data tidak tersedia (ID: ${value})`;
      select.appendChild(option);
    }
    select.value = value;
  }

  function showCreateForm() {
    const form = document.getElementById('topupForm');
    form.reset();
    form.action = "{{ route('admin.members.add_topupmember') }}";

    // Hapus _method kalau ada
    const methodInput = document.getElementById('_method');
    if (methodInput) methodInput.remove();

    // Reset tombol
    document.getElementById('submitButton').textContent = 'Simpan Data';

    const modal = new bootstrap.Modal(document.getElementById('createMemberModal'));
    modal.show();
  }

  function editTopup(button) {
    const userId = button.getAttribute('data-id');
    console.log('user id : ', userId)
    fetch(`${baseUrl}/topup/detail/${userId}`)
      .then(response => response.json())
      .then(data => {
        let html = '';
        if (data.length === 0) {
          html = '<tr><td colspan="5" class="text-center">Belum ada data top-up</td></tr>';
        } else {
          data.forEach(item => {
            const tanggal = new Date(item.datetop_up).toLocaleDateString('id-ID', {
              day: '2-digit',
              month: 'long',
              year: 'numeric'
            });
            html += `
              <tr>
                <td>${tanggal}</td>
                <td>${item.name}</td>
                <td>${item.day} Hari</td>
                <td>${item.total_poin}</td>
                <td>
                  <button class="btn btn-sm btn-warning" onclick="showEditForm(${item.id})">Edit</button>
                  <button class="btn btn-sm btn-danger" onclick="deleteTopup(${item.id})">Hapus</button>
                </td>
              </tr>
            `;
          });
        }
        document.getElementById('topupDetailBody').innerHTML = html;
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editMemberModal'));
        modal.show();
      })
      .catch(error => {
        console.error('Gagal fetch data:', error);
      });
  }

  function showEditForm(id) {
    fetch(`${baseUrl}/topup/detailitem/${id}`)
      .then(res => res.json())
      .then(data => {
        const form = document.getElementById('topupForm');
        form.action = `${baseUrl}/topup/update/${data.id}`;

        if (!document.getElementById('_method')) {
          const methodInput = document.createElement('input');
          methodInput.type = 'hidden';
          methodInput.name = '_method';
          methodInput.value = 'PUT';
          methodInput.id = '_method';
          form.appendChild(methodInput);
        }

        document.getElementById('edit_id').value = data.id;
        setSelectValue('iduser', data.iduser);
        setSelectValue('total_poin', data.idtop_up);
        setSelectValue('idtrainer', data.idtrainer);
        document.getElementById('datetop_up').value = data.datetop_up;

        document.getElementById('submitButton').textContent = 'Update Data';

        const modal = new bootstrap.Modal(document.getElementById('createMemberModal'));
        modal.show();

        const modalRiwayat = bootstrap.Modal.getInstance(document.getElementById('editMemberModal'));
        if (modalRiwayat) modalRiwayat.hide();
      });
  }

  function deleteTopup(id) {
    if (confirm("Yakin ingin menghapus top-up ini?")) {
      fetch(`${baseUrl}/topup/delete/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
        .then(res => {
          if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
          return res.json();
        })
        .then(data => {
          alert(data.message);
          location.reload();
        })
        .catch(err => {
          console.error('Gagal menghapus:', err);
          alert('Terjadi kesalahan.');
        });
    }
  }
  
  function setSelectValue(selectId, value) {
      const select = document.getElementById(selectId);
      let option = select.querySelector(`option[value="${value}"]`);
      if (!option && value !== "") {
        option = document.createElement('option');
        option.value = value;
        option.text = `Data tidak tersedia (ID: ${value})`;
        select.appendChild(option);
      }
      select.value = value;
    
      // Jika pakai Tom Select
      if (select.tomselect) {
        select.tomselect.setValue(value);
      }
    }
</script>

@include('admin.layout.footer')