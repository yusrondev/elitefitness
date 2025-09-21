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
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Edit Profil</h4>

                                <!-- Menampilkan pesan error atau success -->
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Form untuk mengedit profil -->
                                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="name">Nama Panggilan</label>
                                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="full_name">Nama Lengkap</label>
                                                <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name', $user->full_name) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="idcountry">Negara</label>
                                                <select name="idcountry" id="idcountry" class="single-select">
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}" {{ $user->idcountry == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>    
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="idstate">Provinsi</label>
                                                <select name="idstate" id="idstate" class="single-select">
                                                    @foreach($states as $state)
                                                        <option value="{{ $state->id }}" {{ $user->idstate == $state->id ? 'selected' : '' }} disabled>{{ $state->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="idcities">Kota</label>
                                                <select name="idcities" id="idcities" class="single-select">
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}" {{ $user->idcities == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col"></div>
                                    </div>
                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="gender">Jenis Kelamin</label>
                                                <select name="gender" id="gender" class="form-control" required>
                                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="address">Alamat</label>
                                                <textarea name="address" id="address" class="form-control">{{ old('address', $user->address) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="address_2">Alamat 2</label>
                                                <textarea name="address_2" id="address_2" class="form-control">{{ old('address_2', $user->address_2) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="number_phone">Nomor Telepon</label>
                                                <input type="text" name="number_phone" id="number_phone" class="form-control" value="{{ old('number_phone', $user->number_phone) }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="portal_code">Kode Pos</label>
                                                <input type="text" name="portal_code" id="portal_code" class="form-control" value="{{ old('portal_code', $user->portal_code) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div style="margin-top:10px" class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="photo">Foto Profil</label>
                                                <input type="file" name="photo" id="photo" class="form-control">
                                                @if ($user->photo)
                                                    <img src="{{ asset('storage/app/public/' . $user->photo) }}" alt="Photo" width="100">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="upload">File Upload</label>
                                                <input type="file" name="upload" id="upload" class="form-control">
                                                @if ($user->upload)
                                                    <img src="{{ asset('storage/app/public/' . $user->upload) }}" alt="Photo" width="100">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div style="text-align:right;margin-top:10px;">
                                        <button type="submit" class="btn btn-primary">Update Profil</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Edit Password</h4>

                                <!-- Menampilkan pesan error atau success -->
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Form untuk mengubah password -->
                                <form action="{{ route('admin.profile.updatePassword') }}" method="POST">
                                    @csrf

                                    <div class="form-group">
                                        <label for="current_password">Password Lama</label>
                                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password">Password Baru</label>
                                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                                    </div>

                                    <div style="text-align:right;margin-top:10px;">
                                        <button type="submit" class="btn btn-primary">Update Password</button>
                                    </div>    
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Ketika Negara dipilih
        $('#idcountry').change(function() {
            var country_id = $(this).val(); // Ambil nilai negara yang dipilih
            if(country_id) {
                // Ambil data provinsi berdasarkan negara yang dipilih
                $.ajax({
                    url: '/get-states/' + country_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Kosongkan dropdown Provinsi
                        $('#idstate').empty();
                        $('#idstate').append('<option value="">Pilih Provinsi</option>');
                        // Loop untuk menambahkan options
                        $.each(data, function(key, value) {
                            $('#idstate').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });

                        // Kosongkan dropdown Kota
                        $('#idcities').empty();
                        $('#idcities').append('<option value="">Pilih Kota</option>');
                    }
                });
            } else {
                $('#idstate').empty();
                $('#idstate').append('<option value="">Pilih Provinsi</option>');
                $('#idcities').empty();
                $('#idcities').append('<option value="">Pilih Kota</option>');
            }
        });

        // Ketika Provinsi dipilih
        $('#idstate').change(function() {
            var state_id = $(this).val(); // Ambil nilai provinsi yang dipilih
            if(state_id) {
                // Ambil data kota berdasarkan provinsi yang dipilih
                $.ajax({
                    url: '/get-cities/' + state_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Kosongkan dropdown Kota
                        $('#idcities').empty();
                        $('#idcities').append('<option value="">Pilih Kota</option>');
                        // Loop untuk menambahkan options
                        $.each(data, function(key, value) {
                            $('#idcities').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                    }
                });
            } else {
                $('#idcities').empty();
                $('#idcities').append('<option value="">Pilih Kota</option>');
            }
        });
    });
</script>
@include('admin.layout.footer')