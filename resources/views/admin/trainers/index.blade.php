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
				<div class="d-flex justify-content-between align-items-center flex-wrap">
                    <form action="{{ route('admin.trainers.index') }}" method="GET" class="input-group contacts-search mb-4">
                        <input type="text" name="search" class="form-control" placeholder="Search here..." value="{{ request('search') }}">
                        <button type="submit" class="input-group-text"><i class="flaticon-381-search-2"></i></button>
                    </form>
                </div>
				<div class="row">
					<div class="col-xl-12">
						<div class="row">
                            @foreach($active as $v_active)
                                <div class="col-xl-3 col-xxl-4 col-lg-4 col-md-6 col-sm-6 items">
                                    <div class="card contact-bx item-content">
                                        <div class="card-body user-profile">
                                            <div class="image-bx">
                                            @php
                                                $fileExtension = pathinfo($v_active->upload, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                                            @endphp
                                            @if ($v_active->photo)
                                                <img 
                                                    src="{{ asset('storage/app/public/' . $v_active->photo) }}" 
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
                                                <!-- <span class="active"></span> -->
                                            </div>
                                            <div class="media-body user-meta-info">
                                                <h6 class="fs-18 font-w600 my-1"><a href="app-profile.html" class="text-black user-name" data-name="Alan Green">{{ $v_active->name }}</a></h6>
                                                <p class="fs-14 mb-3 user-work" data-occupation="UI Designer">Tanggal Daftar : {{ \Carbon\Carbon::parse($v_active->created_at)->format('d-m-Y') }}</p>
                                                <ul>
                                                    <li>
                                                        <a href="https://wa.me/{{ '62' . substr($v_active->number_phone, 1) }}" target="_blank">
                                                            <i class="fas fa-phone-alt"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
						</div>
					</div>
					<div class="progect-pagination d-flex justify-content-between align-items-center flex-wrap mt-3">
						<h4 class="mb-3">Showing {{ $active->firstItem() }} to {{ $active->lastItem() }} of {{ $active->total() }} data</h4>
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
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">Edit Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="memberForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Nama Panggilan</label>
                            <input type="text" class="form-control" name="name" id="name" value="">
                        </div>
                        <div class="col">   
                            <label class="form-label">No. Telp</label>
                            <input type="text" class="form-control" name="notelp" id="notelp" maxlength="15" pattern="\d*" title="Hanya boleh angka">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editMemberModal = document.getElementById('editMemberModal');

        editMemberModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const notelp = button.getAttribute('data-notelp');
            const gender = button.getAttribute('data-gender');
            const pertemuan = button.getAttribute('data-pertemuan');
            const idpaket = button.getAttribute('data-idpaket');
            const idtrainer = button.getAttribute('data-idtrainer');
            const status = button.getAttribute('data-status');

            // Debugging untuk verifikasi
            console.log({ id, name, notelp, gender, pertemuan, idpaket, idtrainer, status });

            // Populate form fields
            const form = editMemberModal.querySelector('#memberForm');
            form.action = `/admin/members/update/${id}`; // Update form action dynamically

            form.querySelector('#name').value = name;
            form.querySelector('#notelp').value = notelp;
            form.querySelector('#gender').value = gender;
            form.querySelector('#email').value = email;
            form.querySelector('#status').value = status;

            // Update select options for Paket and Trainer
            const paketSelect = form.querySelector('#idpaket');
            const trainerSelect = form.querySelector('#idtrainer');

            Array.from(paketSelect.options).forEach(option => {
                option.selected = option.value === idpaket;
            });

            Array.from(trainerSelect.options).forEach(option => {
                option.selected = option.value === idtrainer;
            });
        });
    });
</script>

@include('admin.layout.footer')