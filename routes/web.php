<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\TrainerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportMember;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\WablasController;
use App\Http\Controllers\Admin\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\State;
use App\Models\City;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/get-states/{country_id}', function($country_id) {
    return response()->json(State::where('country_id', $country_id)->get());
});

Route::get('/get-cities/{state_id}', function($state_id) {
    return response()->json(City::where('state_id', $state_id)->get());
});

Route::get('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
Route::post('/send-messages', [WablasController::class, 'sendMessages']);
Route::post('/check_in', [MemberController::class, 'check_in'])->name('checkin');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/save_notes', [DashboardController::class, 'add_notes'])->name('admin.members.add_notes');
    Route::put('/save_notes', [DashboardController::class, 'update_notes'])->name('admin.members.update_notes');
    Route::delete('/delete_notes/{id}', [DashboardController::class, 'delete_notes'])->name('admin.members.delete_notes');
});

Route::get('storage-file/{filename}', function ($filename) {
    $path = storage_path('app/public/' . $filename);

    if (file_exists($path)) {
        return response()->file($path);
    }

    abort(404);
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'editProfile'])->name('profile');
    
    Route::get('/data_member', [MemberController::class, 'index'])->name('data_member');
    Route::get('/add_member', [MemberController::class, 'add_member'])->name('add_member');
    Route::post('/save_member', [MemberController::class, 'store'])->name('members.store');
    Route::post('/save_smember', [MemberController::class, 'tambah_dataMember'])->name('members.add_smember');
    Route::get('/get-states', [MemberController::class, 'getStates'])->name('get.states');
    Route::get('/get-cities', [MemberController::class, 'getCities'])->name('get.cities');
    Route::put('/members/update/{id}', [MemberController::class, 'update'])->name('members.update');
    Route::post('/members/perpanjangan/{id}', [MemberController::class, 'perpanjanganmember'])->name('members.perpanjangan');
    Route::get('/admin/editmembers/{id}', [MemberController::class, 'getMember'])->name('members.editmember');
    Route::get('/admin/members', [MemberController::class, 'search'])->name('members.index');
    Route::get('/admin/cetak/{id}', [MemberController::class, 'cetakBarcode'])->name('members.cetakBarcode');
    Route::delete('/admin/members/{id}', [MemberController::class, 'destroy'])->name('members.destroy');
    Route::get('/schedule_member', [MemberController::class, 'scheduleMember'])->name('jadwal_member');
    Route::get('/checkin', [MemberController::class, 'checkin'])->name('checkin');
    Route::post('/checkout/{id}', [MemberController::class, 'checkout'])->name('checkout');
    Route::post('/store-schedule', [MemberController::class, 'storeSchedule'])->name('members.storeSchedule');
    Route::get('/schedule/detail/{iduser}', [MemberController::class, 'getScheduleDetail'])->name('members.detailschedule');
    Route::put('/schedule/detail/{id}', [MemberController::class, 'updateScheduleDetail'])->name('members.updateschedule');
    Route::delete('/schedule/delete-schedule/{id}', [MemberController::class, 'deleteSchedule'])->name('members.deleteschedule');
    Route::get('/top_upmember', [MemberController::class, 'top_upmember'])->name('topup_member');
    Route::post('/savetop_upmember', [MemberController::class, 'addtop_upmember'])->name('members.add_topupmember');
    Route::get('/topup/detail/{id}', [MemberController::class, 'topupDetail'])->name('topup.detail');
    Route::get('/topup/detailitem/{id}', [MemberController::class, 'getTopupItem'])->name('topup.item');
    Route::put('/topup/update/{id}', [MemberController::class, 'updateTopUp'])->name('topup.update');
    Route::put('/topup/delete/{id}', [MemberController::class, 'softDelete'])->name('admin.topup.delete');
    
    Route::get('/edit_member/{id}', [MemberController::class, 'edit_member'])->name('edit_member');
    Route::put('/update_member/{id}', [MemberController::class, 'update_member'])->name('update_member');


    Route::get('/data_trainer', [TrainerController::class, 'index'])->name('data_trainer');
    Route::get('/searchdata_trainer', [TrainerController::class, 'search'])->name('trainers.index');
    Route::get('/add_trainer', [TrainerController::class, 'add_Trainer'])->name('add_trainer');
    Route::post('/save_trainer', [TrainerController::class, 'store'])->name('trainers.store');
    Route::get('/admin/trainers/schedule-data', [TrainerController::class, 'getScheduleData'])->name('trainers.scheduleData');
    Route::get('/schedule_trainer', [TrainerController::class, 'scheduleTrainer'])->name('schedule');
    Route::get('/Informationschedule_trainer', [TrainerController::class, 'informationscheduleTrainer'])->name('informationschedule');
    Route::get('/trainers/schedule/{id}', [TrainerController::class, 'getSchedule'])->name('trainers.getSchedule');
    Route::post('/store-informationschedule', [TrainerController::class, 'informationSchedule'])->name('trainers.scheduleinformation');
    Route::put('/admin/trainers/scheduleinformation/update', [TrainerController::class, 'updateSchedule'])->name('trainers.scheduleinformation.update');
    Route::delete('/admin/trainers/scheduleinformation/delete', [TrainerController::class, 'deleteInformasiSchedule'])->name('trainers.scheduleinformation.delete');
    Route::post('/store-addpoin', [TrainerController::class, 'addpoin'])->name('trainers.addpoin');
    Route::put('/admin/trainers/poin/update', [TrainerController::class, 'updatepoin'])->name('trainers.poin.update');
    Route::delete('/admin/trainers/poin/delete', [TrainerController::class, 'deletePoinSchedule'])->name('trainers.poin.delete');

    Route::get('/report_day', [ReportMember::class, 'reportDays'])->name('report_day');
    Route::get('/report_memberactive', [ReportMember::class, 'reportmemberActive'])->name('report_memberactive');
    Route::get('/report_membernonactive', [ReportMember::class, 'reportmembernonActive'])->name('report_membernonactive');
    Route::get('/report_allmember', [ReportMember::class, 'reportmemberDays'])->name('report_allmember');
    Route::get('/export-excel', [ReportMember::class, 'exportExcel'])->name('reportmemberPDF');
    Route::get('/export-exceltrainerday', [ReportMember::class, 'exportTrainerExcel'])->name('reporttrainerPDF');
    Route::get('/export-excelactive', [ReportMember::class, 'exportActiveExcel'])->name('reportactivePDF');
    Route::get('/export-excelnonactive', [ReportMember::class, 'exportNonactiveExcel'])->name('reportnonactivePDF');
    Route::get('/report_allmoney', [ReportMember::class, 'reportAllMoney'])->name('report_allmoney');
    Route::post('/report_allmoney/store', [ReportMember::class, 'storeIncome'])->name('report_allmoney.store');
    Route::put('/report_allmoney/update/{id}', [ReportMember::class, 'updateIncome'])->name('report_allmoney.update');
    Route::delete('/report_allmoney/destroy/{id}', [ReportMember::class, 'destroyIncome'])->name('report_allmoney.destroy');
    Route::get('/export-excelmoney', [ReportMember::class, 'exportMoneyExcel'])->name('reportmoneyPDF');
    Route::get('/reportdatatrainer', [ReportMember::class, 'reportTrainer'])->name('reportdatatrainer');
    Route::get('/export-exceltrainer', [ReportMember::class, 'exportDataTrainerExcel'])->name('exportexceltrainer');
    Route::get('/report_allexpensemoney', [ReportMember::class, 'reportAllExpenseMoney'])->name('report_allexpensemoney');
    Route::post('/report_allexpensemoney/store', [ReportMember::class, 'store'])->name('expense.store');
    Route::put('/report_allexpensemoney/update/{id}', [ReportMember::class, 'update'])->name('expense.update');
    Route::delete('/report_allexpensemoney/destroy/{id}', [ReportMember::class, 'destroy'])->name('expense.destroy');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    
    Route::get('/InformationMember/{id}', [MemberController::class, 'show'])->name('information.member');
    Route::get('/admin/reportmember', [ReportMember::class, 'reportmemberDays'])->name('reportmemberDays');
    Route::get('/admin/reporttrainer', [ReportMember::class, 'reportDays'])->name('reporttrainer');
    Route::get('/admin/reportactive', [ReportMember::class, 'reportmemberActive'])->name('reportactive');
    Route::get('/admin/reportnonactive', [ReportMember::class, 'reportmembernonActive'])->name('reportnonactive');
    Route::get('/admin/checkin', [ReportMember::class, 'reportcheckIn'])->name('reportcheckin');
    
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});


// Api
Route::post('/api/member-data', [MemberController::class, 'memberData']);

Route::middleware(['auth', 'role:trainer'])->get('/trainer/dashboard', function () {
    return view('trainer.dashboard'); // Arahkan ke tampilan trainer.dashboard
})->name('trainer.dashboard');

Route::middleware(['auth'])->post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Rute untuk profil pengguna
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/roles.php';
require __DIR__.'/cms.php';