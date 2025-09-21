@extends('layouts.backoffice')
@section('title', 'Edit User')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                @if ($errors->any())
                <ul class="alert alert-warning">
                    @foreach ($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ url('users/'.$user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="">Nama Panggilan</label>
                                <input type="text" name="name" value="{{ $user->name }}" class="form-control" />
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="">Nama Lengkap</label>
                                <input type="text" name="full_name" value="{{ $user->full_name }}" class="form-control" />
                                @error('full_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="">Jenis Kelamin</label>
                                <select id="gender" class="form-control" name="gender">
                                    <option value="">Silahkan Pilih Gender</option>
                                    <option value="man" {{ $user->gender == 'man' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="woman" {{ $user->gender == 'woman' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Email</label>
                                <input type="text" name="email" readonly value="{{ $user->email }}" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Negara</label>
                                <select id="select-country" class="form-control" name="idcountry">
                                    <option value="">Silahkan Pilih Negara</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" {{ $user->idcountry == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Provinsi</label>
                                <select id="select-state" class="form-control" name="idstate">
                                    <option value="">Silahkan Pilih Provinsi</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kota</label>
                                <select id="select-city" class="form-control" name="idcities">
                                    <option value="">Silahkan Pilih Kota</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Alamat</label>
                                <input type="text" name="address" value="{{ $user->address }}" class="form-control" />
                                @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="">Alamat Ke-2</label>
                                <input type="text" name="address_2" value="{{ $user->address_2 }}" class="form-control" />
                                @error('address_2') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="">Kode Pos</label>
                                <input type="number" name="portal_code" value="{{ $user->portal_code }}" class="form-control" />
                                @error('portal_code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="">Password</label>
                                <input type="text" name="password" class="form-control" />
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="">Roles</label>
                                <select name="roles[]" class="form-control" multiple>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                    <option
                                        value="{{ $role }}"
                                        {{ in_array($role, $userRoles) ? 'selected':'' }}
                                    >
                                        {{ $role }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('roles') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    const userStateId = "{{ $user->idstate }}";
    const userCityId = "{{ $user->idcities }}";

    function loadStates(countryId, selectedStateId = null) {
        $('#select-state').html('<option value="">Loading...</option>');
        $('#select-city').html('<option value="">Silahkan Pilih Kota</option>');

        $.getJSON('/get-states/' + countryId, function (states) {
            let options = '<option value="">Silahkan Pilih Provinsi</option>';
            states.forEach(state => {
                const selected = state.id == selectedStateId ? 'selected' : '';
                options += `<option value="${state.id}" ${selected}>${state.name}</option>`;
            });
            $('#select-state').html(options);
        });
    }

    function loadCities(stateId, selectedCityId = null) {
        $('#select-city').html('<option value="">Loading...</option>');

        $.getJSON('/get-cities/' + stateId, function (cities) {
            let options = '<option value="">Silahkan Pilih Kota</option>';
            cities.forEach(city => {
                const selected = city.id == selectedCityId ? 'selected' : '';
                options += `<option value="${city.id}" ${selected}>${city.name}</option>`;
            });
            $('#select-city').html(options);
        });
    }

    // Trigger load saat halaman edit
    const selectedCountry = $('#select-country').val();
    if (selectedCountry) {
        loadStates(selectedCountry, userStateId);
        if (userStateId) {
            loadCities(userStateId, userCityId);
        }
    }

    // Negara berubah
    $('#select-country').on('change', function () {
        const countryId = $(this).val();
        if (countryId) {
            loadStates(countryId);
        } else {
            $('#select-state').html('<option value="">Silahkan Pilih Provinsi</option>');
            $('#select-city').html('<option value="">Silahkan Pilih Kota</option>');
        }
    });

    // Provinsi berubah
    $('#select-state').on('change', function () {
        const stateId = $(this).val();
        if (stateId) {
            loadCities(stateId);
        } else {
            $('#select-city').html('<option value="">Silahkan Pilih Kota</option>');
        }
    });
});
</script>

@endsection