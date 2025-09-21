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
                <!--<form id="memberForm" action="{{ route('admin.members.store') }}" method="POST">-->
                <form id="memberForm" action="{{ isset($member) ? route('admin.update_member', $member->id) : route('admin.members.store') }}" method="POST" enctype="multipart/form-data">
                    @if($errors->has('email'))
                        <div class="alert alert-danger mt-3">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                    <div class="row">
                        @csrf
                        @if(isset($member))
                            @method('PUT')
                        @endif
                        <input type="hidden" name="photo" id="photoInput">
                        <input type="hidden" name="upload" id="uploadedFileInput">
                        <div class="col">
                            <div class="card">
                                <div class="card-header border-0 flex-wrap">
                                    <h4 class="fs-20 font-w700 mb-2">Informasi Member</h4>
                                </div>
                                <div class="card-body">
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label class="form-label">Nama</label>
                                            <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $member->name ?? '') }}">
                                            @error('name')
                                                <small class="text-danger">Nama tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <!--<label class="form-label">Nama Lengkap</label>-->
                                            <!--<input type="text" class="form-control" name="full_name" id="full_name" value="{{ old('full_name', $member->full_name ?? '') }}">-->
                                            <!--@error('full_name')-->
                                            <!--    <small class="text-danger">Nama lengkap tidak boleh kosong</small>-->
                                            <!--@enderror-->
                                        </div>
                                    </div><br>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label class="form-label">No. Telp</label>
                                            <input type="text" class="form-control" name="number_phone" id="number_phone" maxlength="15" pattern="\d*" title="Hanya boleh angka" value="{{ old('number_phone', $member->number_phone ?? '') }}">
                                            @error('number_phone')
                                                <small class="text-danger">Nomor telepon tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label class="form-label">No. Telp Darurat </label>
                                            <input type="text" class="form-control" name="number_phoned" id="number_phone" maxlength="15" pattern="\d*" title="Hanya boleh angka" value="{{ old('number_phoned', $member->number_phoned ?? '') }}">
                                        </div>
                                    </div><br>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label class="form-label">Jenis Kelamin</label>
                                            <select id="gender" class="default-select form-control wide" name="gender">
                                                <option disabled {{ old('gender', $member->gender ?? '') == '' ? 'selected' : '' }}>Silahkan Pilih Gender</option>
                                                <option value="man" {{ old('gender', $member->gender ?? '') == 'man' ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="woman" {{ old('gender', $member->gender ?? '') == 'woman' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                            @error('gender')
                                                <small class="text-danger">Silahkan pilih jenis kelamin</small>
                                            @enderror
                                        </div>
                                        <div class="col"></div>
                                    </div><br>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label for="country" class="form-label">Negara</label>
                                            <select id="country" class="single-select" name="idcountry">
                                                <option disabled {{ old('idcountry', $member->idcountry ?? '') == '' ? 'selected' : '' }}>Silahkan Pilih Negara</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}" {{ old('idcountry', $member->idcountry ?? '') == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="state" class="form-label">Provinsi</label>
                                            <select id="state" class="single-select" name="idstate" {{ old('idstate', $member->idstate ?? '') ? '' : 'disabled' }}>
                                                <option selected disabled>Silahkan Pilih Provinsi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label for="city" class="form-label">Kota</label>
                                            <select id="city" class="single-select" name="idcities" {{ old('idcities', $member->idcities ?? '') ? '' : 'disabled' }}>
                                                @if(old('idcities') || !empty($member->idcities))
                                                    <option selected disabled>Loading...</option>
                                                @else
                                                    <option selected disabled>Silahkan Pilih Kota</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col"></div>
                                    </div>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label class="form-label">Alamat pertama</label>
                                            <input type="text" class="form-control" name="address" id="address" value="{{ old('address', $member->address ?? '') }}">
                                            @error('address')
                                                <small class="text-danger">Alamat tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Alamat kedua</label>
                                            <input type="text" class="form-control" name="address_2" value="{{ old('address_2', $member->address_2 ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top:10px">
                                        <div class="col">
                                            <label class="form-label">Kode Pos</label>
                                            <input type="number" class="form-control" name="portal_code" id="portal_code" value="{{ old('portal_code', $member->portal_code ?? '') }}">
                                            @error('portal_code')
                                                <small class="text-danger">Kode Pos tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                        <div class="col"></div>
                                    </div>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label class="form-label">Email</label>
                                            <input type="text" class="form-control" name="email" id="email" value="{{ old('email', $member->email ?? '') }}">
                                            @error('email')
                                                <small class="text-danger">Email tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Password</label>
                                            <input type="text" class="form-control" name="password" id="password">
                                            @error('password')
                                                <small class="text-danger">Password tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <div class="col"></div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header border-0 flex-wrap">
                                    <h4 class="fs-20 font-w700 mb-2">Identitas Member</h4>
                                </div>
                                <div class="row">
                                    <div class="col text-center">
                                        @if(!empty($member->photo))
                                            <img src="{{ asset('storage/app/public/' . $member->photo) }}" alt="Photo" width="100">
                                        @endif
                                        <button type="button" class="btn btn-primary mt-3" onclick="openCamera()">Buka Kamera</button>
                                    </div>
                                    <div class="col text-center">
                                        @if(!empty($member->upload))
                                            <img src="{{ asset('storage/app/public/' . $member->upload) }}" alt="Photo" width="100">
                                        @endif
                                        <button type="button" class="btn btn-primary mt-3" onclick="openFileUpload()">Unggah Berkas</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col text-center">
                                            <video id="videoStream" autoplay playsinline style="max-width: 100%; display: none;"></video>
                                            <canvas id="photoCanvas" style="display: none;"></canvas>
                                            <img id="capturedPhoto" src="#" alt="Hasil Foto" class="img-thumbnail" style="max-width: 200px; display: none;">
                                            <button id="savePhotoButton" type="button" class="btn btn-success mt-3" style="display: none;" onclick="savePhoto()">Simpan Gambar</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col text-center">
                                            <input type="file" id="fileInput" accept=".png,.jpg,.jpeg,.pdf,.docx" style="display: none;" onchange="previewFile()">
                                            <canvas id="fileCanvas" style="display: none;"></canvas>
                                            <img id="filePreview" src="#" alt="Preview Berkas" class="img-thumbnail" style="max-width: 200px; display: none;">
                                            <button id="saveFileButton" type="button" class="btn btn-success mt-3" style="display: none;" onclick="saveFile()">Simpan Berkas</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let videoStream;

    const videoElement = document.getElementById('videoStream');
    const canvasElement = document.getElementById('photoCanvas');
    const capturedPhoto = document.getElementById('capturedPhoto');
    const savePhotoButton = document.getElementById('savePhotoButton');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const saveFileButton = document.getElementById('saveFileButton');

    function showConfirmationModal() {
        const name = $('[name="name"]').val() || '-';
        const number_phone = $('[name="number_phone"]').val() || '-';
        const paket = $('[name="idpaket"] option:selected').text() || '-';
        const gender = $('[name="gender"] option:selected').text() || '-';
        const genderValue = $('[name="gender"]').val();

        $('#confirmName').text(name);
        $('#confirmnumber_phone').text(number_phone);
        $('#confirmPaket').text(paket.includes('Silahkan') ? '-' : paket);
        $('#confirmGender').text(gender.includes('Silahkan') ? '-' : gender);

        const genderImage = document.getElementById('genderModalImage');
        if (genderValue === 'man') {
            genderImage.src = "{{ asset('assets/images/man.png') }}";
            genderImage.style.display = "block";
        } else if (genderValue === 'woman') {
            genderImage.src = "{{ asset('assets/images/woman.png') }}";
            genderImage.style.display = "block";
        } else {
            genderImage.style.display = "none";
        }

        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        confirmationModal.show();
    }

    function submitForm() {
        document.getElementById('memberForm').submit();
    }

    async function openCamera() {
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({ video: true });
            videoElement.srcObject = videoStream;
            videoElement.style.display = 'block';
            savePhotoButton.style.display = 'block';
            capturedPhoto.style.display = 'none';
        } catch (error) {
            alert('Kamera tidak dapat diakses: ' + error.message);
        }
    }

    function savePhoto() {
        const context = canvasElement.getContext('2d');
        canvasElement.width = videoElement.videoWidth;
        canvasElement.height = videoElement.videoHeight;

        context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

        const imageData = canvasElement.toDataURL('image/png');
        $('#photoInput').val(imageData);

        capturedPhoto.src = imageData;
        capturedPhoto.style.display = 'block';

        videoStream.getTracks().forEach(track => track.stop());
        videoElement.style.display = 'none';
        savePhotoButton.style.display = 'none';
    }

    function openFileUpload() {
        fileInput.click();
    }

    function previewFile() {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                if (file.type.startsWith('image/')) {
                    filePreview.src = e.target.result;
                    filePreview.style.display = 'block';
                } else {
                    alert('Pratinjau hanya tersedia untuk berkas gambar.');
                }
                saveFileButton.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function saveFile() {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#uploadedFileInput').val(e.target.result);
                if (file.type.startsWith('image/')) {
                    filePreview.src = e.target.result;
                    filePreview.style.display = 'block';
                } else {
                    alert('File berhasil diunggah!');
                }
                saveFileButton.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            alert('Tidak ada file untuk disimpan.');
        }
    }

    $(document).ready(function () {
        const selectedCountry = '{{ old('idcountry', $member->idcountry ?? '') }}';
        const selectedState = '{{ old('idstate', $member->idstate ?? '') }}';
        const selectedCity = '{{ old('idcities', $member->idcities ?? '') }}';
        
        if (selectedCountry) {
            $('#state').prop('disabled', true).html('<option selected disabled>Loading...</option>');
            $.get('{{ route('admin.get.states') }}', { country_id: selectedCountry }, function (data) {
                let stateOptions = '<option disabled selected>Silahkan Pilih Provinsi</option>';
                data.forEach(function (state) {
                    stateOptions += `<option value="${state.id}" ${state.id == selectedState ? 'selected' : ''}>${state.name}</option>`;
                });
                $('#state').html(stateOptions).prop('disabled', false);
        
                if (selectedState) {
                    $('#city').prop('disabled', true).html('<option selected disabled>Loading...</option>');
                    $.get('{{ route('admin.get.cities') }}', { state_id: selectedState }, function (data) {
                        let cityOptions = '<option disabled selected>Silahkan Pilih Kota</option>';
                        data.forEach(function (city) {
                            cityOptions += `<option value="${city.id}" ${city.id == selectedCity ? 'selected' : ''}>${city.name}</option>`;
                        });
                        $('#city').html(cityOptions).prop('disabled', false);
                    });
                }
            });
        }
        $('#country').on('change', function () {
            const countryId = $(this).val();
            $('#state').prop('disabled', true).html('<option selected disabled>Loading...</option>');
            $('#city').prop('disabled', true).html('<option selected disabled>Silahkan Pilih Kota</option>');

            $.get('{{ route('admin.get.states') }}', { country_id: countryId }, function (data) {
                $('#state').html('<option selected disabled>Silahkan Pilih Provinsi</option>');
                data.forEach(state => {
                    $('#state').append(`<option value="${state.id}">${state.name}</option>`);
                });
                $('#state').prop('disabled', false);
            });
        });

        $('#state').on('change', function () {
            const stateId = $(this).val();
            $('#city').prop('disabled', true).html('<option selected disabled>Loading...</option>');

            $.get('{{ route('admin.get.cities') }}', { state_id: stateId }, function (data) {
                $('#city').html('<option selected disabled>Silahkan Pilih Kota</option>');
                data.forEach(city => {
                    $('#city').append(`<option value="${city.id}">${city.name}</option>`);
                });
                $('#city').prop('disabled', false);
            });
        });

        $('#confirmSaveButton').on('click', function () {
            if ($('#capturedPhoto').css('display') === 'none') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Foto Belum Diambil',
                    text: 'Silakan ambil foto terlebih dahulu sebelum menyimpan.',
                });
                return;
            }

            const name = $('#name').val();
            // const fullName = $('#full_name').val();
            const gender = $('#gender').val();
            const number_phone = $('#number_phone').val();
            const address = $('#address').val();
            const address2 = $('#address_2').val();
            const portal_code = $('#portal_code').val();
            const email = $('#email').val();
            const password = $('#password').val();

            Swal.fire({
                title: 'Konfirmasi Simpan',
                html: `
                    <div style="text-align: left;">
                        <p><b>Nama:</b> ${name || 'Tidak diisi'}</p>
                        <p><b>Jenis Kelamin:</b> ${gender || 'Tidak diisi'}</p>
                        <p><b>No. Telp:</b> ${number_phone || 'Tidak diisi'}</p>
                        <p><b>No. Telp Darurat:</b> ${number_phoned || 'Tidak diisi'}</p>
                        <p><b>Alamat Pertama:</b> ${address || 'Tidak diisi'}</p>
                        <p><b>Alamat Kedua:</b> ${address2 || 'Tidak diisi'}</p>
                        <p><b>Kode Pos:</b> ${portal_code || 'Tidak diisi'}</p>
                        <p><b>Email:</b> ${email || 'Tidak diisi'}</p>
                        <p><b>Password:</b> ${password || 'Tidak diisi'}</p>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#memberForm').submit();
                }
            });
        });
    });
</script>
@include('admin.layout.footer')