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
            @php
                $start_date = request('start_date') ?? \Carbon\Carbon::today()->format('Y-m-d');
                $end_date = request('end_date') ?? \Carbon\Carbon::today()->format('Y-m-d');
            @endphp
			<div class="container-fluid">
                <form action="{{ route('admin.reportmemberDays') }}" method="GET">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Dari Tanggal:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $start_date }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Sampai Tanggal:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $end_date }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.reportmemberPDF', ['start_date' => $start_date, 'end_date' => $end_date]) }}"
                               class="btn btn-danger ms-2" target="_blank">Cetak Excel</a>
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
                                        <th>Tanggal Pendaftaran</th>
                                        <th>No. Loker</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $v_data)
                                    <tr>
                                        <td>{{ $v_data->name_member }}</td>
                                        <td>{{ \Carbon\Carbon::parse($v_data->created_at)->format('d-m-Y') }}</td>
                                        <td>{{ $v_data->key_fob }}</td>
                                        <td>{{ \Carbon\Carbon::parse($v_data->created_at)->format('H:i:s') }}</td>
                                        <td>
                                            @if($v_data->status === 'Checkout')
                                                {{ \Carbon\Carbon::parse($v_data->updated_training)->format('H:i:s') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
            
                            <div class="mt-3">
                                {{ $data->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
	</div>
@include('admin.layout.footer')