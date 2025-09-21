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
                <form id="memberForm" action="{{ route('admin.trainers.store') }}" method="POST">
                    <div class="row">
                        @csrf
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
                                            <input type="text" class="form-control" name="name" id="name">
                                            @error('name')
                                                <small class="text-danger">Nama tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            
                                        </div>
                                    </div><br>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label class="form-label">Jenis Kelamin</label>
                                            <select id="gender" class="default-select form-control wide" name="gender">
                                                <option selected="" disabled>Silahkan Pilih Gender</option>
                                                <option value="man">Laki-laki</option>
                                                <option value="woman">Perempuan</option>
                                            </select>
                                            @error('gender')
                                                <small class="text-danger">Silahkan pilih jenis kelamin</small>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label class="form-label">No. Telp</label>
                                            <input type="text" class="form-control" name="number_phone" id="number_phone" maxlength="15" pattern="\d*" title="Hanya boleh angka">
                                            @error('number_phone')
                                                <small class="text-danger">Nomor telepon tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                    </div><br>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label for="country" class="form-label">Negara</label>
                                            <select id="country" class="single-select" name="idcountry">
                                                <option selected disabled>Silahkan Pilih Negara</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="state" class="form-label">Provinsi</label>
                                            <select id="state" name="idstate" class="single-select" disabled>
                                                <option selected disabled>Silahkan Pilih Provinsi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label for="city" class="form-label">Kota</label>
                                            <select id="city" name="idcities" class="single-select" disabled>
                                                <option selected disabled>Silahkan Pilih Kota</option>
                                            </select>
                                        </div>
                                        <div class="col"></div>
                                    </div>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label class="form-label">Alamat pertama</label>
                                            <input type="text" class="form-control" name="address" id="address">
                                            @error('address')
                                                <small class="text-danger">Alamat tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Alamat kedua</label>
                                            <input type="text" class="form-control" name="address_2">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top:10px">
                                        <div class="col">
                                            <label class="form-label">Kode Pos</label>
                                            <input type="number" class="form-control" name="portal_code" id="portal_code">
                                            @error('portal_code')
                                                <small class="text-danger">Kode Pos tidak boleh kosong</small>
                                            @enderror
                                        </div>
                                        <div class="col"></div>
                                    </div>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <label class="form-label">Email</label>
                                            <input type="text" class="form-control" name="email" id="email">
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
                                            <button type="button" id="confirmSaveButton" class="btn btn-primary">Simpan</button>
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
                                        <button type="button" class="btn btn-primary mt-3" onclick="openCamera()">Buka Kamera</button>
                                    </div>
                                    <div class="col text-center">
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
                                            <input type="file" id="fileInput" accept=".png,.jpg,.jpeg" style="display: none;" onchange="previewFile()">
                                            <img id="filePreview" src="#" alt="Pratinjau Gambar" class="img-thumbnail" style="max-width: 200px; display: none;">
                                            <p id="errorMessage" style="color: red; display: none;">Format file tidak didukung! Hanya JPG, JPEG, dan PNG.</p>
                                            <input type="hidden" id="uploadedFileInput">
                                            <button id="saveFileButton" type="button" class="btn btn-success mt-3" style="display: none;" onclick="saveFile()">Simpan Gambar</button>
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
    function showConfirmationModal() {
        const name = document.getElementsByName('name')[0].value || '-';
        const number_phone = document.getElementsByName('number_phone')[0].value || '-';
        const paketSelect = document.getElementsByName('idpaket')[0];
        const paket = paketSelect.options[paketSelect.selectedIndex]?.text || '-';
        const genderSelect = document.getElementsByName('gender')[0];
        const gender = genderSelect.options[genderSelect.selectedIndex]?.text || '-';
        const genderValue = genderSelect.value;

        document.getElementById('confirmName').textContent = name;
        document.getElementById('confirmnumber_phone').textContent = number_phone;
        document.getElementById('confirmPaket').textContent = paket.includes('Silahkan') ? '-' : paket;
        document.getElementById('confirmGender').textContent = gender.includes('Silahkan') ? '-' : gender;

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

    let videoStream;
    const videoElement = document.getElementById('videoStream');
    const canvasElement = document.getElementById('photoCanvas');
    const capturedPhoto = document.getElementById('capturedPhoto');
    const savePhotoButton = document.getElementById('savePhotoButton');

    // Fungsi untuk membuka kamera
    async function openCamera() {
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({ video: true });
            videoElement.srcObject = videoStream;
            videoElement.style.display = 'block';
            savePhotoButton.style.display = 'block'; // Tampilkan tombol simpan gambar
            capturedPhoto.style.display = 'none';
        } catch (error) {
            alert('Kamera tidak dapat diakses: ' + error.message);
        }
    }

    function savePhoto() {
        const context = canvasElement.getContext('2d');
        canvasElement.width = videoElement.videoWidth;
        canvasElement.height = videoElement.videoHeight;

        // Menangkap gambar dari video ke canvas
        context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

        // Mendapatkan data URL gambar
        const imageData = canvasElement.toDataURL('image/png');

        // Menyimpan data URL ke hidden input
        document.getElementById('photoInput').value = imageData;

        // Menampilkan gambar hasil tangkapan
        capturedPhoto.src = imageData;
        capturedPhoto.style.display = 'block';

        // Menghentikan stream video
        videoStream.getTracks().forEach(track => track.stop());
        videoElement.style.display = 'none';
        savePhotoButton.style.display = 'none';
    }

    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const saveFileButton = document.getElementById('saveFileButton');
    const errorMessage = document.getElementById('errorMessage');
    const uploadedFileInput = document.getElementById('uploadedFileInput');
    
    function openFileUpload() {
        fileInput.click();
    }
    
    function previewFile() {
        const file = fileInput.files[0];
    
        if (file) {
            const validExtensions = ['image/jpeg', 'image/png', 'image/jpg'];
    
            if (!validExtensions.includes(file.type)) {
                errorMessage.style.display = 'block';
                filePreview.style.display = 'none';
                saveFileButton.style.display = 'none';
                return;
            }
    
            errorMessage.style.display = 'none';
            const reader = new FileReader();
    
            reader.onload = function (e) {
                filePreview.src = e.target.result;
                filePreview.style.display = 'block';
                saveFileButton.style.display = 'block';
                uploadedFileInput.value = e.target.result; // Simpan base64
            };
    
            reader.readAsDataURL(file);
        }
    }
    
    function saveFile() {
        if (uploadedFileInput.value) {
            alert('Gambar berhasil diunggah!');
            saveFileButton.style.display = 'none';
        } else {
            alert('Tidak ada file untuk disimpan.');
        }
    }

    $(document).ready(function () {
        // Ketika Country diubah
        $('#country').on('change', function () {
            const countryId = $(this).val();
            $('#state').prop('disabled', true).html('<option selected disabled>Loading...</option>');
            $('#city').prop('disabled', true).html('<option selected disabled>Silahkan Pilih Kota</option>');

            // AJAX untuk mendapatkan State
            $.get('{{ route('admin.get.states') }}', { country_id: countryId }, function (data) {
                $('#state').html('<option selected disabled>Silahkan Pilih Provinsi</option>');
                data.forEach(state => {
                    $('#state').append(`<option value="${state.id}">${state.name}</option>`);
                });
                $('#state').prop('disabled', false);
            });
        });

        // Ketika State diubah
        $('#state').on('change', function () {
            const stateId = $(this).val();
            $('#city').prop('disabled', true).html('<option selected disabled>Loading...</option>');

            // AJAX untuk mendapatkan City
            $.get('{{ route('admin.get.cities') }}', { state_id: stateId }, function (data) {
                $('#city').html('<option selected disabled>Silahkan Pilih Kota</option>');
                data.forEach(city => {
                    $('#city').append(`<option value="${city.id}">${city.name}</option>`);
                });
                $('#city').prop('disabled', false);
            });
        });

        $('#confirmSaveButton').on('click', function () {
            // Ambil data dari form
            const name = $('#name').val();
            const gender = $('#gender').val();
            const number_phone = $('#number_phone').val();
            const address = $('#address').val();
            const address2 = $('#address_2').val();
            const portal_code = $('#portal_code').val();
            const email = $('#email').val();
            const password = $('#password').val();

            // Tampilkan data dalam SweetAlert
            Swal.fire({
                title: 'Konfirmasi Simpan',
                html: `
                    <div style="text-align: left;">
                        <p><b>Nama:</b> ${name || 'Tidak diisi'}</p>
                        <p><b>Jenis Kelamin:</b> ${gender || 'Tidak diisi'}</p>
                        <p><b>No. Telp:</b> ${number_phone || 'Tidak diisi'}</p>
                        <p><b>Alamat Pertama:</b> ${address || 'Tidak diisi'}</p>
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
                console.log(result.value);
                console.log(result);
                
                if (result.isConfirmed || result.value == true) {
                    // Submit form jika dikonfirmasi
                    console.log('aaa');
                    
                    $('#memberForm').submit();
                }
            });
        });
    });
</script>
@include('admin.layout.footer')