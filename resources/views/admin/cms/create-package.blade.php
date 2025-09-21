@extends('layouts.backoffice')
@section('title', 'Buat Paket & Harga')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-body">
                        <form id="packetForm" action="{{ route('package.store') }}" method="POST">
                            @csrf
                            <input class="form-control" type="hidden" value="{{ Auth::user()->id }}" name="iduser" required><br>

                            <label for="packet_name">Nama Paket:</label>
                            <input class="form-control" type="text" name="packet_name" required><br>

                            <label for="days">Durasi (Hari):</label>
                            <input class="form-control" type="number" name="days" required><br>

                            <label for="price">Harga:</label>
                            <input class="form-control" type="number" name="price" required><br>

                            <label for="price">Harga Promosi:</label>
                            <input class="form-control" type="number" name="promote" required><br>

                            <label>Deskripsi:</label>
                            <div id="description-container">
                                <div class="description-item">
                                    <input type="text" class="form-control" name="description[]" required>
                                </div>
                            </div>
                            <br>
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
                            text: "Paket berhasil disimpan!",
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