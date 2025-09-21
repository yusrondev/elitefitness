@extends('layouts.backoffice')
@section('title', 'Paket & Harga')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card mt-3">
                    <div class="card-header">
                        <h4>
                            @can('create-role')
                                <a href="{{ url('/admin/cms/create-package') }}" class="btn btn-primary btn-sm float-end">Tambah</a>
                            @endcan
                        </h4>
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Paket</th>
                                    <th>Durasi (Hari)</th>
                                    <th>Harga</th>
                                    <th>Harga Promo</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages as $package)
                                    <tr>
                                        <td>{{ $package->id }}</td>
                                        <td>{{ $package->packet_name }}</td>
                                        <td>{{ $package->days }}</td>
                                        <td>{{ number_format($package->price, 0, ',', '.') }}</td>
                                        <td>{{ number_format($package->promote, 0, ',', '.') }}</td>
                                        <td>
                                            <ul>
                                                @if ($package->description)
                                                    @foreach ($package->description as $desc)
                                                        <li>{{ $desc }}</li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </td>
                                        <td>
                                            @if($package->is_active == 1)
                                                <label>Aktif</label>                                            
                                            @else
                                                <label>Tidak Aktif</label>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('/admin/cms/edit-package/' . $package->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>

                                            <form action="{{ url('/admin/cms/delete-package/' . $package->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus paket ini?');">
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
@endsection