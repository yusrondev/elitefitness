<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\TopupInformation;
use App\Models\Notes;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        // if (auth()->user()->isAdmin()) {
        //     return view('admin.dashboard');
        // }

        // if (auth()->user()->isTrainer()) {
        //     return view('trainer.dashboard');
        // }
        // Total member dari awal tahun hingga sekarang
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();
        $total_member = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->count();

        // Total member bulan ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $total_this_month = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // Total member bulan lalu
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();
        $total_last_month = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        // Hitung persentase perubahan
        $percentage_change = $total_last_month > 0
            ? (($total_this_month - $total_last_month) / $total_last_month) * 100
            : 0;

         // Total member dari awal tahun hingga sekarang dengan idpaket = 2
        $startOfYear2 = Carbon::now()->startOfYear();
        $endOfYear2 = Carbon::now()->endOfYear();
        $total_member2 = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfYear2, $endOfYear2])
            ->count();

        $startOfMonth2 = Carbon::now()->startOfMonth();
        $endOfMonth2 = Carbon::now()->endOfMonth();
        $total_this_month2 = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfMonth2, $endOfMonth2])
            ->count();

        $startOfLastMonth2 = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth2 = Carbon::now()->subMonth()->endOfMonth();
        $total_last_month2 = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfLastMonth2, $endOfLastMonth2])
            ->count();

        $percentage_change2 = $total_last_month2 > 0
            ? round((($total_this_month2 - $total_last_month2) / $total_last_month2) * 100, 1)
            : ($total_this_month2 > 0 ? 100 : 0);
        

        // Total member hari ini dengan idpaket = 2
        $startOfToday3 = Carbon::now()->startOfDay();
        $endOfToday3 = Carbon::now()->endOfDay();
        $total_today3 = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfToday3, $endOfToday3])
            ->count();

        // Total member kemarin dengan idpaket = 2
        $startOfYesterday3 = Carbon::now()->subDay()->startOfDay();
        $endOfYesterday3 = Carbon::now()->subDay()->endOfDay();
        $total_yesterday3 = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfYesterday3, $endOfYesterday3])
            ->count();

        // Hitung persentase perubahan dibandingkan hari sebelumnya
        $percentage_change_today3 = $total_yesterday3 > 0
            ? (($total_today3 - $total_yesterday3) / $total_yesterday3) * 100
            : 0;

        $total_members = DB::table('member_gym')->count();

        // Total Active (end_training >= waktu sekarang)
        $total_active = DB::table('member_gym')
        ->where('end_training', '>=', $now)
        ->count();

        // Total Non-Active (end_training < waktu sekarang)
        $total_non_active = DB::table('member_gym')
            ->where('end_training', '<', $now)
            ->count();

        // Hitung persentase Active dan Non-Active
        $percentage_active = $total_members > 0 ? ($total_active / $total_members) * 100 : 0;
        $percentage_non_active = $total_members > 0 ? ($total_non_active / $total_members) * 100 : 0;

        $monthlyData = [];
        $startOfYear3 = Carbon::now()->startOfYear();
        $endOfYear3 = Carbon::now()->endOfYear();

        // Loop untuk setiap bulan dari Januari sampai Desember
        for ($month3 = 1; $month3 <= 12; $month3++) {
            $startOfMonth3 = Carbon::create(Carbon::now()->year, $month3, 1)->startOfMonth();
            $endOfMonth3 = Carbon::create(Carbon::now()->year, $month3, 1)->endOfMonth();

            $monthlyData[] = DB::table('member_gym')
                ->whereBetween('created_at', [$startOfMonth3, $endOfMonth3])
                ->count();
        }   

        if (config('app.debug')) {
            logger('Monthly Data:', $monthlyData);
        }
        
        $member = User::select('*')
                ->role('member')
                ->get();
                
        $admin = User::select('*')
                ->role('admin')
                ->get();
                
        $superadmin = User::select('*')
                ->role('super-admin')
                ->get();
                
        $total_poin = DB::table('top_upInformation')
                ->join('packet_trainer', 'top_upInformation.idtop_up', '=', 'packet_trainer.id')
                ->where('top_upInformation.iduser', auth()->id())
                ->where('status', 1)
                ->whereRaw('DATE_ADD(datetop_up, INTERVAL day DAY) >= CURDATE()')
                ->sum(DB::raw('CAST(packet_trainer.poin AS UNSIGNED)'));
        
        $already_poin = DB::table('schedule_trainer')
                ->where('schedule_trainer.iduser', auth()->id())
                ->count();
                
        $remaining_poin = $total_poin - $already_poin;
        
        $poin_trainer = DB::table('top_upInformation')
                ->where('top_upInformation.idtrainer', auth()->id())
                ->where('status', 1)
                ->whereMonth('top_upInformation.created_at', Carbon::now()->month)
                ->whereYear('top_upInformation.created_at', Carbon::now()->year)
                ->sum('total_poin');
                
        $income_trainer = DB::table('top_upInformation')
                ->join('packet_trainer', 'packet_trainer.id', '=', 'top_upInformation.idtop_up')
                ->where('top_upInformation.idtrainer', auth()->id())
                ->where('status', 1)
                ->whereMonth('top_upInformation.created_at', Carbon::now()->month)
                ->whereYear('top_upInformation.created_at', Carbon::now()->year)
                ->sum('price');
                
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();
        
        $notes = DB::table('notes')
            ->where('notes.iduser', auth()->id())
            ->whereBetween('notes.created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $date = null;
        $date = $date ? Carbon::parse($date, 'Asia/Jakarta') : Carbon::now('Asia/Jakarta');

        $startOfDay = Carbon::parse($date, 'Asia/Jakarta')->startOfDay();  // 2025-09-21 00:00:00 Asia/Jakarta
        $endOfDay = Carbon::parse($date, 'Asia/Jakarta')->endOfDay();      // 2025-09-21 23:59:59 Asia/Jakarta
        
        $startOfDayUtc = $startOfDay->copy()->setTimezone('UTC');
        $endOfDayUtc = $endOfDay->copy()->setTimezone('UTC');
        
        $total_today = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();
        
        $total_previous_day = DB::table('member_gym')
            ->whereBetween('created_at', [$startOfDayUtc, $endOfDayUtc])
            ->count();
        
        $percentage_change_today = $total_previous_day > 0
            ? round((($total_today - $total_previous_day) / $total_previous_day) * 100, 1)
            : ($total_today > 0 ? 100 : 0);   
                
        return view('admin.dashboard', [
            'total_member' => $total_member,
            'percentage_change' => round($percentage_change, 1),
            'total_member2' => $total_member2,
            'percentage_change2' => round($percentage_change2, 1),
            'total_today3' => $total_today3,
            'percentage_change_today3' => round($percentage_change_today3, 1),
            'total_active' => $total_active,
            'total_non_active' => $total_non_active,
            'percentage_active' => round($percentage_active, 1),
            'percentage_non_active' => round($percentage_non_active, 1),
            'monthlyData' => $monthlyData,
            'member' => $member,
            'admin' => $admin,
            'superadmin' => $superadmin,
            'total_poin' => $total_poin,
            'already_poin' => $already_poin,
            'remaining_poin' => $remaining_poin,
            'poin_trainer' => $poin_trainer,
            'income_trainer' => $income_trainer,
            'notes' => $notes,
            'percentage_change_today' => $percentage_change_today
        ]);
    }
    
    public function add_notes(Request $request){
        $request->validate([
            'description' => 'required'
        ]);
    
        $user = Notes::create([
            'iduser' => auth()->id(),
            'description' => $request->description,
        ]);
    
    
        return redirect()->route('admin.dashboard');
    }
    
    public function update_notes(Request $request)
    {
        $request->validate([
            'description' => 'required'
        ]);
    
        Notes::where('id', $request->id)
            ->where('iduser', auth()->id()) // agar hanya mengupdate milik user sendiri
            ->update([
                'description' => $request->description,
                'updated_at' => now()
            ]);
    
        return redirect()->route('admin.dashboard')->with('success', 'Catatan berhasil diperbarui.');
    }
    
    public function delete_notes($id)
    {
        $note = Notes::where('id', $id)
                     ->where('iduser', auth()->id())
                     ->first();
    
        if ($note) {
            $note->delete();
            return redirect()->route('admin.dashboard')->with('success', 'Catatan berhasil dihapus.');
        }
    
        return redirect()->route('admin.dashboard')->with('error', 'Catatan tidak ditemukan atau tidak diizinkan.');
    }

}
