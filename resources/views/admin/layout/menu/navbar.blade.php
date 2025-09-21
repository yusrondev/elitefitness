@php
use App\Models\Cms;
$cms = Cms::get();
foreach ($cms as $key => $value) {
    if ($value['section'] == "company") {
        $content['company'] = $value['content'];
    }
}
@endphp
<div class="nav-header">
            <a href="{{ url('dashboard') }}" class="brand-logo">
				<img src="{{ asset('uploads/company_logo.png') }}" alt="logo" style="width:25%;" />
				<div class="brand-title">
					<h2 class="" style="font-size:25px;"><strong>{{ @$content['company']['company_name'] }}</strong></h2>
					<span class="brand-sub-title">Kediri</span>
				</div>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        
        <div class="header border-bottom">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
							<div class="dashboard_bar">
                                
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
							
							<li class="nav-item dropdown  header-profile">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                @if (Auth::check()) 
                                    @php
                                        $user = Auth::user(); // Mendapatkan data user yang sedang login
                                        $fileExtension = pathinfo($user->upload ?? '', PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                                    @endphp

                                    @if ($user->photo)
                                        <img src="{{ asset('storage/app/public/' . $user->photo) }}" width="50" alt="User Photo" class="rounded-circle">
                                    @elseif ($user->upload && $isImage)
                                        <img src="{{ asset('storage/app/public/' . $user->upload) }}" width="56" alt="User Photo" class="rounded-circle">
                                    
                                    @else
                                        <img src="{{ asset('assets/images/default.png') }}" width="56" alt="Default Photo" class="rounded-circle">
                                    @endif
                                @else
                                    <img src="{{ asset('assets/images/default.png') }}" width="56" alt="Default Photo" class="rounded-circle">
                                @endif
                            </a>
								<div class="dropdown-menu dropdown-menu-end">
								    @can('view-profile')
    									<a href="{{ route('admin.profile') }}" class="dropdown-item ai-icon">
    										<svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
    										<span class="ms-2">Profile </span>
    									</a>
    								@endcan
									<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>

                                    <a href="javascript:void(0);" class="dropdown-item ai-icon" onclick="document.getElementById('logoutForm').submit();">
                                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                        <span class="ms-2">Logout</span>
                                    </a>
								</div>
							</li>
                        </ul>
                    </div>
				</nav>
			</div>
		</div>
        
        <div class="dlabnav">
            <div class="dlabnav-scroll">
				<ul class="metismenu" id="menu">
                    <li><a href="{{ route('admin.dashboard') }}" aria-expanded="false">
							<i class="fas fa-home"></i>
							<span class="nav-text">Dashboard</span>
						</a>	
                    </li>
                    @can('view-member')
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-user"></i>
                            <span class="nav-text">Informasi Member</span>
                        </a>
                        <ul aria-expanded="false">
                            @can('tambah-member')
                                <li><a href="{{ route('admin.add_member') }}">Tambah Member</a></li>
                            @endcan
                            @can('data-member')
                                <li><a href="{{ route('admin.data_member') }}">Data Member</a></li>
                            @endcan
                            @can('topup-member')
                                <li><a href="{{ route('admin.topup_member') }}">Top Up Member</a></li>
                            @endcan
                            @can('jadwal-member')
                                <li><a href="{{ route('admin.jadwal_member') }}">Jadwal Member</a></li>
                            @endcan
                            @can('checkin-member')   
                                <li><a href="{{ route('admin.checkin') }}">Check In / Check Out</a></li>   
                            @endcan 
                        </ul>
                    </li>
                    @endcan
                    @can('view-trainer')
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-user-check"></i>
                            <span class="nav-text">Informasi Trainer</span>
                        </a>
                        <ul aria-expanded="false">
                            @can('data-trainer')
                                <li><a href="{{ route('admin.data_trainer') }}">Data Trainer</a></li>
                            @endcan
                            @can('tambah-trainer')
                                <li><a href="{{ route('admin.add_trainer') }}">Tambah Trainer</a></li>
                            @endcan
                            @can('view-checkin-schedule')
                                <li><a href="{{ route('admin.informationschedule') }}">Informasi Jadwal</a></li>
                            @endcan
                            @can('jadwal-trainer')
                                <li><a href="{{ route('admin.schedule') }}">Jadwal Member</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                    @can('view-laporan')
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-info-circle"></i>
                            <span class="nav-text">Laporan</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('admin.report_allmoney') }}">Laporan Keuangan</a></li>
                            <li><a href="{{ route('admin.report_allexpensemoney') }}">Laporan Pengeluaran</a></li>
                            <li><a href="{{ route('admin.report_allmember') }}">Kunjungan Harian</a></li>
                            <!--<li><a href="{{ route('admin.report_day') }}">Laporan Admin</a></li>-->
                            <li><a href="{{ route('admin.report_memberactive') }}">Member Aktif</a></li>
                            <li><a href="{{ route('admin.report_membernonactive') }}">Member Tidak Aktif</a>
                            <!--<li><a href="{{ route('admin.reportcheckin') }}">Checkin Member</a></li>-->
                            <li><a href="{{ route('admin.reportdatatrainer') }}">Laporan Trainer</a></li>
                        </ul>
                    </li>
                    @endcan
                    @can('create-role')
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-shield-alt"></i>
                            <span class="nav-text">Role & Permission</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ url('roles') }}">Roles</a></li>
                            <li><a href="{{ url('permissions') }}">Permissions</a></li>
                            <li><a href="{{ url('users') }}">Manage Users</a></li>
                        </ul>
                    </li>
                    @endcan
                    @can('cms')
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fa fa-cog fa-spin"></i>
                            <span class="nav-text">CMS</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ url('admin/layout') }}">Konfigurasi</a></li>
                            <li><a href="{{ url('admin/cms/package') }}">Paket & Harga</a></li>
                            <li><a href="{{ url('admin/cms/package_poin') }}">Paket Trainer</a></li>
                            <!--<li><a href="{{ url('admin/cms/topup_poin') }}">Harga Top Up</a></li>-->
                        </ul>
                    </li>
                    @endcan
                </ul>
			</div>
        </div>