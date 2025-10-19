<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MemberInformation;
use App\Models\TopupInformation;
use App\Models\TopUp;
use App\Models\Member_gym;
use App\Models\CheckinMember;
use App\Models\Packet_trainer;
use App\Models\Country;
use App\Models\Cms;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use App\Models\ScheduleTrainer;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function __construct(){
        if (!auth()->user()->can('view-member')) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperUser = $user->hasRole('super-admin') || $user->hasRole('admin');
        $request = request(); // Ambil request untuk filter
        $status = $request->query('status');
        $offSoon = $request->query('off_soon');
        $search = $request->query('search');

        if ($isSuperUser) {
            $memberActive = DB::table('member_gym')
            ->select(
                DB::raw('MAX(member_gym.id) as id'),
                DB::raw('MAX(member_gym.iduser) as iduser'),
                DB::raw('MAX(users.name) as name_member'),
                DB::raw('MAX(users.barcode) as barcode'),
                DB::raw('MAX(users.photo) as photo'),
                DB::raw('MAX(users.upload) as upload'),
                DB::raw('MAX(users.barcode_path) as barcode_path'),
                DB::raw('MAX(users.number_phone) as number_phone'),
                DB::raw('MAX(packets.packet_name) as packet_name'),
                DB::raw('MAX(packets.days) as days'),
                DB::raw('MAX(member_gym.end_training) as end_training'),
                DB::raw('MAX(member_gym.start_training) as start_training'),
                // DB::raw('MAX(packet_trainer.pertemuan) as pertemuan'),
                'member_gym.idmember as idmember'
            )
            ->leftJoin('users', 'users.id', '=', 'member_gym.idmember')
            // ->leftJoin('packet_trainer', 'packet_trainer.id', '=', 'member_gym.idpacket_trainer')
            ->leftJoin('packets', 'packets.id', '=', 'member_gym.idpaket')
            ->when(!$isSuperUser, function ($query) use ($user) {
                $query->where('member_gym.idmember', $user->id);
            })
            ->when($status == 'aktif', function ($query) {
                $query->whereDate('member_gym.end_training', '>=', Carbon::today());
            })
            ->when($status == 'nonaktif', function ($query) {
                $query->whereDate('member_gym.end_training', '<', Carbon::today());
            })
            ->when($offSoon, function ($query) {
                $query->whereBetween('member_gym.end_training', [Carbon::today(), Carbon::today()->addDays(3)]);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', '%' . $search . '%')
                      ->orWhere('users.barcode', 'like', '%' . $search . '%');
                });
            })
            ->groupBy('member_gym.idmember')
            ->orderBy('end_training', 'desc')
            ->paginate(10);
        }else{
            $memberActive = DB::table('member_gym')
            ->select(
                DB::raw('MAX(member_gym.id) as id'),
                DB::raw('MAX(member_gym.iduser) as iduser'),
                DB::raw('MAX(users.name) as name_member'),
                DB::raw('MAX(users.barcode) as barcode'),
                DB::raw('MAX(users.photo) as photo'),
                DB::raw('MAX(users.upload) as upload'),
                DB::raw('MAX(users.number_phone) as number_phone'),
                DB::raw('MAX(packets.packet_name) as packet_name'),
                DB::raw('MAX(packets.days) as days'),
                DB::raw('MAX(member_gym.end_training) as end_training'),
                DB::raw('MAX(member_gym.start_training) as start_training'),
                // DB::raw('MAX(packet_trainer.pertemuan) as pertemuan'),
                'member_gym.idmember as idmember'
            )
            ->leftJoin('users', 'users.id', '=', 'member_gym.idmember')
            // ->leftJoin('packet_trainer', 'packet_trainer.id', '=', 'member_gym.idpacket_trainer')
            ->leftJoin('packets', 'packets.id', '=', 'member_gym.idpaket')
            ->when(!$isSuperUser, function ($query) use ($user) {
                $query->where('member_gym.idmember', $user->id);
            })
            ->when($status == 'aktif', function ($query) {
                $query->whereDate('member_gym.end_training', '>=', Carbon::today());
            })
            ->when($status == 'nonaktif', function ($query) {
                $query->whereDate('member_gym.end_training', '<', Carbon::today());
            })
            ->when($offSoon, function ($query) {
                $query->whereBetween('member_gym.end_training', [Carbon::today(), Carbon::today()->addDays(3)]);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', '%' . $search . '%')
                      ->orWhere('users.barcode', 'like', '%' . $search . '%');
                });
            })
            ->groupBy('member_gym.idmember')
            ->orderBy('member_gym.created_at', 'desc')
            ->paginate(10);
        }

        // foreach ($memberActive as $member) {
        //     // Generate QR Code
        //     $qrCode = new QrCode(route('admin.information.member', ['id' => $member->id])); // URL dinamis
        //     $writer = new PngWriter();
        //     $qrCodeImage = $writer->write($qrCode);
        //     $member->qrcode = base64_encode($qrCodeImage->getString());

        //     $endDate = $member->end_training;
        //     $currentDate = Carbon::now();

        //     $remainingDays = $currentDate->diffInDays($endDate, false);
        //     $member->time_remaining = $currentDate->diffForHumans($endDate, [
        //         'parts' => 3, // Menampilkan hingga 3 bagian waktu (misalnya "1 bulan, 2 minggu, 3 hari")
        //         'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
        //     ]);

        //     $member->show_button = $remainingDays <= 3 && $remainingDays >= 0;
        //     if (isset($member->number_phone)) {
        //         $formattedPhoneNumber = preg_replace('/^0/', '62', $member->number_phone); // Ubah awalan 0 menjadi 62
        //     } else {
        //         $formattedPhoneNumber = null; // Jika nomor tidak ada
        //     }
        
        //     if ($member->show_button && $formattedPhoneNumber) {
        //         // $message = urlencode("Halo, ini dari Elite Fitness Kediri. Kami ingin mengingatkan bahwa keanggotaan Anda akan segera berakhir pada tanggal " . $endDate . ". Mohon konfirmasi untuk perpanjangan member Anda. Terima kasih!");
        //         $cms = Cms::where('section', '=>', 'whatsapp')->first();

        //         $message = @$cms->whatsapp_message;
        //         $member->wa_link = "https://wa.me/{$formattedPhoneNumber}?text={$message}";
        //     } else {
        //         $member->wa_link = null;
        //     }
        // }
        
        foreach ($memberActive as $member) {
            // Generate QR Code
            $qrCode = new QrCode(route('admin.information.member', ['id' => $member->id]));
            $writer = new PngWriter();
            $qrCodeImage = $writer->write($qrCode);
            $member->qrcode = base64_encode($qrCodeImage->getString());
        
            // Hitung waktu tersisa
            $endDate = $member->end_training;
            $currentDate = Carbon::now();
            $remainingDays = $currentDate->diffInDays($endDate, false);
            $member->time_remaining = $currentDate->diffForHumans($endDate, [
                'parts' => 3,
                'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
            ]);
        
            // Format nomor telepon
            $formattedPhoneNumber = isset($member->number_phone)
                ? preg_replace('/^0/', '62', $member->number_phone)
                : null;
        
            // Ambil pesan WhatsApp dari CMS
            if ($formattedPhoneNumber) {
                $cms = Cms::where('section', 'whatsapp')->first();
        
                if ($cms) {
                    $content = $cms->content;  // langsung pakai tanpa json_decode
                    $message = $content['whatsapp_message'] ?? '';
                } else {
                    $message = '';
                }
        
                if (!empty($message)) {
                    $encodedMessage = urlencode($message);
                    $member->wa_link = "https://wa.me/{$formattedPhoneNumber}?text={$encodedMessage}";
                } else {
                    $member->wa_link = null;
                }
            } else {
                $member->wa_link = null;
            }
            
        }

        $paket = DB::table('packets')
        ->select('id',
                 DB::raw('CAST(price AS UNSIGNED) as price'), 
                 DB::raw('CAST(promote AS UNSIGNED) as promote'),
                 'days', 
                 'packet_name')
        ->get();
        // $paket_trainer = DB::table('packet_trainer')->get();
        $trainer = DB::table('information_schedule')
                ->select('information_schedule.id as id', 'users.name')
                ->join('users', 'users.id', '=', 'information_schedule.iduser')
                ->get();
                
        // $trainer = User::select('*','users.id AS users_id')
        //         ->role('trainer')
        //         ->get();

        $member = User::select('*','users.id AS users_id')
                ->leftjoin('member_gym', 'users.id', '=', 'member_gym.idmember')
                ->role('member')
                ->get();

        $checkin = DB::table('checkin_member')->where('idmember', $user->id)->orderBy('id', 'desc')->limit(1);

        return view('admin.member.index', [
            'paket' => $paket,
            // 'paket_trainer' => $paket_trainer,
            'trainer' => $trainer,
            'member' => $member,
            'active' => $memberActive,
            'checkin' => $checkin
        ]);
    }
    
    public function edit_member($id)
    {
        $member = User::findOrFail($id);
        $countries = Country::all(); // jika butuh data tambahan lainnya
    
        return view('admin.member.add', compact('member', 'countries'));
    }
    
    public function update_member(Request $request, $id)
    {
        $member = User::findOrFail($id);
    
        $request->validate([
            'name' => 'required|string|max:255',
            'number_phone' => 'required|string|max:15',
            'gender' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'address' => 'required|string',
            'portal_code' => 'required|string',
        ]);
    
        // Siapkan path default (tetap gambar lama jika tidak ada input baru)
        $photoPath = $member->photo;
        $uploadPath = $member->upload;
    
        // Proses update foto (jika ada base64 baru)
        if ($request->photo) {
            $imageData = str_replace('data:image/png;base64,', '', $request->photo);
            $imageData = str_replace(' ', '+', $imageData);
            $fileName = 'photo_' . uniqid() . '.png';
            $photoPath = 'uploads/photos/' . $fileName;
            Storage::disk('public')->put($photoPath, base64_decode($imageData));
        }
    
        // Proses update file upload (jika ada base64 baru)
        if ($request->upload) {
            $fileData = preg_replace('/^data:image\/\w+;base64,/', '', $request->upload);
            $fileData = str_replace(' ', '+', $fileData);
            $imgData = base64_decode($fileData);
    
            if (!$imgData) {
                return redirect()->back()->withErrors(['upload' => 'Format gambar tidak valid.']);
            }
    
            $mimeType = getimagesizefromstring($imgData)['mime'];
            $extension = str_replace('image/', '', $mimeType);
            if (!in_array($extension, ['jpeg', 'png', 'jpg'])) {
                return redirect()->back()->withErrors(['upload' => 'Format gambar tidak didukung.']);
            }
    
            $fileName = 'upload_' . uniqid() . '.' . $extension;
            $uploadPath = 'uploads/photos/' . $fileName;
            Storage::disk('public')->put($uploadPath, $imgData);
        }
    
        // Update data member
        $member->update([
            'name' => $request->name,
            'idcountry' => $request->idcountry,
            'idstate' => $request->idstate,
            'idcities' => $request->idcities,
            'number_phone' => $request->number_phone,
            'number_phoned' => $request->number_phoned,
            'address' => $request->address,
            'address_2' => $request->address_2,
            'portal_code' => $request->portal_code,
            'gender' => $request->gender,
            'email' => $request->email,
            'photo' => $photoPath,
            'upload' => $uploadPath,
        ]);
    
        return redirect()->route('admin.data_member')->with('success', 'Member berhasil diperbarui.');
    }

    public function memberData(Request $request)
    {
        $member = Member_gym::with('trainer')->where('id', $request->id)->first();
        return response()->json($member);
    }

    public function add_member(Request $request)
    {
        $countries = Country::all();
        return view('admin.member.add', compact('countries'));
    }

    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country_id)->get();
        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)->get();
        return response()->json($cities);
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
    
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return redirect()->back()->withErrors(['email' => 'Email sudah terdaftar. Silakan gunakan email lain.']);
        }
    
        // Generate barcode string
        $barcodeContent = 'MBR-' . strtoupper(uniqid());
    
        // Generate barcode image
        $generator = new BarcodeGeneratorPNG();
        $barcodeImage = $generator->getBarcode($barcodeContent, $generator::TYPE_CODE_128);
    
        // Simpan barcode image ke storage
        $barcodeFileName = 'barcode_' . uniqid() . '.png';
        $barcodePath = 'uploads/barcodes/' . $barcodeFileName;
        Storage::disk('public')->put($barcodePath, $barcodeImage);
        
        
        $photoPath = null;
        $uploadPath = null;
    
        // Simpan foto (base64)
        if ($request->photo) {
            $imageData = $request->photo;
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $fileName = 'photo_' . uniqid() . '.png';
            $photoPath = 'uploads/photos/' . $fileName;
            Storage::disk('public')->put($photoPath, base64_decode($imageData));
        }
    
        // Simpan upload file (base64)
        if ($request->upload) {
            $fileData = $request->upload;
            $fileData = preg_replace('/^data:image\/\w+;base64,/', '', $fileData);
            $fileData = str_replace(' ', '+', $fileData);
            $imgData = base64_decode($fileData);
    
            if (!$imgData) {
                return response()->json(['error' => 'Format gambar tidak valid'], 400);
            }
    
            $mimeType = getimagesizefromstring($imgData)['mime'];
            $extension = str_replace('image/', '', $mimeType);
            if (!in_array($extension, ['jpeg', 'png', 'jpg'])) {
                return response()->json(['error' => 'Format gambar tidak didukung'], 400);
            }
    
            $fileName = 'upload_' . uniqid() . '.' . $extension;
            $uploadPath = 'uploads/photos/' . $fileName;
            Storage::disk('public')->put($uploadPath, $imgData);
        }
    
        $user = User::create([
            'name' => $request->name,
            'idcountry' => $request->idcountry,
            'idstate' => $request->idstate,
            'idcities' => $request->idcities,
            'number_phone' => $request->number_phone,
            'number_phoned' => $request->number_phoned,
            'barcode' => $barcodeContent,
            'barcode_path' => $barcodePath,
            'address' => $request->address,
            'address_2' => $request->address_2,
            'portal_code' => $request->portal_code,
            'gender' => $request->gender,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'photo' => $photoPath,
            'upload' => $uploadPath,
        ]);
    
        $role = Role::firstOrCreate(['name' => 'member']);
        $user->assignRole($role);
    
        return redirect()->route('admin.data_member');
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction(); // Biar aman
    
        try {
            $member = Member_gym::findOrFail($id);
    
            // Step 5: Update data member_gym
            $member->update([
                'idmember' => $request->idmember,
                'idpaket' => $request->idpaket,
                'total_price' => $request->total_price,
                'start_training' => $request->start_training,
                'end_training' => date('Y-m-d 23:59:59', strtotime($request->end_training)),
                'description' => $request->description,
            ]);
    
            DB::commit(); // Semua berhasil
    
            return redirect()->route('admin.data_member')->with('success', 'Data member berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollback(); // Kalau error, balikin semua
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function perpanjangan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'notelp' => 'required|string|max:15',
            'idpaket' => 'required',
            'idtrainer' => 'nullable',
            'gender' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
            'pertemuan' => 'required|string',
            'status' => 'required|string',
        ]);

        $barcode = 'MBR-' . strtoupper(uniqid());

        $member = MemberInformation::create([
            'iduser' => auth()->id(),
            'idpaket' => $request->idpaket,
            'idtrainer' => $request->idtrainer ?? null,
            'name' => $request->name,
            'notelp' => $request->notelp,
            'barcode' => $barcode,
            'status' => $request->status,
            'gender' => $request->gender,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'pertemuan' => $request->pertemuan,
        ]);

        $barcodePath = public_path('assets/images/barcode');
        if (!file_exists($barcodePath)) {
            mkdir($barcodePath, 0777, true);
        }

        $qrCode = new QrCode($barcode);
        $writer = new PngWriter();
        $qrCodeImage = $writer->write($qrCode);

        $qrCodeImage->saveToFile($barcodePath . '/' . $barcode . '_qrcode.png');

        return redirect()->route('admin.members.data_member')->with('success', 'Member berhasil ditambahkan!');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        
        $user = Auth::user();
        $isSuperUser = ($user->id == 1 || $user->id == 2 || $user->email == 'super@gmail.com');
    
        if ($isSuperUser) {
            $query = DB::table('member_gym')
                ->select(
                    DB::raw('MAX(member_gym.id) as id'),
                    DB::raw('MAX(member_gym.iduser) as iduser'),
                    DB::raw('MAX(users.name) as name_member'),
                    DB::raw('MAX(users.barcode) as barcode'),
                    DB::raw('MAX(users.barcode_path) as barcode_path'),
                    DB::raw('MAX(users.photo) as photo'),
                    DB::raw('MAX(users.upload) as upload'),
                    DB::raw('MAX(users.number_phone) as number_phone'),
                    DB::raw('MAX(packets.packet_name) as packet_name'),
                    DB::raw('MAX(packets.days) as days'),
                    DB::raw('MAX(member_gym.end_training) as end_training'),
                    DB::raw('MAX(member_gym.start_training) as start_training'),
                    DB::raw('MAX(packet_trainer.pertemuan) as pertemuan'),
                    'member_gym.idmember as idmember'
                )
                ->leftJoin('users', 'users.id', '=', 'member_gym.idmember')
                ->leftJoin('packet_trainer', 'packet_trainer.id', '=', 'member_gym.idpacket_trainer')
                ->leftJoin('packets', 'packets.id', '=', 'member_gym.idpaket')
                ->whereIn('member_gym.id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('member_gym')
                        ->groupBy('idmember');
                })
                ->groupBy('member_gym.idmember') // Mengelompokkan hanya berdasarkan idmember
                ->orderBy('end_training', 'desc'); // Urutkan berdasarkan end_training yang terbaru
    
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                          ->orWhere('users.barcode', 'like', '%' . $search . '%'); // Menambahkan pencarian barcode
                });
            }
    
            $memberActive = $query->paginate(10); // Mendapatkan data yang sudah difilter dan dipaginasi
        } else {
            $query = DB::table('member_gym')
                ->select(
                    DB::raw('MAX(member_gym.id) as id'),
                    DB::raw('MAX(member_gym.iduser) as iduser'),
                    DB::raw('MAX(users.name) as name_member'),
                    DB::raw('MAX(users.barcode) as barcode'),
                    DB::raw('MAX(users.photo) as photo'),
                    DB::raw('MAX(users.upload) as upload'),
                    DB::raw('MAX(users.number_phone) as number_phone'),
                    DB::raw('MAX(packets.packet_name) as packet_name'),
                    DB::raw('MAX(packets.days) as days'),
                    DB::raw('MAX(member_gym.end_training) as end_training'),
                    DB::raw('MAX(member_gym.start_training) as start_training'),
                    DB::raw('MAX(packet_trainer.pertemuan) as pertemuan'),
                    'member_gym.idmember as idmember'
                )
                ->leftJoin('users', 'users.id', '=', 'member_gym.idmember')
                ->leftJoin('packet_trainer', 'packet_trainer.id', '=', 'member_gym.idpacket_trainer')
                ->leftJoin('packets', 'packets.id', '=', 'member_gym.idpaket')
                ->whereIn('member_gym.id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('member_gym')
                        ->groupBy('idmember');
                })
                ->where('member_gym.idmember', $user->id)
                ->groupBy('member_gym.idmember') // Mengelompokkan hanya berdasarkan idmember
                ->orderBy('end_training', 'desc'); // Urutkan berdasarkan end_training yang terbaru
    
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                          ->orWhere('users.barcode', 'like', '%' . $search . '%'); // Menambahkan pencarian barcode
                });
            }
    
            $memberActive = $query->paginate(10); // Mendapatkan data yang sudah difilter dan dipaginasi
        }
    
        // Lakukan pemrosesan data tambahan yang sudah ada (misalnya QR code, waktu sisa, dll.)
    
        foreach ($memberActive as $member) {
            // Pemrosesan untuk QR Code dan sisa waktu
            $qrCode = new QrCode(route('admin.information.member', ['id' => $member->id])); // URL dinamis
            $writer = new PngWriter();
            $qrCodeImage = $writer->write($qrCode);
            $member->qrcode = base64_encode($qrCodeImage->getString());
    
            $endDate = $member->end_training;
            $currentDate = Carbon::now();
    
            $remainingDays = $currentDate->diffInDays($endDate, false);
            $member->time_remaining = $currentDate->diffForHumans($endDate, [
                'parts' => 3, // Menampilkan hingga 3 bagian waktu (misalnya "1 bulan, 2 minggu, 3 hari")
                'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
            ]);
    
            $member->show_button = $remainingDays <= 3 && $remainingDays >= 0;
            if (isset($member->number_phone)) {
                $formattedPhoneNumber = preg_replace('/^0/', '62', $member->number_phone); // Ubah awalan 0 menjadi 62
            } else {
                $formattedPhoneNumber = null; // Jika nomor tidak ada
            }
    
            if ($member->show_button && $formattedPhoneNumber) {
                $cms = Cms::where('section', '=>', 'whatsapp')->first();
                $message = @$cms->whatsapp_message;
                $member->wa_link = "https://wa.me/{$formattedPhoneNumber}?text={$message}";
            } else {
                $member->wa_link = null;
            }
        }
    
        $paket = DB::table('packets')->get();
        $paket_trainer = DB::table('packet_trainer')->get();
        $trainer = DB::table('information_schedule')
                ->select('information_schedule.id as id', 'users.name')
                ->join('users', 'users.id', '=', 'information_schedule.iduser')
                ->get();
    
        $member = User::select('*','users.id AS users_id')
                ->leftjoin('member_gym', 'users.id', '=', 'member_gym.idmember')
                ->role('member')
                ->get();
    
        $checkin = DB::table('checkin_member')->where('idmember', $user->id)->orderBy('id', 'desc')->limit(1);
    
        return view('admin.member.index', [
            'paket' => $paket,
            'paket_trainer' => $paket_trainer,
            'trainer' => $trainer,
            'member' => $member,
            'active' => $memberActive,
            'checkin' => $checkin
        ]);
    }

    public function show($id)
    {
        $member = DB::table('member_gym')
            ->select(
                'member_gym.id as id',
                'member_gym.idmember as idmember',
                'users.photo',
                'users.upload',
                'users.name',
                'users.number_phone',
                'member_gym.created_at',
                'member_gym.end_training',
                'packet_name',
                'member_gym.description'
            )
            ->join('users', 'users.id', '=', 'member_gym.idmember')
            ->join('packets', 'packets.id', '=', 'member_gym.idpaket')
            ->where('member_gym.id', $id)
            ->first();
    
        if (!$member) {
            // Misalnya redirect atau kasih pesan error
            return abort(404, 'Member tidak ditemukan');
        }
    
        // Ambil data schedule
        $schedule = DB::table('member_gym')
            ->select('start_training', 'end_training', 'idmember')
            ->where('idmember', $member->idmember) // <-- gunakan $member setelah dicek
            ->first();
    
        // Ambil nama trainer
        $trainer = DB::table('top_upInformation')
            ->select('users.name')
            ->join('users', 'users.id', '=', 'top_upInformation.idtrainer')
            ->where('iduser', $member->idmember)
            ->first();
    
        // Hitung sisa hari training
        $isInRange = false;
        $remainingDays = 0;
    
        $currentTime = now();
        $endTraining = \Carbon\Carbon::parse($member->end_training);
    
        if ($currentTime->lessThanOrEqualTo($endTraining)) {
            $remainingDays = floor($currentTime->diffInDays($endTraining));
        }
    
        // Ambil data check-in member
        $schedule_member = DB::table('checkin_member')
            ->select(
                'checkin_member.id as id',
                'name',
                'key_fob',
                'checkin_member.created_at as created_at',
                'status',
                'checkin_member.updated_at as updated_at'
            )
            ->join('users', 'users.id', '=', 'checkin_member.idmember')
            ->where('checkin_member.idmember', $member->idmember)
            ->get();
    
        $events = [];
        foreach ($schedule_member as $checkin) {
            $events[] = [
                'title' => $checkin->status,
                'start' => \Carbon\Carbon::parse($checkin->created_at)->toDateString(),
                'color' => 'red',
            ];
        }
    
        return view('admin.member.show', compact(
            'member',
            'remainingDays',
            'schedule_member',
            'events',
            'isInRange',
            'schedule',
            'trainer'
        ));
    }

    public function cetakBarcode($id)
    {
        // Ambil data member + barcode_path
        $member = DB::table('member_gym')
            ->join('users', 'member_gym.idmember', '=', 'users.id')
            ->select('users.barcode_path', 'users.name', 'users.barcode', 'member_gym.id')
            ->where('member_gym.id', $id)
            ->first();
    
        if (!$member) {
            abort(404, 'Member not found');
        }
    
        // QR Code berisi data barcode dari member
        $url = route('admin.information.member', ['id' => $member->id]); // ID numeric
        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
    
        // Simpan ke file
        $filename = $member->barcode . '_qrcode.png';
        $path = public_path('assets/images/barcode/' . $filename);
        $result->saveToFile($path);
    
        // Kirim nama file ke view
        $member->qr_filename = $filename;
    
        return view('admin.member.cetak_barcode', compact('member'));
    }

    public function destroy($id)
    {
        $member = Member_gym::findOrFail($id);
    
        // Step 1: Cek apakah ada paket trainer yang digunakan
        if (!empty($member->idpacket_trainer)) {
            $packetTrainer = Packet_trainer::find($member->idpacket_trainer);
    
            if ($packetTrainer) {
                // Step 2: Cari TopupInformation user
                $topupInfo = TopupInformation::where('iduser', $member->idmember)->latest()->first();
    
                if ($topupInfo) {
                    // Step 3: Tambahkan poin kembali
                    $topupInfo->update([
                        'total_poin' => $topupInfo->total_poin + $packetTrainer->poin,
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    
        // Step 4: Hapus data member_gym
        $member->delete();
    
        return redirect()->route('admin.data_member')->with('success', 'Member berhasil dihapus dan poin dikembalikan!');
    }

    public function tambah_dataMember(Request $request)
    {
        $request->validate([
            'member_name' => 'required|string', // ganti dari idmember ke member_name
            'idpaket' => 'required|exists:packets,id',
            'start_training' => 'required|date',
            'end_training' => 'required|date|after_or_equal:start_training',
        ]);

        // Cari user berdasarkan nama member yang diketik
        $member = User::where('name', $request->member_name)->first();

        if (!$member) {
            return redirect()->back()->withErrors(['member_name' => 'Member tidak ditemukan'])->withInput();
        }

        $idmember = $member->id;

        $endTraining = Carbon::parse($request->end_training)->endOfDay();

        // Simpan data member
        Member_gym::create([
            'iduser' => auth()->id(),
            'idmember' => $idmember,
            'idpaket' => $request->idpaket,
            'total_price' => $request->total_price,
            'start_training' => $request->start_training,
            'end_training' => $endTraining,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update poin jika memilih paket trainer
        if ($request->filled('idpacket_trainer')) {
            $topupInfo->update([
                'total_poin' => max(0, $topupInfo->total_poin - 1),
                'datetop_up' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Data member berhasil ditambahkan.');
    }

    public function getMember($id)
    {
        $member = DB::table('member_gym')
            ->join('users', 'users.id', '=', 'member_gym.idmember')
            ->join('packet_trainer', 'packet_trainer.id', '=', 'member_gym.idpacket_trainer')
            ->select(
                'member_gym.id',
                DB::raw('DATE_FORMAT(member_gym.start_training, "%Y-%m-%d") as start_training'),
                DB::raw('DATE_FORMAT(member_gym.end_training, "%Y-%m-%d") as end_training'),
                'users.name',
                'member_gym.idpaket',
                'member_gym.idpacket_trainer',
                'member_gym.idmember',
                'packet_trainer.pertemuan',
                'packet_trainer.poin',
                'packet_trainer.price',
            )
            ->where('member_gym.id', $id)
            ->first();

        if ($member) {
            return response()->json($member);
        }

        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    public function scheduleMember()
{
    $user = Auth::user();
    
    $trainers = User::role('trainer')->get();
    $ptrainer = DB::table('packet_trainer')->get();
    
    $memberSuper = User::role('super-admin')->where('id', $user->id)->get();
    $memberAdmin = User::role('admin')->where('id', $user->id)->get();
    $isSuperUser = $user->hasRole(['super-admin', 'admin']);

    // Ambil data member gym
    if ($isSuperUser) {
        $member_gym_schedule = DB::table('schedule_trainer')
            ->select('schedule_trainer.iduser as iduser', 'muser.name as customer_name', 'tuser.name as name', 
                     'schedule_trainer.date_trainer as date_trainer', 'schedule_trainer.start_time as start_time', 
                     'schedule_trainer.end_time as end_time')
            ->join('top_upInformation', 'top_upInformation.id', '=', 'schedule_trainer.idtopup_informasi')
            ->join('users as tuser', 'tuser.id', '=', 'top_upInformation.idtrainer')
            ->join('users as muser', 'muser.id', '=', 'schedule_trainer.iduser')
            ->get();
    } else {
        $member_gym_schedule = DB::table('schedule_trainer')
            ->select('schedule_trainer.iduser as iduser', 'muser.name as customer_name', 'tuser.name as name', 
                     'schedule_trainer.date_trainer as date_trainer', 'schedule_trainer.start_time as start_time', 
                     'schedule_trainer.end_time as end_time')
            ->join('top_upInformation', 'top_upInformation.id', '=', 'schedule_trainer.idtopup_informasi')
            ->join('users as tuser', 'tuser.id', '=', 'top_upInformation.idtrainer')
            ->join('users as muser', 'muser.id', '=', 'schedule_trainer.iduser')
            ->where('schedule_trainer.iduser', $user->id)
            ->get();
    }

    // Ambil data member terbaru untuk menentukan rentang tanggal yang valid (start_training dan end_training)
    $member = DB::table('top_upInformation')
        ->join('users', 'users.id', '=', 'top_upInformation.iduser')
        ->leftJoin('information_schedule', 'information_schedule.iduser', '=', 'top_upInformation.idtrainer')
        ->leftJoin('users as trainers', 'trainers.id', '=', 'top_upInformation.idtrainer')
        ->leftJoin('member_gym', 'member_gym.idmember', '=', 'top_upInformation.iduser')
        ->select(
            'top_upInformation.iduser as idmember',
            'users.name as name',
            'top_upInformation.id as idtrainer',
            'trainers.name as trainer_name',
            'information_schedule.start_time',
            'information_schedule.end_time',
            'information_schedule.start_break',
            'information_schedule.end_break',
            DB::raw('DATE_FORMAT(member_gym.start_training, "%Y-%m-%d") as start_training'),
            DB::raw('DATE_FORMAT(member_gym.end_training, "%Y-%m-%d") as end_training')
        )
        ->where('member_gym.idmember', $user->id)
        ->orderBy('member_gym.id', 'desc')
        ->first();
        
    // dd($member);

    if (!$member) {
        $unavailableDates = [];
    } else {
        if (is_null($member->idtrainer)) {
            $unavailableDates = [];
        } else {
            $unavailableDates = DB::table('schedule_trainer')
                ->join('top_upInformation', 'top_upInformation.id', '=', 'schedule_trainer.idtopup_informasi')
                ->where('top_upInformation.idtrainer', $member->idtrainer)
                ->pluck('date_trainer')
                ->toArray();
        }
    }
    
    $slots = [];

    if ($member && $member->idtrainer && $member->start_time && $member->end_time) {
        $startTime = Carbon::createFromFormat('H:i:s', max('07:00:00', $member->start_time));
        $startBreak = Carbon::createFromFormat('H:i:s', $member->start_break ?? '00:00:00');
        $endBreak = Carbon::createFromFormat('H:i:s', $member->end_break ?? '00:00:00');
        $endTime = Carbon::createFromFormat('H:i:s', $member->end_time);
    
        while ($startTime->copy()->addHour() <= $endTime) {
            $slotStart = $startTime->copy();
            $slotEnd = $startTime->copy()->addHour();
        
            $overlapsBreak = $slotStart < $endBreak && $slotEnd > $startBreak;
        
            if ($overlapsBreak) {
                $startTime->addHour();
                continue;
            }
        
            $slots[] = ['start' => $slotStart->format('H:i')];
        
            $startTime->addHour();
        }

    }

    // $schedule = DB::table('schedule_trainer')
    //     ->join('top_upInformation', 'top_upInformation.id', '=', 'schedule_trainer.idtopup_informasi')
    //     ->join('users as umember', 'top_upInformation.iduser', '=', 'umember.id')
    //     ->join('users as utrainer', 'top_upInformation.idtrainer', '=', 'utrainer.id')
    //     ->leftJoin('packet_trainer', 'packet_trainer.id', '=', 'top_upInformation.idtop_up')
    //     ->leftJoin('member_gym', 'member_gym.idmember', '=', 'top_upInformation.iduser')
    //     ->select(
    //         'schedule_trainer.iduser',
    //         'umember.name as customer_name',
    //         'utrainer.name as name',
    //         DB::raw('GROUP_CONCAT(DISTINCT utrainer.name SEPARATOR ", ") as trainer_name'),
    //         DB::raw('DATE_FORMAT(MIN(member_gym.start_training), "%d %M %Y") as training_date'),
    //         DB::raw('DATE_FORMAT(MIN(member_gym.end_training), "%d %M %Y") as training_end'),
    //         DB::raw('IFNULL(packet_trainer.poin, 0) as total_sessions'),
    //         DB::raw('(SELECT COUNT(*) FROM schedule_trainer WHERE schedule_trainer.iduser = member_gym.id) as sessions_taken'),
    //         DB::raw('IFNULL(packet_trainer.poin, 0) - 
    //                  (SELECT COUNT(*) FROM schedule_trainer WHERE schedule_trainer.iduser = member_gym.id) 
    //                  as remaining_sessions')
    //     )
    //     ->groupBy(
    //         'schedule_trainer.iduser',
    //         'umember.name',
    //         'member_gym.id',
    //         'top_upInformation.idtop_up',
    //         'utrainer.name',
    //         'packet_trainer.poin'
    //     )
    //     ->where('member_gym.idmember', $user->id)
    //     ->orderBy('member_gym.created_at', 'desc')
    //     ->get();
    
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
        ->where('top_upInformation.iduser', $user->id)
        ->where('status', 1)
        ->orderBy('top_upInformation.created_at', 'asc')
        ->get();
    
    $remaining_sessions = 0;
    
    foreach ($schedule as $v_schedule) {
        $count = DB::table('schedule_trainer')
            ->where('idtopup_informasi', $v_schedule->id)
            ->count();
            
        $remaining = $v_schedule->sessions_taken - $count;
        $remaining_sessions += $remaining;
    }
    

    return view('admin.member.schedule', compact('ptrainer', 'trainers', 'member', 'schedule', 'member_gym_schedule', 'slots', 'remaining_sessions'));
}

    
    public function storeSchedule(Request $request)
{
    $request->validate([
        'idtopup_informasi' => 'required',
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
                
                // Memeriksa apakah tanggal yang dipilih sudah terjadwal untuk idtrainer
                $unavailableDates = DB::table('schedule_trainer')
                    ->join('top_upInformation', 'top_upInformation.id', '=', 'schedule_trainer.idtopup_informasi')
                    ->where('top_upInformation.idtrainer', $request->idtrainer)
                    ->pluck('date_trainer')
                    ->toArray();
                
                if (in_array($value, $unavailableDates)) {
                    $fail('Tanggal ini sudah terjadwal untuk trainer yang dipilih. Silakan pilih tanggal lain.');
                }
            },
        ],
        'start_time' => 'required',
    ]);
    
    $memberGym = DB::table('top_upInformation')->where('iduser', $request->iduser)->where('status', 1)->orderBy('created_at', 'desc')->first();

    if (!$memberGym) {
        return back()->withErrors(['iduser' => 'Member tidak valid.'])->withInput();
    }
    
    $packetTrainer = DB::table('packet_trainer')
        ->select('pertemuan')
        ->where('id', $memberGym->idtop_up)
        ->first();
    
    $limit = $packetTrainer->pertemuan ?? 0;
    
    $scheduleCount = ScheduleTrainer::where('iduser', $request->iduser)->count();
    
    if ($scheduleCount >= $limit) {
        return back()->withErrors(['iduser' => 'Member ini sudah mencapai batas maksimal jadwal (' . $limit . ').'])->withInput();
    }

    ScheduleTrainer::create([
        'idtopup_informasi' => $request->idtopup_informasi,
        'iduser' => $request->iduser,
        'date_trainer' => $request->date_trainer,
        'start_time' => $request->start_time,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('admin.jadwal_member')->with('success', 'Jadwal berhasil ditambahkan.');
}
    
    public function getScheduleDetail($id)
    {
        $schedules = DB::table('schedule_trainer')
            ->select(
                'schedule_trainer.id',
                'schedule_trainer.date_trainer as training_date',
                'schedule_trainer.start_time',
                'schedule_trainer.end_time',
                'users.name as trainer_name',
                'users.id as idtrainer'
            )
            ->join('top_upInformation', 'top_upInformation.id', '=', 'schedule_trainer.idtopup_informasi')
            ->join('users', 'users.id', '=', 'top_upInformation.idtrainer')
            ->where('schedule_trainer.idtopup_informasi', $id)
            ->get();
    
        $trainerIds = $schedules->pluck('idtrainer')->unique();
    
        $availableTimes = DB::table('information_schedule')
            ->whereIn('iduser', $trainerIds)
            ->select(
                'iduser as idtrainer',
                'start_time',
                'start_break',
                'end_break',
                'end_time'
            )
            ->get();
    
        $topup = DB::table('top_upInformation')->where('id', $id)->first();
    
        $trainingRange = DB::table('member_gym')
            ->where('idmember', $topup->iduser)
            ->orderBy('created_at', 'desc')
            ->select('start_training', 'end_training')
            ->first();
    
        return response()->json([
            'schedules' => $schedules,
            'available_times' => $availableTimes,
            'training_range' => $trainingRange
        ]);
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
            'start_time' => $startTime,
            'date_trainer' => $trainingDate,
            'idtrainer' => $request->idtrainer,
        ]);

        return redirect()->route('admin.jadwal_member')->with('success', 'Jadwal berhasil di update.');
    }
    
    public function deleteSchedule($id)
    {
        $schedule = ScheduleTrainer::findOrFail($id);
        $schedule->delete();
    
        return redirect()->route('admin.jadwal_member')->with('success', 'Schedule deleted successfully.');
    }

    public function check_in(Request $request)
    {
        $request->validate([
            'key_fob' => 'required',
        ]);

        // $existingCheckIn = CheckinMember::where('idmember', $request->idmember)
        // ->whereNotNull('created_at')
        // ->first();

        // if ($existingCheckIn) {
        //     return redirect()->back()->with('error', 'Member sudah melakukan check-in.');
        // }

        CheckinMember::create([
            'idmember' => $request->idmember,
            'key_fob' => $request->key_fob,
            'status' => 'Checkin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Data member berhasil ditambahkan.');
    }

    public function checkin(Request $request){
        $user = auth()->user(); // pastikan user sudah login
        $admin = User::role('super-admin')->exists();
        $super = User::role('admin')->exists();
        $isSuperUser = ($super || $admin);
    
        // Ambil filter dari request
        $filterDate = $request->input('filter_date', date('Y-m-d'));
        $filterName = $request->input('filter_name');
    
        $startDate = \Carbon\Carbon::parse($filterDate)->startOfDay();
        $endDate = \Carbon\Carbon::parse($filterDate)->endOfDay();
    
        // Query builder
        $query = DB::table('checkin_member')
            ->select('checkin_member.id as id', 'name', 'key_fob', 'checkin_member.created_at as created_at', 'status', 'checkin_member.updated_at as updated_at')
            ->join('users', 'users.id', '=', 'checkin_member.idmember')
            ->whereBetween('checkin_member.created_at', [$startDate, $endDate]);
    
        // Jika bukan super admin/admin, batasi hanya data member sendiri
        if (!$isSuperUser) {
            $query->where('idmember', $user->id);
        }
    
        // Jika ada filter nama, tambahkan filter LIKE di nama user
        if ($filterName) {
            $query->where('users.name', 'like', '%' . $filterName . '%');
        }
    
        $schedule_member = $query->get();
    
        $events = [];
        foreach ($schedule_member as $checkin) {
            $events[] = [
                'title' => $checkin->status,
                'start' => \Carbon\Carbon::parse($checkin->created_at)->toDateString(),
                'color' => 'red',
            ];
        }
    
        return view('admin.member.checkin', compact('schedule_member', 'events'));
    }        
    
    public function checkout($id)
    {
        // Cari transaksi berdasarkan ID
        $transaction = CheckinMember::findOrFail($id);

        // Periksa apakah created_at sama dengan updated_at
        if ($transaction->created_at == $transaction->updated_at) {
            // Update status dan updated_at
            $transaction->status = 'Checkout';
            $transaction->updated_at = now(); // Set waktu sekarang
            $transaction->save();

            // Redirect atau tampilkan pesan sukses
            return redirect()->route('admin.checkin')->with('success', 'Member berhasil checkout!');
        }

        return redirect()->route('admin.checkin')->with('error', 'Data tidak valid.');
    }
    
    public function top_upmember() {
        $today = Carbon::now()->startOfDay()->toDateString();
        
        $data = DB::table('top_upInformation')
            ->select(
                DB::raw('MAX(top_upInformation.id) as id'),
                'users.name',
                DB::raw('MAX(datetop_up) as datetop_up'),
                DB::raw("SUM(CASE 
                            WHEN DATE_ADD(datetop_up, INTERVAL day DAY) > '$today' 
                            THEN total_poin ELSE 0 
                        END) as total_poin"),
                DB::raw('MAX(day) as day'),
                'top_upInformation.iduser'
            )
            ->where('status', 1)
            ->leftJoin('users', 'users.id', '=', 'top_upInformation.iduser')
            ->groupBy('top_upInformation.iduser', 'users.name')
            ->orderBy('id', 'desc')
            ->get();
    
        $data_topup = DB::table('packet_trainer')->select('*')->get();
    
        $data_user = User::select('*')->role('member')->get();
        
        $trainer = User::select('*','users.id AS id')
                ->leftjoin('member_gym', 'users.id', '=', 'member_gym.idmember')
                ->role('trainer')
                ->get();
    
        return view('admin.member.top_up', compact('data', 'data_topup', 'data_user', 'trainer'));
    }
    
    public function topupDetail($id) {
        $topups = DB::table('top_upInformation')
            ->select('top_upInformation.id', 'iduser', 'name', 'idadmin', 'idtrainer', 'idtop_up', 'total_poin', 'status', 'datetop_up', 'day')
            ->join('users', 'users.id', '=', 'top_upInformation.idtrainer')
            ->where('top_upInformation.iduser', $id)
            ->where('status', 1)
            ->orderBy('datetop_up', 'desc')
            ->get();
            
        return response()->json($topups);
    }
    
    public function addtop_upmember(Request $request)
    {
        $validated = $request->validate([
            'iduser' => 'required',
            'total_poin' => 'required|exists:packet_trainer,id',
            'datetop_up' => 'required|date',
            'idtrainer' => 'required'
        ]);
    
        $topUp = Packet_trainer::find($request->total_poin);
    
        if (!$topUp) {
            return response()->json(['error' => 'Paket Top-up tidak ditemukan.'], 404);
        }
    
        $topupInfo = TopupInformation::create([
            'iduser' => $request->iduser,
            'idadmin' => auth()->id(),
            'idtrainer' => $request->idtrainer,
            'idtop_up' => $topUp->id,
            'total_poin' => $topUp->poin,
            'status' => 1,
            'datetop_up' => $request->datetop_up,
            'day' => 30,
        ]);
    
        return redirect()->route('admin.topup_member')->with('success', 'Topup berhasil ditambahkan!');
    }
    
    public function updateTopup(Request $request, $id)
    {
        $request->validate([
            'iduser' => 'required',
            'total_poin' => 'required|exists:packet_trainer,id',
            'datetop_up' => 'required|date',
            'idtrainer' => 'required'
        ]);
        
        $topUpData = Packet_trainer::find($request->total_poin);
    
        if (!$topUpData) {
            return response()->json(['error' => 'Paket Top-up tidak ditemukan.'], 404);
        }
    
        $topup = TopupInformation::findOrFail($id);
        // dd($topup);
        $topup->update([
            'iduser' => $request->iduser,
            'idtrainer' => $request->idtrainer,
            'idtop_up' => $topUpData->id,
            'total_poin' => $topUpData->poin,
            'datetop_up' => $request->datetop_up,
            'updated_at' => now(),
        ]);
        
        return redirect()->route('admin.topup_member')->with('success', 'Topup berhasil diperbarui!');
    }
    
    public function softDelete($id)
    {
        $topup = DB::table('top_upInformation')->where('id', $id)->first();
    
        if (!$topup) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    
        DB::table('top_upInformation')->where('id', $id)->update(['status' => 2]);
    
        return response()->json(['message' => 'Topup berhasil dihapus']);
    }
    
    public function getTopupItem($id)
    {
        $item = DB::table('top_upInformation')
        ->where('id', $id)
        ->first();
        return response()->json($item);
    }

    public function perpanjanganmember(Request $request, $id)
    {
        $request->validate([
            'idpaket' => 'required|exists:packets,id',
            'start_training' => 'required|date',
            'end_training' => 'required|date|after_or_equal:start_training',
            'total_price' => 'required|numeric|min:0',
        ]);

        $idmember = $request->idmember;

        // Ambil tanggal end_training dan set ke akhir hari
        $endTraining = Carbon::parse($request->end_training)->endOfDay();

        // Simpan data perpanjangan ke tabel member_gyms (bukan update yang lama)
        Member_gym::create([
            'iduser'         => auth()->id(),
            'idmember'       => $idmember,
            'idpaket'        => $request->idpaket,
            'total_price'    => $request->total_price,
            'start_training' => $request->start_training,
            'end_training'   => $endTraining,
            'description'    => $request->description,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // === Opsi: Kurangi poin trainer jika dipakai ===
        if ($request->filled('idpacket_trainer')) {
            // Misalnya kamu punya model TopupInfo yang relasi dengan user
            $topupInfo = auth()->user()->topupInfo; // Sesuaikan dengan model relasimu

            if ($topupInfo && $topupInfo->total_poin > 0) {
                $topupInfo->update([
                    'total_poin' => $topupInfo->total_poin - 1,
                    'datetop_up' => now(),
                ]);
            }
        }

        return response()->json(['message' => 'Perpanjangan berhasil disimpan.']);
    }

}