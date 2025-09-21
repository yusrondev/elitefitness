<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Trainer;
use App\Models\Country;
use App\Models\User;
use App\Models\Information_schedule;
use App\Models\ScheduleTrainer;
use App\Models\City;
use App\Models\State;
use App\Models\Packet_trainer;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    public function __construct(){
        if (!auth()->user()->can('view-trainer')) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
    
    public function index()
    {
        $memberActive = User::
        role('trainer')
        ->paginate(10);

        return view('admin.trainers.index', [
            'active' => $memberActive,
        ]);
    }

    public function search(Request $request)
    {
        $query = User::role('trainer');
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }
    
        $memberActive = $query->paginate(10);
    
        return view('admin.trainers.index', [
            'active' => $memberActive,
        ]);
    }

    public function add_trainer(Request $request)
    {
        $countries = Country::all();

        return view('admin.trainers.create', compact('countries')); // Form tambah trainer
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number_phone' => 'required|string|max:15',
            'gender' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
            'address' => 'required|string',
            'portal_code' => 'required|string',
        ]);

        $barcode = 'MBR-' . strtoupper(uniqid());
        $photoPath = null;
        $uploadPath = null;

        // Proses simpan foto jika ada
        if ($request->photo) {
            $imageData = $request->photo;
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $fileName = 'photo_' . uniqid() . '.png';
            $photoPath = 'uploads/photos/' . $fileName;
            
            \Storage::disk('public')->put($photoPath, base64_decode($imageData));
        }

        // Proses simpan berkas upload jika ada
        if ($request->upload) {
            $fileData = $request->upload;
        
            // Hilangkan prefix Base64
            $fileData = preg_replace('/^data:image\/\w+;base64,/', '', $fileData);
            $fileData = str_replace(' ', '+', $fileData);
        
            // Decode Base64 menjadi data gambar
            $imgData = base64_decode($fileData);
        
            // Pastikan decoding berhasil
            if (!$imgData) {
                return response()->json(['error' => 'Format gambar tidak valid'], 400);
            }
        
            // Dapatkan MIME Type secara aman
            $mimeType = getimagesizefromstring($imgData)['mime'];
            $extension = str_replace('image/', '', $mimeType); // Ambil ekstensi
        
            // Pastikan hanya format yang diizinkan
            if (!in_array($extension, ['jpeg', 'png', 'jpg'])) {
                return response()->json(['error' => 'Format gambar tidak didukung'], 400);
            }
        
            // Simpan gambar dengan nama unik
            $fileName = 'upload_' . uniqid() . '.' . $extension;
            $uploadPath = 'uploads/photos/' . $fileName;
        
            // Simpan ke storage Laravel (pastikan sudah menjalankan `php artisan storage:link`)
            Storage::disk('public')->put($uploadPath, $imgData);
        
        }

        $currentDate = Carbon::now()->startOfDay();

        $user = User::create([
            'name' => $request->name,
            'idcountry' => $request->idcountry,
            'idstate' => $request->idstate,
            'idcities' => $request->idcities,
            'number_phone' => $request->number_phone,
            'barcode' => $barcode,
            'address' => $request->address,
            'address_2' => $request->address_2,
            'portal_code' => $request->portal_code,
            'gender' => $request->gender,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'photo' => $photoPath,
            'upload' => $uploadPath,
        ]);

        // Pastikan role yang diberikan ada di dalam database
        $role = Role::firstOrCreate(['name' => 'trainer']);

        // Assign role ke user
        $user->assignRole($role);

        return redirect()->route('admin.data_trainer')->with('success', 'Trainer berhasil ditambahkan!');
    }

    public function scheduleTrainer()
    {
        $trainers = User::role('trainer')->get();
        $ptrainer = DB::table('packet_trainer')->get();
        $schedule = DB::table('schedule_trainer')
        ->join('top_upInformation', 'top_upInformation.id', '=', 'schedule_trainer.idtopup_informasi')
        ->join('users as umember', 'top_upInformation.iduser', '=', 'umember.id')
        ->join('users as tmember', 'top_upInformation.idtrainer', '=', 'tmember.id')
        ->leftJoin('member_gym', 'member_gym.idmember', '=', 'top_upInformation.iduser')
        ->select(
            'schedule_trainer.iduser',
            'umember.name as customer_name',
            'tmember.name as name',
            DB::raw('GROUP_CONCAT(DISTINCT tmember.name) as trainer_name'),  // Gabungkan nama pelatih untuk setiap user
            DB::raw('DATE_FORMAT(MIN(member_gym.start_training), "%d %M %Y") as training_date'), // Pilih tanggal pertama
            DB::raw('DATE_FORMAT(MIN(member_gym.end_training), "%d %M %Y") as training_end'), // Pilih tanggal pertama
        )
        ->groupBy(
            'schedule_trainer.iduser',
            'umember.name',
            'schedule_trainer.id',
            'tmember.name'
        )
        ->get();

        $user = Auth::user();
        $memberSuper = User::role('super-admin')->where('id', $user->id)->get();
        $memberAdmin = User::role('admin')->where('id', $user->id)->get();
        $isSuperUser = $user->hasRole(['super-admin', 'admin']);

        if ($isSuperUser) {
            $member_gym_schedule = DB::table('schedule_trainer')
            ->select('schedule_trainer.iduser as iduser', 'muser.name as customer_name', 'tuser.name as name', 
                     'schedule_trainer.date_trainer as date_trainer', 'schedule_trainer.start_time as start_time', 
                     'schedule_trainer.end_time as end_time')
            ->join('top_upInformation', 'top_upInformation.id', '=', 'schedule_trainer.idtopup_informasi')
            ->join('users as tuser', 'tuser.id', '=', 'top_upInformation.idtrainer')
            ->join('users as muser', 'muser.id', '=', 'schedule_trainer.iduser')
            ->get();
            
            // dd($member_gym_schedule);
                
            $schedule = DB::table('top_upInformation')
            ->select(
                'top_upInformation.id',
                'top_upInformation.iduser',
                'muser.name as customer_name',
                'tuser.name as name',
                'start_training as training_date',
                'end_training as training_end',
                'pertemuan as sessions_taken'
            )
            ->join('users as muser', 'muser.id', '=', 'top_upInformation.iduser')
            ->join('users as tuser', 'tuser.id', '=', 'top_upInformation.idtrainer')
            ->join('packet_trainer', 'packet_trainer.id', '=', 'top_upInformation.idtop_up')
            ->leftJoin('member_gym', 'member_gym.idmember', '=', 'top_upInformation.iduser')
            ->where('status', 1)
            ->orderBy('top_upInformation.created_at', 'asc')
            ->get();
        } else {
            $member_gym_schedule = DB::table('schedule_trainer')
            ->select('schedule_trainer.iduser as iduser', 'muser.name as customer_name', 'tuser.name as name', 
                     'schedule_trainer.date_trainer as date_trainer', 'schedule_trainer.start_time as start_time', 
                     'schedule_trainer.end_time as end_time')
            ->join('top_upInformation', 'top_upInformation.id', '=', 'schedule_trainer.idtopup_informasi')
            ->join('users as tuser', 'tuser.id', '=', 'top_upInformation.idtrainer')
            ->join('users as muser', 'muser.id', '=', 'schedule_trainer.iduser')
            ->where('top_upInformation.idtrainer', $user->id)
            ->get();
            
            // dd($member_gym_schedule);
                
            $schedule = DB::table('top_upInformation')
            ->select(
                'top_upInformation.id',
                'top_upInformation.iduser',
                'muser.name as customer_name',
                'tuser.name as name',
                'start_training as training_date',
                'end_training as training_end',
                'pertemuan as sessions_taken'
            )
            ->join('users as muser', 'muser.id', '=', 'top_upInformation.iduser')
            ->join('users as tuser', 'tuser.id', '=', 'top_upInformation.idtrainer')
            ->join('packet_trainer', 'packet_trainer.id', '=', 'top_upInformation.idtop_up')
            ->leftJoin('member_gym', 'member_gym.idmember', '=', 'top_upInformation.iduser')
            ->where('top_upInformation.idtrainer', $user->id)
            ->where('status', 1)
            ->orderBy('top_upInformation.created_at', 'asc')
            ->get();

        }

        // $member = DB::table('member_gym')
        // ->join('users', 'users.id', '=', 'member_gym.idmember')
        // ->leftJoin('users as trainers', 'trainers.id', '=', 'member_gym.idtrainer') // LEFT JOIN ke trainer
        // ->select(
        //     'member_gym.id as idmember',
        //     'users.name as name',
        //     'trainers.id as idtrainer',
        //     'trainers.name as trainer_name',
        //     DB::raw('DATE_FORMAT(member_gym.start_training, "%Y-%m-%d") as start_training'),
        //     DB::raw('DATE_FORMAT(member_gym.end_training, "%Y-%m-%d") as end_training')
        // )
        // ->where('idpaket', 2)
        // ->get();

        return view('admin.trainers.schedule', compact('ptrainer', 'trainers', 'schedule', 'member_gym_schedule'));
    }

    public function getScheduleData()
    {
        $scheduleData = DB::table('member_gym')
            ->join('trainers', 'trainers.id', '=', 'member_gym.idtrainer')
            ->select('member_gym.start_training as start', 'member_gym.end_training as end', 'trainers.name as title')
            ->get()
            ->map(function ($event) {
                $event->className = "bg-primary";
                return $event;
            });

        return response()->json($scheduleData);
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'idtrainer' => 'required',
            'iduser' => 'required',
            'date_trainer' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    // Memeriksa apakah sudah ada jadwal pada tanggal yang sama untuk idtrainer dan iduser
                    $existingSchedule = ScheduleTrainer::where('date_trainer', $value)
                        ->where('iduser', $request->iduser) // Memastikan member yang sama tidak bisa memilih tanggal yang sama
                        ->exists();

                    if ($existingSchedule) {
                        $fail('Tanggal ini sudah dipilih oleh member yang sama. Silakan pilih tanggal lain.');
                    }
                },
            ],
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // Mendapatkan member_gym untuk iduser yang dipilih
        $memberGym = DB::table('member_gym')->where('id', $request->iduser)->first();

        if (!$memberGym) {
            return back()->withErrors(['iduser' => 'Member tidak valid.'])->withInput();
        }

        // Tentukan batas maksimal jadwal berdasarkan idpacket_trainer
        $limit = 0;
        switch ($memberGym->idpacket_trainer) {
            case 1:
                $limit = 5;
                break;
            case 2:
                $limit = 20;
                break;
            case 3:
                $limit = 30;
                break;
            default:
                return back()->withErrors(['iduser' => 'Paket tidak valid.'])->withInput();
        }

        // Hitung jumlah jadwal yang sudah ada untuk member ini
        $scheduleCount = ScheduleTrainer::where('iduser', $request->iduser)->count();

        if ($scheduleCount >= $limit) {
            return back()->withErrors(['iduser' => 'Member ini sudah mencapai batas maksimal jadwal (' . $limit . ').'])->withInput();
        }

        // Jika validasi lolos, simpan data
        ScheduleTrainer::create([
            'idtrainer' => $request->idtrainer,
            'iduser' => $request->iduser,
            'date_trainer' => $request->date_trainer,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.schedule')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function getScheduleDetail($iduser)
    {
       $schedule = DB::table('schedule_trainer')
            ->join('member_gym', 'schedule_trainer.iduser', '=', 'member_gym.id')
            ->join('users as umember', 'member_gym.idmember', '=', 'umember.id')
            ->join('users as utrainer', 'schedule_trainer.idtrainer', '=', 'utrainer.id')
            ->where('schedule_trainer.iduser', $iduser)
            ->select(
                'schedule_trainer.id as id',
                'umember.name as customer_name',
                'utrainer.name as trainer_name',
                'schedule_trainer.idtrainer as idtrainer',
                DB::raw('DATE_FORMAT(schedule_trainer.date_trainer, "%d %M %Y") as training_date'),
                DB::raw('DATE_FORMAT(schedule_trainer.start_time, "%H:%i") as start_time'),
                DB::raw('DATE_FORMAT(schedule_trainer.end_time, "%H:%i") as end_time'),
                DB::raw('CASE 
                            WHEN member_gym.idpacket_trainer = 1 THEN 5
                            WHEN member_gym.idpacket_trainer = 2 THEN 20
                            WHEN member_gym.idpacket_trainer = 3 THEN 30
                            ELSE 0 
                        END as total_sessions'),
                DB::raw('(CASE 
                            WHEN member_gym.idpacket_trainer = 1 THEN 5
                            WHEN member_gym.idpacket_trainer = 2 THEN 20
                            WHEN member_gym.idpacket_trainer = 3 THEN 30
                            ELSE 0 
                        END) - COUNT(schedule_trainer.id) as remaining_sessions')
            )
            ->groupBy(
                'schedule_trainer.id',
                'umember.name',
                'utrainer.name',
                'schedule_trainer.idtrainer',
                'schedule_trainer.date_trainer',
                'schedule_trainer.start_time',
                'schedule_trainer.end_time',
                'member_gym.idpacket_trainer'
            )
            ->orderBy(
                'schedule_trainer.date_trainer', 'asc'
            )
            ->get(); // Mendapatkan semua data terkait iduser

        return response()->json($schedule);
    }

    public function updateScheduleDetail(Request $request, $id)
    {
        // Pastikan format tanggal yang diterima sesuai dengan yang diinginkan
        $trainingDate = Carbon::parse($request->training_date)->format('Y-m-d');

        // Validasi dan konversi start_time dan end_time untuk memastikan format yang tepat
        $startTime = Carbon::parse($request->start_time)->format('H:i:s'); // Menambahkan detik ":00"
        $endTime = Carbon::parse($request->end_time)->format('H:i:s'); // Menambahkan detik ":00"
        
        // Cari jadwal berdasarkan ID
        $schedule = ScheduleTrainer::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        // Update jadwal dengan data baru
        $schedule->update([
            'service_price' => $request->service_price,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date_trainer' => $trainingDate,
            'idtrainer' => $request->idtrainer,
        ]);

        return redirect()->route('admin.schedule')->with('success', 'Jadwal berhasil di update.');
    }

    public function deleteSchedule($id)
    {
        $schedule = ScheduleTrainer::findOrFail($id);

        $schedule->delete();

        return redirect()->route('admin.schedule')->with('success', 'Schedule deleted successfully');
    }
    
    public function informationscheduleTrainer(){
        $trainers = User::select('*','users.id AS users_id')
                ->role('trainer')
                ->get();
                
        $schedule = DB::table('information_schedule')->select('information_schedule.id', 'name', 'service_price', 'start_time', 'end_time', 'start_break', 'end_break')->leftjoin('users', 'users.id', '=', 'information_schedule.iduser')->get();
        
        $paket = DB::table('packet_trainer')
        ->select('id', 'packet_name')
        ->get();
           
        return view('admin.trainers.informationschedule', compact('trainers','schedule', 'paket'));
    }
    
    public function informationSchedule(Request $request){
        $request->validate([
            'idtrainer' => 'required',
            'service_price' => 'required',
            'start_time' => 'required',
            'start_break' => 'required',
            'end_break' => 'required',
            'end_time' => 'required',
        ]);
        
        $cleanPrice = str_replace('.', '', $request->service_price);
        
        $schedule = Information_schedule::create([
            'iduser' => $request->idtrainer,
            'service_price' => $request->service_price,
            'start_time' => $request->start_time,
            'start_break' => $request->start_break,
            'end_break' => $request->end_break,
            'end_time' => $request->end_time,
        ]);
        
        return redirect()->route('admin.informationschedule');
    }
    
    public function updateSchedule(Request $request) {
        $request->validate([
            'id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        
        $cleanPrice = str_replace('.', '', $request->service_price);
    
        DB::table('information_schedule')
            ->where('id', $request->id)
            ->update([
                'service_price' => $request->service_price,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'start_break' => $request->start_break,
                'end_break' => $request->end_break,
            ]);
    
        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui!');
    }
    
    public function deleteInformasiSchedule(Request $request) {
        $request->validate([
            'id' => 'required',
        ]);
    
        DB::table('information_schedule')->where('id', $request->id)->delete();
    
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
    }
    
    public function addpoin(Request $request){
        $request->validate([
            'packet_name' => 'required',
            'pertemuan' => 'required',
            'poin' => 'required',
            'price' => 'required',
        ]);
        
        $schedule = Packet_trainer::create([
            'iduser' => auth()->id(),
            'packet_name' => $request->packet_name,
            'pertemuan' => $request->pertemuan,
            'poin' => $request->poin,
            'price' => $request->price,
        ]);
        
        return redirect('/admin/cms/package_poin');
    }

    public function updatepoin(Request $request) {
        $request->validate([
            'packet_name' => 'required',
            'pertemuan' => 'required',
            'poin' => 'required',
            'price' => 'required',
        ]);
    
        DB::table('packet_trainer')
            ->where('id', $request->id)
            ->update([
                'pertemuan' => $request->pertemuan,
                'packet_name' => $request->packet_name,
                'poin' => $request->poin,
                'price' => $request->price,
            ]);
    
        return redirect('/admin/cms/package_poin');
    }
    
    public function deletePoinSchedule(Request $request) {
        $request->validate([
            'id' => 'required',
        ]);
    
        DB::table('packet_trainer')->where('id', $request->id)->delete();
    
        return redirect('/admin/cms/package_poin');
    }
}