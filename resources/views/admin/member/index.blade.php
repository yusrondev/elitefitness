@extends('layouts.backoffice')
@section('title', 'Roles')
@section('content')
<div class="container-fluid">
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
    <form action="{{ route('admin.data_member') }}" method="GET" class="row g-2 mb-4 align-items-center" id="searchForm">
        {{-- Kolom Search --}}
        <div class="col-md-4 col-12">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau barcode..." id="searchInput" value="{{ request('search') }}">
        </div>
    
        {{-- Dropdown Status --}}
        <div class="col-md-3 col-6">
            <select name="status" class="form-control">
                <option value="">-- Semua Status --</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
    
        {{-- Checkbox H-3 --}}
        <div class="col-md-2 col-6">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="off_soon" value="1" id="offSoonCheck" {{ request('off_soon') ? 'checked' : '' }}>
                <label class="form-check-label" for="offSoonCheck">H-3 (Akan Habis)</label>
            </div>
        </div>
    
        {{-- Tombol Submit --}}
        <div class="col-md-3 col-12">
            <button type="submit" class="btn btn-primary w-100">Cari</button>
        </div>
    </form>
    
    <!-- Kolom input tersembunyi khusus scanner -->
    <input type="text" id="barcodeInput" style="opacity:0; position:absolute; left:-9999px;" autofocus>
    @can('create-member')
        <button 
            type="button" 
            class="btn btn-primary" 
            data-bs-toggle="modal" 
            data-bs-target="#createMemberModal">
            Tambah Data
        </button>
    @endcan
    @foreach($active as $v_active)
    <div class="card mb-4" style="margin-top:10px;">
        <div class="card-body">
            <div class="row align-items-center">
                <!-- Barcode and Name -->
                <div class="col-xl-3 col-lg-6 col-sm-12 mb-3">
                    <div class="media-body">
                        <span class="text-primary d-block fs-18 font-w500 mb-1">#{{ $v_active->barcode }}</span>
                        <h3 class="fs-18 text-black font-w600">{{ $v_active->name_member }} <span style="font-size:14px;">( {{ $v_active->time_remaining }} )</span></h3>
                        <span class="d-block mb-2 fs-16">
                            <i class="fas fa-calendar me-2"></i>
                            Tanggal Daftar : {{ \Carbon\Carbon::parse($v_active->start_training)->format('d-m-Y') }}
                        </span>
                    </div>
                </div>

                <!-- Gender Image -->
                <div class="col-xl-2 col-lg-3 col-sm-4 col-6 mb-3 text-center">
                    @php
                        $fileExtension = pathinfo($v_active->upload, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                    @endphp
                    @if ($v_active->photo)
                        <img 
                            src="{{ asset('uploads/' . $v_active->photo) }}" 
                            alt="User Photo" 
                            class="img-fluid rounded-circle" 
                            style="width: 100px; height: 100px; object-fit: cover;"
                        >
                    @elseif ($v_active->upload)

                        @if ($isImage)
                            <img 
                                src="{{ asset('storage/app/public/' . $v_active->upload) }}" 
                                alt="Uploaded File" 
                                class="img-fluid rounded-circle" 
                                style="width: 100px; height: 100px; object-fit: cover;"
                            >

                        @else
                            <a 
                                href="{{ asset('storage/app/public/' . $v_active->upload) }}" 
                                target="_blank" 
                                class="btn btn-primary"
                            >
                                Lihat Berkas
                            </a>
                        @endif
                    @else
                        <img 
                            src="{{ asset('assets/images/default.png') }}" 
                            alt="Default Photo" 
                            class="img-fluid rounded-circle" 
                            style="width: 100px; height: 100px; object-fit: cover;"
                        >
                    @endif
                </div>

                <!-- QR Code -->
                <div class="col-xl-2 col-lg-3 col-sm-4 col-6 mb-3 text-center">
                    <img src="data:image/png;base64,{{ $v_active->qrcode }}" alt="Barcode for {{ $v_active->barcode }}" class="img-fluid" style="border: 2px solid #000; padding: 10px; background-color: #fff; width: 50%;">
                    <br><br>
                    @can('create-member')
                        <a href="{{ route('admin.members.cetakBarcode', $v_active->id) }}" target="_blank">
                            <button class="btn btn-primary">Cetak Barcode</button>
                        </a>
                    @endcan
                </div>

                <!-- End Date -->
                <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
                    <div class="d-flex align-items-center">
                        <svg class="me-3" width="55" height="55" viewbox="0 0 55 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="27.5" cy="27.5" r="27.5" fill="#886CC0"></circle>
                            <path d="M37.2961 23.6858C37.1797 23.4406 36.9325 23.2843 36.661 23.2843H29.6088L33.8773 16.0608C34.0057 15.8435 34.0077 15.5738 33.8826 15.3546C33.7574 15.1354 33.5244 14.9999 33.2719 15L27.2468 15.0007C26.9968 15.0008 26.7656 15.1335 26.6396 15.3495L18.7318 28.905C18.6049 29.1224 18.604 29.3911 18.7294 29.6094C18.8548 29.8277 19.0873 29.9624 19.3391 29.9624H26.3464L24.3054 38.1263C24.2255 38.4457 24.3781 38.7779 24.6725 38.9255C24.7729 38.9757 24.8806 39 24.9872 39C25.1933 39 25.3952 38.9094 25.5324 38.7413L37.2058 24.4319C37.3774 24.2215 37.4126 23.931 37.2961 23.6858Z" fill="white"></path>
                        </svg>
                        <div>
                            <small class="d-block fs-16 font-w400">End Date</small>
                            <span class="fs-18 font-w500">{{ \Carbon\Carbon::parse($v_active->end_training)->format('d-m-Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-xl-2 col-lg-6 col-sm-4 mb-3 text-end">
                    <div class="d-flex justify-content-end">

                    @php
                        if ($v_active->end_training >= now()) {
                            $status_label = 'Active';
                            $status_class = 'btn bgl-success text-success';
                        } else {
                            $status_label = 'Non-Active';
                            $status_class = 'btn bgl-danger text-danger';
                        }
                    @endphp

                    <span class="btn {{ $status_class }} fs-18 font-w600">{{ $status_label }}</span>
                        <div class="dropdown ms-4">
                            @can('create-member')
                                <div class="btn-link" data-bs-toggle="dropdown">
                                    <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z" stroke="#737B8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z" stroke="#737B8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z" stroke="#737B8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </div>
                            @endcan
                            <div class="dropdown-menu dropdown-menu-right">
                                <button
                                    type="button" 
                                    class="dropdown-item edit-data-member"
                                    data-id="{{ $v_active->id }}">
                                    Ubah Data
                                </button>
                                <a class="dropdown-item" href="{{ route('admin.information.member', $v_active->id) }}" target="_blank">
                                    Absensi
                                </a>
                                    <a class="dropdown-item" href="{{ $v_active->wa_link }}" target="_blank">
                                        Hubungi Sekarang
                                    </a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $v_active->id }}').submit();">Hapus</a>
                                <form id="delete-form-{{ $v_active->id }}" action="{{ route('admin.members.destroy', $v_active->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <!--<button-->
                                <!--    type="button" -->
                                <!--    class="dropdown-item edit-data-member"-->
                                <!--    data-id="{{ $v_active->id }}">-->
                                <!--    Check-In-->
                                <!--</button>-->
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    @endforeach
    <div class="progect-pagination d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="mb-3">
            Showing {{ $active->firstItem() }} to {{ $active->lastItem() }} of {{ $active->total() }} data
        </h4>
        <ul class="pagination mb-3">
            @if ($active->onFirstPage())
                <li class="page-item page-indicator disabled">
                    <a class="page-link" href="javascript:void(0)">
                        <i class="fas fa-angle-double-left me-2"></i>Previous
                    </a>
                </li>
            @else
                <li class="page-item page-indicator">
                    <a class="page-link" href="{{ $active->previousPageUrl() }}">
                        <i class="fas fa-angle-double-left me-2"></i>Previous
                    </a>
                </li>
            @endif

            @foreach ($active->getUrlRange(1, $active->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $active->currentPage() ? 'active' : '' }}">
                    <a class="page-link {{ $page == $active->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach

            @if ($active->hasMorePages())
                <li class="page-item page-indicator">
                    <a class="page-link" href="{{ $active->nextPageUrl() }}">
                        Next<i class="fas fa-angle-double-right ms-2"></i>
                    </a>
                </li>
            @else
                <li class="page-item page-indicator disabled">
                    <a class="page-link" href="javascript:void(0)">
                        Next<i class="fas fa-angle-double-right ms-2"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="createMemberModal" tabindex="-1" aria-labelledby="createMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createMemberModalLabel">Tambah Data Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createMemberForm" action="{{ route('admin.members.add_smember') }}" method="POST">
                @csrf
                <input type="hidden" name="total_price" id="input_total_price">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Member</label>
                            <input list="memberList" name="member_name" id="member_name" class="form-control" placeholder="Ketikan nama member..." required>
                            <datalist id="memberList">
                                @foreach ($member as $m)
                                    @if ($m->end_training < now())
                                        <option value="{{ $m->name }}"></option>
                                    @endif
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col">
                            <label class="form-label">Paket</label>
                            <select class="form-control" name="idpaket" id="idpaket" required onchange="calculateEndTraining()">
                                <option value="">-- Pilih Paket --</option>
                                @foreach ($paket as $p)
                                    <option value="{{ $p->id }}" data-days="{{ $p->days }}">{{ $p->packet_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Mulai Latihan</label>
                            <input type="date" class="form-control" name="start_training" id="start_training" required onchange="calculateEndTraining()">
                        </div>
                        <div class="col">
                            <label class="form-label">Akhir Latihan</label>
                            <input type="date" class="form-control" name="end_training" id="end_training" required readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Keterangan</label>
                            <input type="input" class="form-control" name="description">
                        </div>
                        <div class="col">
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <label for="total_price" class="form-label fw-bold">Total Price: </label>
                        <span id="total_price" class="fw-bold">0</span>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Member -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">Edit Data Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMemberForm" action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_member_id_gym">
                <input type="hidden" name="total_price" id="edit_input_total_price">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Member</label>
                            <select class="form-control" name="idmember" id="edit_idmember" required>
                                <option value="">-- Pilih Member --</option>
                                @foreach ($member as $m)
                                    <option value="{{ $m->users_id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Paket</label>
                            <select class="form-control" name="idpaket" id="edit_idpaket" required>
                                <option value="">-- Pilih Paket --</option>
                                @foreach ($paket as $p)
                                    <option value="{{ $p->id }}" data-days="{{ $p->days }}">{{ $p->packet_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Mulai Latihan</label>
                            <input type="date" class="form-control" name="start_training" id="edit_start_training" required onchange="calculateEditEndTraining()">
                        </div>
                        <div class="col">
                            <label class="form-label">Akhir Latihan</label>
                            <input type="date" class="form-control" name="end_training" id="edit_end_training" required readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Keterangan</label>
                            <input type="input" class="form-control" name="description" id="edit_description">
                        </div>
                        <div class="col">
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <label for="edit_total_price" class="form-label fw-bold">Total Price: </label>
                        <span id="edit_total_price" class="fw-bold">0</span>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const paketData = @json($paket->mapWithKeys(function($item) {
        return [$item->id => ['price' => $item->price, 'promote' => $item->promote]];
    }));

    function formatToRupiah(value) {
        return value.toLocaleString('id-ID', {
            style: 'currency',
            currency: 'IDR',
        });
    }

    // ===== Tambah Member =====
    const idPaketElement = document.getElementById('idpaket');
    const biayaElement = document.getElementById('biaya');
    const totalPriceElement = document.getElementById('total_price');
    const totalPriceInput = document.getElementById('input_total_price');
    const startTrainingInput = document.getElementById('start_training');
    const endTrainingInput = document.getElementById('end_training');

    function calculateTotalPrice() {
        const paket = paketData[idPaketElement?.value] || { price: 0, promote: 0 };
        const harga = parseInt(paket.promote) > 0 ? parseInt(paket.promote) : parseInt(paket.price);
        const biaya = parseInt(biayaElement?.value) || 0;
        const total = harga + biaya;

    
        if (totalPriceElement) totalPriceElement.textContent = formatToRupiah(total);
        if (totalPriceInput) totalPriceInput.value = total; // <-- jangan toFixed(2)
    }

    function calculateEndTraining() {
        const selectedPaket = idPaketElement?.options[idPaketElement.selectedIndex];
        const days = parseInt(selectedPaket?.getAttribute('data-days')) || 0;
        const start = startTrainingInput?.value;

        if (start) {
            const startDate = new Date(start);
            startDate.setDate(startDate.getDate() + days);
            if (endTrainingInput) endTrainingInput.value = startDate.toISOString().split('T')[0];
        } else {
            if (endTrainingInput) endTrainingInput.value = '';
        }
    }

    if (idPaketElement) idPaketElement.addEventListener('change', () => {
        calculateTotalPrice();
        calculateEndTraining();
    });

    if (biayaElement) biayaElement.addEventListener('input', calculateTotalPrice);
    if (startTrainingInput) startTrainingInput.addEventListener('change', calculateEndTraining);

    // ===== Edit Member =====
    const editIdPaketElement = document.getElementById('edit_idpaket');
    const editBiayaElement = document.getElementById('edit_biaya');
    const editTotalPriceElement = document.getElementById('edit_total_price');
    const editTotalPriceInput = document.getElementById('edit_input_total_price');
    const editStartTrainingInput = document.getElementById('edit_start_training');
    const editEndTrainingInput = document.getElementById('edit_end_training');
    const editDescription = document.getElementById('edit_description');

    function calculateEditTotalPrice() {
        const paket = paketData[editIdPaketElement?.value] || { price: 0, promote: 0 };
        const harga = parseInt(paket.promote) > 0 ? parseInt(paket.promote) : parseInt(paket.price);
        const biaya = parseInt(biayaElement?.value) || 0;
        const total = harga + biaya;

    
        if (editTotalPriceElement) editTotalPriceElement.textContent = formatToRupiah(total);
        if (editTotalPriceInput) editTotalPriceInput.value = total; // <-- jangan toFixed(2)
    }

    function calculateEditEndTraining() {
        const selectedPaket = editIdPaketElement?.options[editIdPaketElement.selectedIndex];
        const days = parseInt(selectedPaket?.getAttribute('data-days')) || 0;
        const start = editStartTrainingInput?.value;

        if (start) {
            const startDate = new Date(start);
            startDate.setDate(startDate.getDate() + days);
            if (editEndTrainingInput) editEndTrainingInput.value = startDate.toISOString().split('T')[0];
        } else {
            if (editEndTrainingInput) editEndTrainingInput.value = '';
        }
    }

    if (editIdPaketElement) editIdPaketElement.addEventListener('change', () => {
        calculateEditTotalPrice();
        calculateEditEndTraining();
    });

    if (editBiayaElement) editBiayaElement.addEventListener('input', calculateEditTotalPrice);
    if (editStartTrainingInput) editStartTrainingInput.addEventListener('change', calculateEditEndTraining);

    // Trigger hitung ulang saat modal edit terbuka
    const editMemberModal = document.getElementById('editMemberModal');
    if (editMemberModal) {
        editMemberModal.addEventListener('shown.bs.modal', function () {
            calculateEditTotalPrice();
            calculateEditEndTraining();
        });
    }

    // Ekspor formatToRupiah agar bisa dipakai di luar DOMContentLoaded
    window.formatToRupiah = formatToRupiah;
});
</script>

<script>
function replaceNormalDate(val) {
    return val.replace(/\s\d{2}:\d{2}:\d{2}/, "");
}

// === BUKA MODAL EDIT DAN LOAD DATA ===
$('body').on('click', '.edit-data-member', function(e){
    e.preventDefault();
    let id = $(this).data('id')

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $.post('/api/member-data', { id: id }, function(res){
        $("#editMemberModal").modal('show');

        $("#edit_idmember").val(res.idmember).change();
        $("#edit_idpaket").val(res.idpaket).change();
        $("#edit_member_id_gym").val(res.id);
        $("#edit_idtrainer").val(res.idtrainer).change();
        $("#edit_start_training").val(replaceNormalDate(res.start_training));
        $("#edit_end_training").val(replaceNormalDate(res.end_training));
        $("#edit_input_total_price").val(res.total_price);
        $("#edit_description").val(res.description);

        if (res.trainer != null) {
            setTimeout(() => {
                $("#edit_useTrainer").prop("checked", true);
                $("#edit_trainerSection").show();
                $("#edit_idpacket_trainer").val(res.trainer.id).change();
                $("#edit_biaya").val(res.trainer.price);
                $("#edit_point").val(res.trainer.poin);

                $("#edit_total_price").html(formatToRupiah(res.total_price));
            }, 100);
        }
    });
});

// === SUBMIT FORM EDIT ===
$('body').on('submit', '#editMemberForm', function(e) {
    e.preventDefault();

    let id = $("#edit_member_id_gym").val();
    let url = `/admin/members/update/${id}`;

    $.post(url, $(this).serialize(), function(response) {
        $("#editMemberModal").modal('hide');
        swal("Berhasil", "Data berhasil diperbarui", "success");
        setTimeout(() => window.location.reload(), 1000);
    }).fail(function() {
        alert("Terjadi kesalahan!");
    });
});
</script>

<script>
    // === HANDLE SCAN BARCODE KE INPUT PENCARIAN OTOMATIS ===
    let scanBuffer = '';
    let scanTimeout;

    document.addEventListener('keydown', function (e) {
        // Abaikan kalau user sedang mengetik di input/textarea
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }
    
        if (e.key.length === 1) {
            scanBuffer += e.key;
            clearTimeout(scanTimeout);
    
            scanTimeout = setTimeout(function () {
                if (scanBuffer.length >= 5) {
                    const searchInput = document.getElementById('searchInput');
                    const searchForm = document.getElementById('searchForm');
    
                    if (searchInput && searchForm) {
                        searchInput.value = scanBuffer;
                        searchForm.submit();
                    }
    
                    scanBuffer = '';
                }
            }, 100);
        }
    });

</script>
@endsection