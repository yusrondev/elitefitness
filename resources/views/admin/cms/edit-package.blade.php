@extends('layouts.backoffice')
@section('title', 'Edit Paket & Harga')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-body">
                        <form id="packetForm" action="{{ route('package.update', $packet->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input class="form-control" type="hidden" value="{{ Auth::user()->id }}" name="iduser" required><br>

                            <label for="packet_name">Nama Paket:</label>
                            <input value="{{ $packet->packet_name }}" class="form-control" type="text" name="packet_name" required><br>

                            <label for="days">Durasi (Hari):</label>
                            <input value="{{ $packet->days }}" class="form-control" type="number" name="days" required><br>

                            <label for="price">Harga:</label>
                            <input value="{{ $packet->price }}" class="form-control" type="number" name="price" required><br>

                            <label for="price">Harga Promosi:</label>
                            <input value="{{ $packet->promote }}" class="form-control" type="number" name="promote" required><br>
                            
                            <label>Deskripsi:</label>
                            @if ($packet->description)
                                @foreach ($packet->description as $key => $item)
                                    @if ($key == 0)
                                        <div id="description-container">
                                            <div class="description-item">
                                                <input value="{{ $item }}" type="text" class="form-control" name="description[]" required>
                                            </div>
                                        </div>
                                        @else
                                        <div class="description-item">
                                            <div class="row mt-2">
                                                <div class="col-10">
                                                    <input value="{{ $item }}" type="text" name="description[]" class="form-control" required>
                                                </div>
                                                <div class="col">
                                                    <button type="button" class="remove-desc btn btn-sm btn-danger">Hapus</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <br>

                            <label for="price">Status:</label>
                            <select class="form-control" name="is_active">
                                @if($packet->is_active == 1)
                                    <option value="{{ $packet->is_active }}">Aktif</option>
                                    <option value="2">Tidak Aktif</option>
                                @else
                                    <option value="{{ $packet->is_active }}">Tidak Aktif</option>
                                    <option value="1">Aktif</option>
                                @endif
                            </select><br>

                            <button type="button" id="add-description" class="btn btn-sm btn-info">Tambah Deskripsi</button>
                            <button type="submit" class="btn btn-success btn-sm">Simpan Paket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Tambah input deskripsi
            $("#add-description").click(function() {
                $("#description-container").append(`
                <div class="description-item">
                    <div class="row mt-2">
                        <div class="col-10">
                            <input type="text" name="description[]" class="form-control" required>
                        </div>
                        <div class="col">
                            <button type="button" class="remove-desc btn btn-sm btn-danger">Hapus</button>
                        </div>
                    </div>
                </div>
            `);
            });

            // Hapus input deskripsi
            $(document).on("click", ".remove-desc", function() {
                $(this).closest(".description-item").remove();
            });

            // Submit form dengan jQuery AJAX
            $("#packetForm").submit(function(e) {
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Paket berhasil diperbaruhi!",
                            icon: "success"
                        });
                        window.location.href = "{{ url('/admin/cms/package') }}";
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan! " + xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush