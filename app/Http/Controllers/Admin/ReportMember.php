<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Expense;
use App\Models\Income;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class ReportMember extends Controller
{
    public function __construct(){
        if (!auth()->user()->can('view-laporan')) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
    
    public function reportDays(Request $request){
        $trainer = User::select('*','users.id AS users_id')
                ->role('trainer')
                ->first();
                
        $query = DB::table('member_gym')
            ->select(
                'users.name as name_admin', 
                DB::raw('COUNT(member_gym.id) as total_visits'),
                DB::raw('SUM(member_gym.total_price) as price_member')
            )
            ->leftJoin('users', 'users.id', '=', 'member_gym.iduser')
            ->where('role', $trainer->role)
            ->groupBy('member_gym.iduser', 'users.name');
            
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('member_gym.start_training', [$request->start_date, $request->end_date]);
        }
    
        $data = $query->orderBy('start_training', 'desc')->paginate(10);
        
        return view('admin.report.reportday', [
            'data' => $data,
        ]);
    }

    public function reportmemberDays(Request $request)
    {
        $start_date = $request->start_date ?? Carbon::today()->format('Y-m-d');
        $end_date = $request->end_date ?? Carbon::today()->format('Y-m-d');
    
        $query = DB::table('checkin_member')
            ->select(
                'users.name as name_member',
                'key_fob',
                'checkin_member.created_at',
                'checkin_member.updated_at as updated_training',
                'status'
            )
            ->leftJoin('users', 'users.id', '=', 'checkin_member.idmember')
            ->whereBetween('checkin_member.created_at', [
                Carbon::parse($start_date)->startOfDay(),
                Carbon::parse($end_date)->endOfDay()
            ])
            ->orderBy('checkin_member.created_at', 'desc')
            ->groupBy(
                'checkin_member.idmember',
                'users.name',
                'key_fob',
                'checkin_member.created_at',
                'checkin_member.updated_at',
                'status'
            );
    
        $data = $query->paginate(10);
    
        return view('admin.report.reportallmember', compact('data', 'start_date', 'end_date'));
    }

    public function reportmemberActive(Request $request)
    {
        $subQuery = DB::table('member_gym')
            ->select(DB::raw('MAX(id) as latest_id'))
            ->groupBy('idmember');
    
        $query = DB::table('member_gym')
            ->joinSub($subQuery, 'latest', function ($join) {
                $join->on('member_gym.id', '=', 'latest.latest_id');
            })
            ->leftJoin('users', 'users.id', '=', 'member_gym.idmember')
            ->select(
                'users.name as name_member',
                'member_gym.start_training',
                'member_gym.end_training',
                'member_gym.total_price'
            )
            ->whereDate('member_gym.start_training', '<=', now())
            ->whereDate('member_gym.end_training', '>=', now());
    
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('member_gym.start_training', [$request->start_date, $request->end_date]);
        }
    
        $data = $query->orderBy('member_gym.start_training', 'desc')->paginate(10);
    
        return view('admin.report.reportmemberactive', [
            'data' => $data,
        ]);
    }

    public function reportmembernonActive(Request $request){
        // Subquery: ambil data terakhir per member
        $member = $trainer = User::select('*','users.id AS users_id')
                ->role('member')
                ->get();
    
        $data = DB::table('member_gym')
        ->join('users', 'users.id', '=', 'member_gym.idmember')
        ->whereIn('member_gym.idmember', $member->pluck('id')) // Filter berdasarkan ID user yang punya role 'member'
        ->whereDate('member_gym.end_training', '<', now()) // Member yang tidak aktif
        ->select(
            'users.name as name_member',
            'member_gym.start_training',
            'member_gym.end_training',
            'member_gym.total_price'
        );

        // Jika ada filter berdasarkan tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $data->whereBetween('member_gym.start_training', [$request->start_date, $request->end_date]);
        }
    
        // Ambil data dan urutkan berdasarkan tanggal end_training
        $data = $data->orderBy('member_gym.end_training', 'desc')->paginate(10);
            
        return view('admin.report.reportmembernonactive', [
            'data' => $data,
        ]);
    }
    
    
    public function exportExcel(Request $request)
    {
        
        // Load library PhpSpreadsheet secara manual
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php');
        require_once base_path('vendor/ZipStream/src/EndOfCentralDirectory.php');
        require_once base_path('vendor/ZipStream/src/CentralDirectoryFileHeader.php');
        require_once base_path('vendor/ZipStream/src/Time.php');
        require_once base_path('vendor/ZipStream/src/LocalFileHeader.php');
        require_once base_path('vendor/ZipStream/src/PackField.php');
        require_once base_path('vendor/ZipStream/src/Zs/ExtendedInformationExtraField.php');
        require_once base_path('vendor/ZipStream/src/Version.php');
        require_once base_path('vendor/ZipStream/src/GeneralPurposeBitFlag.php');
        require_once base_path('vendor/ZipStream/src/File.php');
        require_once base_path('vendor/ZipStream/src/CompressionMethod.php');
        require_once base_path('vendor/ZipStream/src/OperationMode.php');
        require_once base_path('vendor/ZipStream/src/ZipStream.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream0.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DefinedNames.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Metadata.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Reader/Xlsx/Namespaces.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/XMLWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Iterator.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/HashTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Workbook.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Table.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/StringTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsVBA.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsRibbon.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Rels.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Drawing.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DocProps.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/ContentTypes.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Comments.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Chart/DataSeries.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Chart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/AddressRange.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DefaultValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IgnoredErrors.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DataType.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/CellAddress.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Cell.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Coordinate.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Validations.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Functions.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/IComparable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Supervisor.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/NumberFormat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Alignment.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Border.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Borders.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Fill.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Color.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Font.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Security.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/IntOrFloat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Properties.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter/Column/Rule.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Dimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/ColumnDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/RowDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/SheetView.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/HeaderFooter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageMargins.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageSetup.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Memory/SimpleCache3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Settings.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Cells.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/CellsFactory.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/StringHelper.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/BranchPruner.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/Logger.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/CyclicReferenceStack.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Category.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Calculation.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/Date.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/IWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/BaseWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php');
    

        $start_date = $request->start_date ?? Carbon::today()->format('Y-m-d');
        $end_date = $request->end_date ?? Carbon::today()->format('Y-m-d');
    
        $query = DB::table('checkin_member')
            ->select(
                'users.name as name_member',
                'key_fob',
                'checkin_member.created_at',
                'checkin_member.updated_at as updated_training',
                'status'
            )
            ->leftJoin('users', 'users.id', '=', 'checkin_member.idmember')
            ->whereBetween('checkin_member.created_at', [
                Carbon::parse($start_date)->startOfDay(),
                Carbon::parse($end_date)->endOfDay()
            ])
            ->orderBy('checkin_member.created_at', 'desc')
            ->groupBy(
                'checkin_member.idmember',
                'users.name',
                'key_fob',
                'checkin_member.created_at',
                'checkin_member.updated_at',
                'status'
            );
    
        $data = $query->orderBy('created_at', 'desc')->get();
    
    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Buat header
    $headers = ['Nama member', 'No. Loker', 'Check-in', 'Check-out'];
    $column = 'A';
    
    // Atur warna dan style header
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF00'], // Warna kuning
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    foreach ($headers as $header) {
        $sheet->setCellValue($column . '1', $header);
        $sheet->getStyle($column . '1')->applyFromArray($headerStyle);
        $column++;
    }
    
    // Masukkan data ke dalam Excel
    $row = 2;
    $total_price = 0;
    
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item->name_member);
        $sheet->setCellValue('B' . $row, $item->key_fob);
        $sheet->setCellValue('C' . $row, $item->created_at);
        $sheet->setCellValue('D' . $row, $item->updated_training);
        
        $row++;
    }
    
    // Tambahkan border untuk seluruh tabel
    $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    $sheet->getStyle("A1:C" . ($row - 1))->applyFromArray($borderStyle);
    
    // Tambahkan total price di bawah data
    $sheet->getStyle("C$row:D$row")->applyFromArray([
        'font' => ['bold' => true],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);
    
    // Simpan sebagai file Excel
    $writer = new Xlsx($spreadsheet);
    $fileName = 'laporan_member.xlsx';
    $filePath = public_path($fileName);
    
    // Simpan ke server
    $writer->save($filePath);
    
        // Berikan file ke user untuk didownload
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    public function exportTrainerExcel(Request $request)
    {
        
        // Load library PhpSpreadsheet secara manual
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php');
        require_once base_path('vendor/ZipStream/src/EndOfCentralDirectory.php');
        require_once base_path('vendor/ZipStream/src/CentralDirectoryFileHeader.php');
        require_once base_path('vendor/ZipStream/src/Time.php');
        require_once base_path('vendor/ZipStream/src/LocalFileHeader.php');
        require_once base_path('vendor/ZipStream/src/PackField.php');
        require_once base_path('vendor/ZipStream/src/Zs/ExtendedInformationExtraField.php');
        require_once base_path('vendor/ZipStream/src/Version.php');
        require_once base_path('vendor/ZipStream/src/GeneralPurposeBitFlag.php');
        require_once base_path('vendor/ZipStream/src/File.php');
        require_once base_path('vendor/ZipStream/src/CompressionMethod.php');
        require_once base_path('vendor/ZipStream/src/OperationMode.php');
        require_once base_path('vendor/ZipStream/src/ZipStream.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream0.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DefinedNames.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Metadata.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Reader/Xlsx/Namespaces.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/XMLWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Iterator.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/HashTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Workbook.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Table.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/StringTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsVBA.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsRibbon.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Rels.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Drawing.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DocProps.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/ContentTypes.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Comments.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Chart/DataSeries.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Chart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/AddressRange.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DefaultValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IgnoredErrors.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DataType.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/CellAddress.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Cell.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Coordinate.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Validations.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Functions.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/IComparable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Supervisor.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/NumberFormat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Alignment.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Border.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Borders.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Fill.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Color.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Font.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Security.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/IntOrFloat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Properties.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter/Column/Rule.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Dimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/ColumnDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/RowDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/SheetView.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/HeaderFooter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageMargins.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageSetup.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Memory/SimpleCache3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Settings.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Cells.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/CellsFactory.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/StringHelper.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/BranchPruner.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/Logger.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/CyclicReferenceStack.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Category.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Calculation.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/Date.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/IWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/BaseWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php');
    
        $trainer = User::select('*','users.id AS users_id')
                ->role('trainer')
                ->first();
                
        $query = DB::table('member_gym')
            ->select(
                'users.name as name_admin', 
                DB::raw('COUNT(member_gym.id) as total_visits'),
                DB::raw('SUM(member_gym.total_price) as price_member'),
            )
            ->leftJoin('users', 'users.id', '=', 'member_gym.iduser')
            ->where('role', $trainer->role)
            ->groupBy('member_gym.iduser', 'users.name');
            
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('member_gym.start_training', [$request->start_date, $request->end_date]);
        }
    
        $data = $query->orderBy('start_training', 'desc')->paginate(10);

    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Buat header
    $headers = ['Nama Trainer', 'Total Member', 'Total Harga'];
    $column = 'A';
    
    // Atur warna dan style header
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF00'], // Warna kuning
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    foreach ($headers as $header) {
        $sheet->setCellValue($column . '1', $header);
        $sheet->getStyle($column . '1')->applyFromArray($headerStyle);
        $column++;
    }
    
    // Masukkan data ke dalam Excel
    $row = 2;
    $total_price = 0;
    
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item->name_admin);
        $sheet->setCellValue('B' . $row, $item->total_visits);
        $sheet->setCellValue('C' . $row, $item->price_member);
        
        // Tambahkan ke total price
        $total_price += $item->price_member;
        
        $row++;
    }
    
    // Tambahkan border untuk seluruh tabel
    $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    $sheet->getStyle("A1:C" . ($row - 1))->applyFromArray($borderStyle);
    
    // Tambahkan total price di bawah data
    $sheet->setCellValue('B' . $row, 'Total Keseluruhan:');
    $sheet->setCellValue('C' . $row, $total_price);
    $sheet->getStyle("B$row:C$row")->applyFromArray([
        'font' => ['bold' => true],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);
    
    // Simpan sebagai file Excel
    $writer = new Xlsx($spreadsheet);
    $fileName = 'laporan_admin.xlsx';
    $filePath = public_path($fileName);
    
    // Simpan ke server
    $writer->save($filePath);
    
        // Berikan file ke user untuk didownload
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    public function exportMoneyExcel(Request $request)
    {
        
        // Load library PhpSpreadsheet secara manual
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php');
        require_once base_path('vendor/ZipStream/src/EndOfCentralDirectory.php');
        require_once base_path('vendor/ZipStream/src/CentralDirectoryFileHeader.php');
        require_once base_path('vendor/ZipStream/src/Time.php');
        require_once base_path('vendor/ZipStream/src/LocalFileHeader.php');
        require_once base_path('vendor/ZipStream/src/PackField.php');
        require_once base_path('vendor/ZipStream/src/Zs/ExtendedInformationExtraField.php');
        require_once base_path('vendor/ZipStream/src/Version.php');
        require_once base_path('vendor/ZipStream/src/GeneralPurposeBitFlag.php');
        require_once base_path('vendor/ZipStream/src/File.php');
        require_once base_path('vendor/ZipStream/src/CompressionMethod.php');
        require_once base_path('vendor/ZipStream/src/OperationMode.php');
        require_once base_path('vendor/ZipStream/src/ZipStream.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream0.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DefinedNames.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Metadata.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Reader/Xlsx/Namespaces.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/XMLWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Iterator.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/HashTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Workbook.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Table.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/StringTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsVBA.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsRibbon.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Rels.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Drawing.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DocProps.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/ContentTypes.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Comments.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Chart/DataSeries.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Chart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/AddressRange.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DefaultValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IgnoredErrors.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DataType.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/CellAddress.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Cell.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Coordinate.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Validations.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Functions.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/IComparable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Supervisor.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/NumberFormat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Alignment.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Border.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Borders.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Fill.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Color.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Font.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Security.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/IntOrFloat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Properties.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter/Column/Rule.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Dimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/ColumnDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/RowDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/SheetView.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/HeaderFooter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageMargins.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageSetup.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Memory/SimpleCache3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Settings.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Cells.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/CellsFactory.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/StringHelper.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/BranchPruner.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/Logger.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/CyclicReferenceStack.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Category.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Calculation.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/Date.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/IWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/BaseWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php');
    
        Carbon::setLocale('id');
    
        // Default tanggal
        $start = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->startOfDay();
    
        $end = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();
    
        // Query pertama: data dari top_upInformation
        $queryTopup = DB::table('top_upInformation')
            ->select(
                'top_upInformation.iduser',
                'muser.name as namamember',
                'tuser.name as namakasir',
                'trainer.name as namatrainer',
                'top_upInformation.created_at',
                'packet_trainer.price',
                DB::raw('0 as total_price'),
                DB::raw("CONCAT('Member Top Up dengan Trainer: ', trainer.name) as keterangan")
            )
            ->join('users as muser', 'top_upInformation.iduser', '=', 'muser.id')
            ->join('users as tuser', 'top_upInformation.idadmin', '=', 'tuser.id')
            ->join('packet_trainer', 'top_upInformation.idtop_up', '=', 'packet_trainer.id')
            ->leftJoin('users as trainer', 'top_upInformation.idtrainer', '=', 'trainer.id')
            ->where('top_upInformation.status', '1')
            ->whereBetween('top_upInformation.created_at', [$start, $end]);
    
        // Query kedua: data dari member_gym
        $queryMemberGym = DB::table('member_gym')
            ->select(
                'member_gym.idmember as iduser',
                'muser.name as namamember',
                'kasir.name as namakasir',
                DB::raw('NULL as namatrainer'),
                'member_gym.created_at',
                DB::raw('NULL as price'),
                'member_gym.total_price',
                DB::raw("'Pendaftaran Member' as keterangan")
            )
            ->join('users as muser', 'member_gym.idmember', '=', 'muser.id')
            ->leftJoin('users as kasir', 'member_gym.iduser', '=', 'kasir.id')
            ->whereBetween('member_gym.created_at', [$start, $end]);
    
        // Gabungkan keduanya
        $report = $queryTopup->unionAll($queryMemberGym)
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Header kolom
        $headers = ['Tanggal', 'Nama Member', 'Nama Kasir', 'Nama Trainer', 'Harga Top-Up', 'Harga Regis Member', 'Total Price', 'Keterangan'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'A9D08E'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
            $column++;
        }
    
        // Isi data
        $row = 2;
        $grandTotal = 0;
        foreach ($report as $item) {
            $tanggal = Carbon::parse($item->created_at)->format('d-m-Y');
            $sheet->setCellValue("A{$row}", $tanggal);
            $sheet->setCellValue("B{$row}", $item->namamember);
            $sheet->setCellValue("C{$row}", $item->namakasir);
            $sheet->setCellValue("D{$row}", $item->namatrainer ?? '-');
            $sheet->setCellValue("E{$row}", $item->price ?? 0);
            $sheet->setCellValue("F{$row}", $item->total_price ?? 0);
            $total = ($item->price ?? 0) + ($item->total_price ?? 0);
            $sheet->setCellValue("G{$row}", $total);
            $sheet->setCellValue("H{$row}", $item->keterangan);
            
            $grandTotal += $total;
            $row++;
        }
    
        // Tambahkan total keseluruhan
        $sheet->setCellValue("F{$row}", 'Total Keseluruhan:');
        $sheet->setCellValue("G{$row}", $grandTotal);
        $sheet->getStyle("F{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    
        // Border seluruh data
        $sheet->getStyle("A1:H" . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    
        // Format kolom harga sebagai rupiah
        foreach (['E', 'F', 'G'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$row}")->getNumberFormat()
                ->setFormatCode('#,##0');
        }
    
        // Download
        $writer = new Xlsx($spreadsheet);
        $fileName = 'laporan_topup_member.xlsx';
        $filePath = public_path($fileName);
        $writer->save($filePath);
    
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    public function exportActiveExcel(Request $request)
    {
        
        // Load library PhpSpreadsheet secara manual
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php');
        require_once base_path('vendor/ZipStream/src/EndOfCentralDirectory.php');
        require_once base_path('vendor/ZipStream/src/CentralDirectoryFileHeader.php');
        require_once base_path('vendor/ZipStream/src/Time.php');
        require_once base_path('vendor/ZipStream/src/LocalFileHeader.php');
        require_once base_path('vendor/ZipStream/src/PackField.php');
        require_once base_path('vendor/ZipStream/src/Zs/ExtendedInformationExtraField.php');
        require_once base_path('vendor/ZipStream/src/Version.php');
        require_once base_path('vendor/ZipStream/src/GeneralPurposeBitFlag.php');
        require_once base_path('vendor/ZipStream/src/File.php');
        require_once base_path('vendor/ZipStream/src/CompressionMethod.php');
        require_once base_path('vendor/ZipStream/src/OperationMode.php');
        require_once base_path('vendor/ZipStream/src/ZipStream.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream0.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DefinedNames.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Metadata.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Reader/Xlsx/Namespaces.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/XMLWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Iterator.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/HashTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Workbook.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Table.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/StringTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsVBA.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsRibbon.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Rels.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Drawing.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DocProps.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/ContentTypes.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Comments.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Chart/DataSeries.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Chart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/AddressRange.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DefaultValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IgnoredErrors.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DataType.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/CellAddress.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Cell.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Coordinate.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Validations.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Functions.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/IComparable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Supervisor.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/NumberFormat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Alignment.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Border.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Borders.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Fill.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Color.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Font.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Security.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/IntOrFloat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Properties.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter/Column/Rule.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Dimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/ColumnDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/RowDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/SheetView.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/HeaderFooter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageMargins.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageSetup.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Memory/SimpleCache3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Settings.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Cells.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/CellsFactory.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/StringHelper.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/BranchPruner.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/Logger.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/CyclicReferenceStack.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Category.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Calculation.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/Date.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/IWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/BaseWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php');
    
        $query = DB::table('member_gym')
            ->select(
                'users.name as name_member', 
                'member_gym.start_training as start_training', 
                'member_gym.end_training as end_training', 
                'member_gym.total_price as total_price'
            )
            ->where('idpaket', 2)
            ->whereDate('member_gym.end_training', '>=', now()) // Kondisi tanggal
            ->leftJoin('users', 'users.id', '=', 'member_gym.idmember')
            ->groupBy(
                'member_gym.idmember',
                'users.name',
                'member_gym.start_training',
                'member_gym.end_training',
                'member_gym.total_price'
            ); // Tambahkan semua kolom ke GROUP BY
            
            
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('member_gym.start_training', [$request->start_date, $request->end_date]);
        }
        
    
        $data = $query->orderBy('member_gym.start_training', 'desc')->paginate(10);

    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Buat header
    $headers = ['Nama Member', 'Mulai Latihan', 'Harga Trainer', 'Total Harga'];
    $column = 'A';
    
    // Atur warna dan style header
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF00'], // Warna kuning
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    foreach ($headers as $header) {
        $sheet->setCellValue($column . '1', $header);
        $sheet->getStyle($column . '1')->applyFromArray($headerStyle);
        $column++;
    }
    
    // Masukkan data ke dalam Excel
    $row = 2;
    $total_price = 0;
    
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item->name_member);
        $sheet->setCellValue('B' . $row, $item->start_training);
        $sheet->setCellValue('C' . $row, $item->price_member);
        $sheet->setCellValue('D' . $row, $item->total_price);
        
        // Tambahkan ke total price
        $total_price += $item->total_price;
        
        $row++;
    }
    
    // Tambahkan border untuk seluruh tabel
    $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    $sheet->getStyle("A1:C" . ($row - 1))->applyFromArray($borderStyle);
    
    // Tambahkan total price di bawah data
    $sheet->setCellValue('C' . $row, 'Total Keseluruhan:');
    $sheet->setCellValue('D' . $row, $total_price);
    $sheet->getStyle("C$row:D$row")->applyFromArray([
        'font' => ['bold' => true],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);
    
    // Simpan sebagai file Excel
    $writer = new Xlsx($spreadsheet);
    $fileName = 'laporan_memberActive.xlsx';
    $filePath = public_path($fileName);
    
    // Simpan ke server
    $writer->save($filePath);
    
        // Berikan file ke user untuk didownload
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    public function exportNonactiveExcel(Request $request)
    {
        
        // Load library PhpSpreadsheet secara manual
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php');
        require_once base_path('vendor/ZipStream/src/EndOfCentralDirectory.php');
        require_once base_path('vendor/ZipStream/src/CentralDirectoryFileHeader.php');
        require_once base_path('vendor/ZipStream/src/Time.php');
        require_once base_path('vendor/ZipStream/src/LocalFileHeader.php');
        require_once base_path('vendor/ZipStream/src/PackField.php');
        require_once base_path('vendor/ZipStream/src/Zs/ExtendedInformationExtraField.php');
        require_once base_path('vendor/ZipStream/src/Version.php');
        require_once base_path('vendor/ZipStream/src/GeneralPurposeBitFlag.php');
        require_once base_path('vendor/ZipStream/src/File.php');
        require_once base_path('vendor/ZipStream/src/CompressionMethod.php');
        require_once base_path('vendor/ZipStream/src/OperationMode.php');
        require_once base_path('vendor/ZipStream/src/ZipStream.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream0.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DefinedNames.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Metadata.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Reader/Xlsx/Namespaces.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/XMLWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Iterator.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/HashTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Workbook.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Table.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/StringTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsVBA.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsRibbon.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Rels.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Drawing.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DocProps.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/ContentTypes.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Comments.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Chart/DataSeries.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Chart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/AddressRange.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DefaultValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IgnoredErrors.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DataType.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/CellAddress.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Cell.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Coordinate.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Validations.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Functions.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/IComparable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Supervisor.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/NumberFormat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Alignment.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Border.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Borders.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Fill.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Color.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Font.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Security.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/IntOrFloat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Properties.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter/Column/Rule.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Dimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/ColumnDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/RowDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/SheetView.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/HeaderFooter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageMargins.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageSetup.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Memory/SimpleCache3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Settings.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Cells.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/CellsFactory.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/StringHelper.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/BranchPruner.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/Logger.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/CyclicReferenceStack.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Category.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Calculation.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/Date.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/IWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/BaseWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php');
    
        $query = DB::table('member_gym')
            ->select(
                'users.name as name_member', 
                'member_gym.start_training as start_training', 
                'member_gym.end_training as end_training', 
                'member_gym.total_price as total_price'
            )
            ->where('idpaket', 2)
            ->whereDate('member_gym.end_training', '<=', now())
            ->leftJoin('users', 'users.id', '=', 'member_gym.idmember')
            ->groupBy(
                'member_gym.idmember',
                'users.name',
                'member_gym.start_training',
                'member_gym.end_training',
                'member_gym.total_price'
            ); // Tambahkan semua kolom ke GROUP BY
            
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('member_gym.start_training', [$request->start_date, $request->end_date]);
        }
        
        $data = $query->orderBy('member_gym.start_training', 'desc')->paginate(10);

    // Buat objek spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Buat header
    $headers = ['Nama Member', 'Mulai Latihan', 'Harga Trainer', 'Total Harga'];
    $column = 'A';
    
    // Atur warna dan style header
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF00'], // Warna kuning
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    foreach ($headers as $header) {
        $sheet->setCellValue($column . '1', $header);
        $sheet->getStyle($column . '1')->applyFromArray($headerStyle);
        $column++;
    }
    
    // Masukkan data ke dalam Excel
    $row = 2;
    $total_price = 0;
    
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item->name_member);
        $sheet->setCellValue('B' . $row, $item->start_training);
        $sheet->setCellValue('C' . $row, $item->price_member);
        $sheet->setCellValue('D' . $row, $item->total_price);
        
        // Tambahkan ke total price
        $total_price += $item->total_price;
        
        $row++;
    }
    
    // Tambahkan border untuk seluruh tabel
    $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    $sheet->getStyle("A1:C" . ($row - 1))->applyFromArray($borderStyle);
    
    // Tambahkan total price di bawah data
    $sheet->setCellValue('C' . $row, 'Total Keseluruhan:');
    $sheet->setCellValue('D' . $row, $total_price);
    $sheet->getStyle("C$row:D$row")->applyFromArray([
        'font' => ['bold' => true],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);
    
    // Simpan sebagai file Excel
    $writer = new Xlsx($spreadsheet);
    $fileName = 'laporan_memberNonActive.xlsx';
    $filePath = public_path($fileName);
    
    // Simpan ke server
    $writer->save($filePath);
    
        // Berikan file ke user untuk didownload
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    public function exportDataTrainerExcel(Request $request)
    {
        
        // Load library PhpSpreadsheet secara manual
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php');
        require_once base_path('vendor/ZipStream/src/EndOfCentralDirectory.php');
        require_once base_path('vendor/ZipStream/src/CentralDirectoryFileHeader.php');
        require_once base_path('vendor/ZipStream/src/Time.php');
        require_once base_path('vendor/ZipStream/src/LocalFileHeader.php');
        require_once base_path('vendor/ZipStream/src/PackField.php');
        require_once base_path('vendor/ZipStream/src/Zs/ExtendedInformationExtraField.php');
        require_once base_path('vendor/ZipStream/src/Version.php');
        require_once base_path('vendor/ZipStream/src/GeneralPurposeBitFlag.php');
        require_once base_path('vendor/ZipStream/src/File.php');
        require_once base_path('vendor/ZipStream/src/CompressionMethod.php');
        require_once base_path('vendor/ZipStream/src/OperationMode.php');
        require_once base_path('vendor/ZipStream/src/ZipStream.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/ZipStream0.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DefinedNames.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Metadata.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Reader/Xlsx/Namespaces.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/XMLWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Iterator.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/HashTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/WriterPart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Workbook.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Table.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/StringTable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsVBA.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/RelsRibbon.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Rels.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Drawing.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/DocProps.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/ContentTypes.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Comments.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Chart/DataSeries.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx/Chart.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/AddressRange.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DefaultValueBinder.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/IgnoredErrors.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/DataType.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/CellAddress.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Cell.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Cell/Coordinate.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Validations.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Functions.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/IComparable.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Supervisor.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/NumberFormat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Alignment.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Border.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Borders.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Fill.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Color.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Font.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Style/Style.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Security.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/IntOrFloat.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Document/Properties.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter/Column/Rule.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/AutoFilter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Dimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/ColumnDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/RowDimension.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Protection.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/SheetView.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/HeaderFooter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageMargins.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/PageSetup.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Memory/SimpleCache3.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Settings.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/Cells.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Collection/CellsFactory.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/StringHelper.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Worksheet/Worksheet.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Theme.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/BranchPruner.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/Logger.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Engine/CyclicReferenceStack.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Category.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Calculation/Calculation.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Shared/Date.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/IWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/BaseWriter.php');
        require_once base_path('vendor/PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php');
    
        $query = DB::table('member_gym')
        ->join('information_schedule', 'information_schedule.id', '=', 'member_gym.idtrainer')
        ->join('users', 'information_schedule.iduser', '=', 'users.id')
        ->join('packet_trainer', 'packet_trainer.id', '=', 'member_gym.idpacket_trainer')
        ->select(
            'users.name as trainer_name',
            DB::raw('SUM(packet_trainer.poin) as total_poin')
        )
        ->groupBy('member_gym.idtrainer', 'users.name');

    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('member_gym.created_at', [$request->start_date, $request->end_date]);
    }

    $data = $query->get();

    // Buat Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $headers = ['Nama Trainer', 'Total Poin'];
    $column = 'A';

    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'D9EAD3'], // Hijau muda
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];

    foreach ($headers as $header) {
        $sheet->setCellValue($column . '1', $header);
        $sheet->getStyle($column . '1')->applyFromArray($headerStyle);
        $column++;
    }

    // Isi data
    $row = 2;
    $total_all_poin = 0;

    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item->trainer_name);
        $sheet->setCellValue('B' . $row, $item->total_poin);
        $total_all_poin += $item->total_poin;
        $row++;
    }

    // Total keseluruhan poin
    $sheet->setCellValue('A' . $row, 'Total Semua Trainer:');
    $sheet->setCellValue('B' . $row, $total_all_poin);
    $sheet->getStyle("A$row:B$row")->applyFromArray([
        'font' => ['bold' => true],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);

    // Tambahkan border ke seluruh data
    $sheet->getStyle("A1:B" . ($row - 1))->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);

    // Simpan file dan kirim ke user
    $writer = new Xlsx($spreadsheet);
    $fileName = 'laporan_trainer.xlsx';
    $filePath = public_path($fileName);
    $writer->save($filePath);

    return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    public function reportcheckIn(Request $request)
    {
        $query = DB::table('checkin_member')
            ->leftJoin('users', 'users.id', '=', 'checkin_member.idmember')
            ->select(
                'checkin_member.idmember',
                'users.name as member_name',
                'checkin_member.key_fob',
                'checkin_member.status',
                'checkin_member.created_at'
            );
    
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('checkin_member.created_at', [$request->start_date, $request->end_date]);
        }
    
        $rawData = $query->orderBy('checkin_member.created_at', 'asc')->get();
    
        // Gabungkan data berdasarkan pasangan Checkin - Checkout
        $grouped = [];
    
        foreach ($rawData as $record) {
            $key = $record->idmember . '_' . date('Y-m-d', strtotime($record->created_at));
    
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'member_name' => $record->member_name,
                    'key_fob' => $record->key_fob,
                    'tanggal' => date('Y-m-d', strtotime($record->created_at)),
                    'checkin' => null,
                    'checkout' => null,
                ];
            }
    
            if (strtolower($record->status) === 'checkin') {
                $grouped[$key]['checkin'] = date('H:i:s', strtotime($record->created_at));
            } elseif (strtolower($record->status) === 'checkout') {
                $grouped[$key]['checkout'] = date('H:i:s', strtotime($record->created_at));
            }
        }
    
        // Convert ke koleksi paginasi manual
        $data = collect(array_values($grouped));
    
        return view('admin.report.reportcheckin', [
            'data' => $data,
        ]);
    }
    
    public function reportAllMoney(Request $request)
    {
        Carbon::setLocale('id');
    
        // Default tanggal
        $start = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->startOfDay();
    
        $end = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();
    
        // Query 1: data top_upInformation
        $queryTopup = DB::table('top_upInformation')
            ->select(
                'top_upInformation.iduser',
                'muser.name as namamember',
                'tuser.name as namakasir',
                'trainer.name as namatrainer',
                'top_upInformation.created_at',
                'packet_trainer.price',
                DB::raw('0 as total_price'),
                DB::raw("CONCAT('Member Top Up dengan Trainer: ', trainer.name) as keterangan"),
                DB::raw('NULL as id'),
                DB::raw('NULL as description'),
                DB::raw('NULL as money')
            )
            ->join('users as muser', 'top_upInformation.iduser', '=', 'muser.id')
            ->join('users as tuser', 'top_upInformation.idadmin', '=', 'tuser.id')
            ->join('packet_trainer', 'top_upInformation.idtop_up', '=', 'packet_trainer.id')
            ->leftJoin('users as trainer', 'top_upInformation.idtrainer', '=', 'trainer.id')
            ->where('top_upInformation.status', '1')
            ->whereBetween('top_upInformation.created_at', [$start, $end]);
    
        // Query 2: data member_gym
        $queryMemberGym = DB::table('member_gym')
            ->select(
                'member_gym.idmember as iduser',
                'muser.name as namamember',
                'kasir.name as namakasir',
                DB::raw('NULL as namatrainer'),
                'member_gym.created_at',
                DB::raw('NULL as price'),
                'member_gym.total_price',
                DB::raw("'Pendaftaran Member' as keterangan"),
                DB::raw('NULL as id'),
                DB::raw('NULL as description'),
                DB::raw('NULL as money')
            )
            ->join('users as muser', 'member_gym.idmember', '=', 'muser.id')
            ->leftJoin('users as kasir', 'member_gym.iduser', '=', 'kasir.id')
            ->whereBetween('member_gym.created_at', [$start, $end]);
    
        // Query 3: data expense_money
        $queryExpense = DB::table('expense_money')
        ->select(
            'expense_money.iduser',
            DB::raw('NULL as namamember'),
            'u.name as namakasir',
            DB::raw('NULL as namatrainer'),
            'expense_money.created_at',
            DB::raw('NULL as price'),
            'expense_money.money as total_price',
            DB::raw("CONCAT('Pengeluaran hari ini: ', expense_money.description) as keterangan"),
            DB::raw('NULL as id'),
                DB::raw('NULL as description'),
                DB::raw('NULL as money')
        )
        ->leftJoin('users as u', 'expense_money.iduser', '=', 'u.id')
        ->whereBetween('expense_money.created_at', [$start, $end]);
    
        // Query 4: data income_money
        $queryIncome = DB::table('income_money')
            ->select(
                'income_money.iduser',
                DB::raw('NULL as namamember'),
                'u.name as namakasir',
                DB::raw('NULL as namatrainer'),
                'income_money.created_at',
                DB::raw('NULL as price'),
                'income_money.money as total_price',
                'income_money.description as keterangan',
                'income_money.id as id',
                'income_money.description',
                'income_money.money'
            )
            ->leftJoin('users as u', 'income_money.iduser', '=', 'u.id')
            ->whereBetween('income_money.created_at', [$start, $end]);

        // Query 5: data cashflow (DIBERI COLLATION)
        $queryCashflow = DB::table('cashflow')
        ->select(
            'cashflow.member_id as iduser',
            DB::raw('muser.name COLLATE utf8mb4_unicode_ci as namamember'),
            DB::raw('u.name COLLATE utf8mb4_unicode_ci as namakasir'),
            DB::raw('NULL as namatrainer'),
            'cashflow.date as created_at',
            DB::raw('NULL as price'),
            'cashflow.amount as total_price',

            // keterangan tanpa ID — murni tulisannya saja
            DB::raw("REGEXP_REPLACE(cashflow.description, 'ID [0-9]+', '') COLLATE utf8mb4_unicode_ci as keterangan"),

            // tetap ambil id cashflow
            'cashflow.id as id',

            // description juga tanpa tambahan apapun
            DB::raw("REGEXP_REPLACE(cashflow.description, 'ID [0-9]+', '') COLLATE utf8mb4_unicode_ci as description"),

            // nominal uangnya
            'cashflow.amount as money'
        )
        ->leftJoin('users as muser', 'cashflow.member_id', '=', 'muser.id')
        ->leftJoin('users as u', 'cashflow.created_by', '=', 'u.id')
        ->whereBetween('cashflow.date', [$start, $end]);
    
            // Gabungkan ketiganya
            $unionQuery = $queryTopup
            ->unionAll($queryMemberGym)
            ->unionAll($queryExpense)
            ->unionAll($queryIncome)
            ->unionAll($queryCashflow); // <-- sudah aman

        
        $rawData = DB::table(DB::raw("({$unionQuery->toSql()}) as combined"))
            ->mergeBindings($unionQuery) // penting agar binding dari union tetap ikut
            ->orderByDesc('created_at')
            ->get();
        
        $report = $rawData->map(function ($item) {
            $carbonDate = Carbon::parse($item->created_at);
            $item->tanggal_full = $carbonDate->format('d-m-Y');
            $item->nama_bulan = $carbonDate->translatedFormat('F');
        
            if (isset($item->keterangan)) {
                if (str_contains($item->keterangan, 'Pengeluaran')) {
                    $item->type = 'expense';
                } elseif (!empty($item->description)) {
                    $item->type = 'income';
                } else {
                    $item->type = 'other';
                }
            } else {
                $item->type = 'other';
            }
        
            return $item;
        });
            
            $totalTopup = $report->sum(function ($item) {
                $isTopUp = isset($item->keterangan) && str_contains($item->keterangan, 'Member Top Up');
                $isPendaftaran = isset($item->keterangan) && $item->keterangan === 'Pendaftaran Member';
                $isIncomeMoney = isset($item->keterangan) && !empty($item->description) && !str_contains($item->keterangan, 'Pengeluaran');
            
                if ($isTopUp) {
                    return (int) ($item->price ?? 0);
                } elseif ($isPendaftaran || $isIncomeMoney) {
                    return (int) ($item->total_price ?? 0);
                }
            
                return 0;
            });
            
            $totalExpense = $report->sum(function ($item) {
                $isPengeluaran = isset($item->keterangan) && str_contains($item->keterangan, 'Pengeluaran hari ini');
                return $isPengeluaran ? (int) ($item->total_price ?? 0) : 0;
            });
            
            $netIncome = $totalTopup - $totalExpense;
    
        return view('admin.report.reportmoney', [
            'report' => $report,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'totalTopupincome' => $totalTopup,
            'totalExpense' => $totalExpense,
            'selisih' => $netIncome,
        ]);
    }

    public function reportTrainer(Request $request)
    {
        $query = DB::table('top_upInformation')
            ->join('users', 'top_upInformation.idtrainer', '=', 'users.id')
            ->join('packet_trainer', 'packet_trainer.id', '=', 'top_upInformation.idtop_up')
            ->select(
                'users.name as trainer_name',
                DB::raw('SUM(packet_trainer.poin) as total_poin')
            )
            ->where('status', 1)
            ->groupBy('top_upInformation.idtrainer', 'users.name');
    
        // Jika ingin filter berdasarkan tanggal training:
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('top_upInformation.created_at', [$request->start_date, $request->end_date]);
        }
    
        $data = $query->get();
    
        return view('admin.report.reporttrainer', [
            'data' => $data,
        ]);
    }
    
    public function reportAllExpenseMoney(Request $request){
        
        $query = DB::table('expense_money')
        ->select('expense_money.id', 'expense_money.iduser', 'name', 'description', 'money', 'expense_money.created_at')
        ->join('users', 'users.id', '=', 'expense_money.iduser')
        ->orderBy('expense_money.created_at', 'desc');
        
        // Jika ingin filter berdasarkan tanggal training:
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_money.created_at', [$request->start_date, $request->end_date]);
        }
    
        $data = $query->get();
        
        return view('admin.report.reportexpense', [
            'data' => $data,
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'money' => 'required|integer',
        ]);
    
        Expense::create([
            'iduser' => auth()->id(),
            'description' => $request->description,
            'money' => $request->money,
        ]);
    
        return redirect()->back()->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
            'money' => 'required|integer',
        ]);
    
        $expense = Expense::findOrFail($id);
        $expense->update([
            'iduser' => $request->iduser,
            'description' => $request->description,
            'money' => $request->money,
        ]);
    
        return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
    
        return redirect()->back()->with('success', 'Data pengeluaran berhasil dihapus.');
    }
    
    public function storeIncome(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'money' => 'required|integer',
        ]);
    
        Income::create([
            'iduser' => auth()->id(),
            'description' => $request->description,
            'money' => $request->money,
        ]);
    
        return redirect()->back()->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }
    
    public function updateIncome(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
            'money' => 'required|integer',
        ]);
    
        $Income = Income::findOrFail($id);
        $Income->update([
            'iduser' => $request->iduser,
            'description' => $request->description,
            'money' => $request->money,
        ]);
    
        return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroyIncome($id)
    {
        $income = Income::findOrFail($id);
        $income->delete();
    
        return redirect()->back()->with('success', 'Data pemasukan berhasil dihapus.');
    }

}