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
			    <form action="{{ route('admin.reportnonactive') }}" method="GET">
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
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.reportnonactivePDF', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-danger ms-2" target="_blank">Cetak Excel</a>
                        </div>
                    </div>
                </form>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mulai Latihan</th>
                                        <th>Selesai Latihan</th>
                                        <th>Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $v_data)
                                    <tr>
                                        <td>{{ $v_data->name_member }}</td>
                                        <td>{{ \Carbon\Carbon::parse($v_data->start_training)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($v_data->end_training)->format('d-m-Y') }}</td>
                                        <td>{{ 'Rp ' . number_format($v_data->total_price, 0, ',', '.') }}</td>
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
@include('admin.layout.footer')