@extends('layouts.backoffice')
@section('title', 'Users')
@section('content')
    <div class="container-fluid">
        <div class="row">
            @role('member')
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4 pb-0 justify-content-between">
                        <div>
                            <h4><b>Total Poin</b></h4>
                            <h2>{{ $total_poin }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4 pb-0 justify-content-between">
                        <div>
                            <h4><b>Poin Yang Sudah Digunakan</b></h4>
                            <h2>{{ $already_poin }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4 pb-0 justify-content-between">
                        <div>
                            <h4><b>Sisa Poin</b></h4>
                            <h2>{{ $remaining_poin }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            @endrole
            @role('trainer')
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4 pb-0 justify-content-between">
                        <div>
                            <h4><b>Total Poin Saya</b></h4>
                            <h2>{{ $poin_trainer }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4 pb-0 justify-content-between">
                        <div>
                            <h4><b>Income saya</b></h4>
                            <h2>Rp {{ number_format($income_trainer, 0, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            @endrole
            @role('admin')
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4 pb-0 justify-content-between">
                        <div>
                            <h4 class="fs-18 font-w600 mb-4 text-nowrap">Total Members</h4>
                            <div class="d-flex align-items-center">
                                <h2 class="fs-32 font-w700 mb-0">{{ $total_member }}</h2>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4  justify-content-between">
                        <div>
                            <div class="">
                                <h2 class="fs-32 font-w700">{{ $total_member2 }}</h2>
                                <span class="fs-18 font-w500 d-block">New Members</span>
                                <span class="d-block fs-16 font-w400">
                                    <small class="{{ $percentage_change2 >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $percentage_change2 >= 0 ? '+' : '' }}{{ $percentage_change2 }}%
                                    </small> than last month
                                </span>
                            </div>
                        </div>
                        <div id="NewCustomers1"></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4  justify-content-between">
                        <div>
                            <div class="">
                                <h2 class="fs-32 font-w700">{{ $total_today3 }}</h2>
                                <span class="fs-18 font-w500 d-block">Member Today</span>
                                <span class="d-block fs-16 font-w400">
                                    <small class="{{ $percentage_change_today >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $percentage_change_today >= 0 ? '+' : '' }}{{ $percentage_change_today }}%
                                    </small> than yesterday
                                </span>
                            </div>
                        </div>
                        <div id="NewCustomers"></div>
                    </div>
                </div>
            </div>
            @endrole
            @role('super-admin')
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4 pb-0 justify-content-between">
                        <div>
                            <h4 class="fs-18 font-w600 mb-4 text-nowrap">Total Members</h4>
                            <div class="d-flex align-items-center">
                                <h2 class="fs-32 font-w700 mb-0">{{ $total_member }}</h2>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4  justify-content-between">
                        <div>
                            <div class="">
                                <h2 class="fs-32 font-w700">{{ $total_member2 }}</h2>
                                <span class="fs-18 font-w500 d-block">New Members</span>
                                <span class="d-block fs-16 font-w400">
                                    <small class="{{ $percentage_change2 >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $percentage_change2 >= 0 ? '+' : '' }}{{ $percentage_change2 }}%
                                    </small> than last month
                                </span>
                            </div>
                        </div>
                        <div id="NewCustomers1"></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex px-4  justify-content-between">
                        <div>
                            <div class="">
                                <h2 class="fs-32 font-w700">{{ $total_today3 }}</h2>
                                <span class="fs-18 font-w500 d-block">Member Today</span>
                                <span class="d-block fs-16 font-w400">
                                    <small class="{{ $percentage_change_today >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $percentage_change_today >= 0 ? '+' : '' }}{{ $percentage_change_today }}%
                                    </small> than yesterday
                                </span>
                            </div>
                        </div>
                        <div id="NewCustomers"></div>
                    </div>
                </div>
            </div>
            @endrole
        </div>
        @role('super-admin')
        <div class="card">
            <div class="card-header border-0">
                <div>
                    <h4 class="fs-20 font-w700">Member Statistics</h4>
                </div>	
            </div>	
            <div class="card-body">
                <div id="emailchart"> </div>
                <div class="mb-3 mt-4">
                    <h4 class="fs-18 font-w600">Legend</h4>
                </div>
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <span class="fs-18 font-w500">	
                            <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="20" height="20" rx="6" fill="#886CC0"></rect>
                            </svg>
                            Active ({{ $percentage_active }}%)
                        </span>
                        <span class="fs-18 font-w600">{{ $total_active }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between  mb-4">
                        <span class="fs-18 font-w500">	
                            <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="20" height="20" rx="6" fill="#26E023"></rect>
                            </svg>
                            Non-Active ({{ $percentage_non_active }}%)
                        </span>
                        <span class="fs-18 font-w600">{{ $total_non_active }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-lg-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Information Registrasi</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="barChart_1"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Registration Chart</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="areaChart_1"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole
        @role('admin')
        <div class="card">
            <div class="card-header border-0">
                <div>
                    <h4 class="fs-20 font-w700">Member Statistics</h4>
                </div>	
            </div>	
            <div class="card-body">
                <div id="emailchart"> </div>
                <div class="mb-3 mt-4">
                    <h4 class="fs-18 font-w600">Legend</h4>
                </div>
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <span class="fs-18 font-w500">	
                            <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="20" height="20" rx="6" fill="#886CC0"></rect>
                            </svg>
                            Active ({{ $percentage_active }}%)
                        </span>
                        <span class="fs-18 font-w600">{{ $total_active }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between  mb-4">
                        <span class="fs-18 font-w500">	
                            <svg class="me-3" width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="20" height="20" rx="6" fill="#26E023"></rect>
                            </svg>
                            Non-Active ({{ $percentage_non_active }}%)
                        </span>
                        <span class="fs-18 font-w600">{{ $total_non_active }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-lg-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Information Registrasi</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="barChart_1"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Registration Chart</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="areaChart_1"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole
        @role('trainer')
        <div class="card">
            <div class="card-header border-0">
                <div>
                    <h4 class="fs-20 font-w700">Catatan Saya</h4>
                </div>	
            </div>	
            <div class="card-body">
                <div class="container-fluid">
                    <form action="{{ route('admin.dashboard') }}" method="GET">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Dari Tanggal:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Sampai Tanggal:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filter</button>&nbsp;&nbsp;
                                <button 
                                    type="button" 
                                    class="btn btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#createMemberModal">
                                    Tambah Data
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" style="margin-top:10px">
                                <table id="example" class="display" style="min-width: 845px">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notes as $v_notes)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($v_notes->created_at)->translatedFormat('d F Y H:i') }}</td>
                                            <td>{{ $v_notes->description }}</td>
                                            <td>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-warning edit-note-button"
                                                    data-id="{{ $v_notes->id }}"
                                                    data-description="{{ $v_notes->description }}"
                                                    data-action="{{ route('admin.members.update_notes', $v_notes->id) }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#createMemberModal">
                                                    Edit
                                                </button>
                                                
                                                <form 
                                                    action="{{ route('admin.members.delete_notes', $v_notes->id) }}" 
                                                    method="POST" 
                                                    style="display:inline;" 
                                                    onsubmit="return confirm('Yakin ingin menghapus catatan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
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
        </div>
        @endrole
    </div>
        
	<div class="modal fade" id="createMemberModal" tabindex="-1" aria-labelledby="createMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createMemberModalLabel">Tambah Data Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createMemberForm" method="POST">
                    @csrf
                    @method('POST') {{-- akan diganti JS saat edit --}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" name="description" id="description" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            emailchart({{ $percentage_active }}, {{ $percentage_non_active }});
        });

        var emailchart = function(activePercentage, nonActivePercentage) {
            var options = {
                series: [activePercentage, nonActivePercentage],
                labels: ['Active', 'Non-Active'], // Tambahkan label untuk series
                chart: {
                    type: 'donut',
                    height: 300
                },
                dataLabels: {
                    enabled: true, // Aktifkan label dalam chart
                    formatter: function(val, opts) {
                        return opts.w.globals.labels[opts.seriesIndex] + ": " + val.toFixed(1) + "%";
                    }
                },
                stroke: {
                    width: 0,
                },
                colors: ['#886CC0', '#26E023'], // Warna Active dan Non-Active
                legend: {
                    position: 'bottom',
                    show: true
                },
                responsive: [
                    {
                        breakpoint: 1800,
                        options: {
                            chart: {
                                height: 200
                            },
                        }
                    }
                ]
            };

            var chart = new ApexCharts(document.querySelector("#emailchart"), options);
            chart.render();
        };
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            barChart1({{ json_encode($monthlyData) }});
        });

        var barChart1 = function(monthlyData) {
            if (jQuery('#barChart_1').length > 0) {
                const barChart_1 = document.getElementById("barChart_1").getContext('2d');

                barChart_1.height = 100;

                new Chart(barChart_1, {
                    type: 'bar',
                    data: {
                        defaultFontFamily: 'Poppins',
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                        datasets: [
                            {
                                label: "Monthly Data",
                                data: monthlyData,
                                borderColor: 'rgba(136,108,192, 1)',
                                borderWidth: "0",
                                backgroundColor: 'rgba(136,108,192, 1)'
                            }
                        ]
                    },
                    options: {
                        legend: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }],
                            xAxes: [{
                                barPercentage: 0.5
                            }]
                        }
                    }
                });
            }
        }

        var areaChart1 = function(){
            // Menambahkan pengecekan jika elemen ada
            if (jQuery('#areaChart_1').length > 0) {
                const areaChart_1 = document.getElementById("areaChart_1").getContext('2d');

                areaChart_1.height = 100;

                // Menggunakan data bulan dari controller
                var monthlyData = @json($monthlyData); // Mengambil data dari controller

                new Chart(areaChart_1, {
                    type: 'line',
                    data: {
                        defaultFontFamily: 'Poppins',
                        // Menyesuaikan labels bulan (Januari - Desember)
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                        datasets: [
                            {
                                label: "Member Growth", // Judul dataset
                                data: monthlyData, // Data dari controller
                                borderColor: 'rgba(0, 0, 1128, .3)',
                                borderWidth: "1",
                                backgroundColor: 'rgba(255,167,215,1)', 
                                pointBackgroundColor: 'rgba(0, 0, 1128, .3)'
                            }
                        ]
                    },
                    options: {
                        legend: { display: false },
                        scales: {
                            yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                // max: 100, // hapus max ini supaya Chart.js auto sesuaikan
                                stepSize: 20,
                                padding: 10
                            }
                            }],
                            xAxes: [{
                            ticks: {
                                padding: 5
                            }
                            }]
                        }
                    }
                });
            }
        }

        // Memanggil fungsi untuk menampilkan chart setelah halaman dimuat
        areaChart1();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('createMemberForm');
            const descriptionInput = document.getElementById('description');
            const noteIdInput = document.getElementById('id'); // input hidden
            const methodInput = document.querySelector('input[name="_method"]'); // ✅ tambahkan ini
        
            document.querySelectorAll('.edit-note-button').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const description = this.getAttribute('data-description');
                    const actionUrl = this.getAttribute('data-action');
        
                    document.getElementById('createMemberModalLabel').innerText = 'Edit Data Catatan';
        
                    descriptionInput.value = description;
                    noteIdInput.value = id;
                    form.setAttribute('action', actionUrl);
                    methodInput.setAttribute('value', 'PUT'); // ← ini butuh methodInput yg sudah terdefinisi
                });
            });
        
            // Reset form saat modal ditutup
            const modalEl = document.getElementById('createMemberModal');
            modalEl.addEventListener('hidden.bs.modal', function () {
                form.reset();
                form.setAttribute('action', '{{ route('admin.members.add_notes') }}');
                methodInput.setAttribute('value', 'POST');
                document.getElementById('createMemberModalLabel').innerText = 'Tambah Data Member';
            });
        });
    </script>
@endsection