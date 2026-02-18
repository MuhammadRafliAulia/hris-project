<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeController extends Controller
{
    // ...existing code...

    public function show(Employee $employee)
    {
        // Superadmin and Internal HR can view any employee; others only their own
        if (! (auth()->user()->isSuperAdmin() || auth()->user()->isInternalHR() || $employee->user_id === Auth::id()) ) {
            abort(403);
        }
        return view('employees.show', compact('employee'));
    }
    public function index(Request $request)
    {
        // Superadmin and Internal HR see all employees; others see only their own
        if (auth()->user()->isSuperAdmin() || auth()->user()->isInternalHR()) {
            $query = Employee::query();
        } else {
            $query = Employee::where('user_id', Auth::id());
        }

        // Global search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('nama', 'like', "%$q%")
                    ->orWhere('nik', 'like', "%$q%")
                    ->orWhere('jabatan', 'like', "%$q%")
                    ->orWhere('seksi', 'like', "%$q%")
                    ->orWhere('no_telpon', 'like', "%$q%")
                    ->orWhere('no_wa', 'like', "%$q%")
                    ->orWhere('ktp', 'like', "%$q%")
                    ->orWhere('alamat_email', 'like', "%$q%");
            });
        }

        // Column-specific filters
        $textFilters = [
            'dept', 'gol', 'jabatan', 'seksi', 'gol_darah',
            'status_karyawan', 'status_aktif', 'status_perkawinan',
            'pendidikan', 'agama', 'asal_kota', 'status_pph',
            'status_tempat_tinggal',
        ];

        foreach ($textFilters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        $employees = $query->orderBy('created_at', 'desc')->get();
        $departments = \App\Models\Department::orderBy('name')->get();

        // Get distinct values for filter dropdowns
        $userId = (auth()->user()->isSuperAdmin() || auth()->user()->isInternalHR()) ? null : Auth::id();
        $filterOptions = [];
        foreach ($textFilters as $filter) {
            $fquery = Employee::query();
            if ($userId) $fquery->where('user_id', $userId);
            $filterOptions[$filter] = $fquery->whereNotNull($filter)->where($filter, '!=', '')
                ->distinct()->orderBy($filter)->pluck($filter)->toArray();
        }

        return view('employees.index', compact('employees', 'departments', 'filterOptions'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'nullable|string|max:255',
            'nama' => 'required|string|max:255',
            'gol' => 'nullable|string|max:255',
            'dept' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'seksi' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'gol_darah' => 'nullable|string|max:10',
            'alamat_domisili' => 'nullable|string|max:255',
            'status_tempat_tinggal' => 'nullable|string|max:255',
            'no_telpon' => 'nullable|string|max:255',
            'no_wa' => 'nullable|string|max:255',
            'kontak_darurat' => 'nullable|string|max:255',
            'tgl_masuk' => 'nullable|date',
            'bulan_masuk' => 'nullable|string|max:20',
            'tahun_masuk' => 'nullable|string|max:10',
            'status_karyawan' => 'nullable|string|max:50',
            'status_pph' => 'nullable|string|max:50',
            'end_pkwt_1' => 'nullable|date',
            'end_pkwt_2' => 'nullable|date',
            'tgl_pengangkatan' => 'nullable|date',
            'tgl_sekarang' => 'nullable|date',
            'masa_kerja' => 'nullable|string|max:100',
            'usia' => 'nullable|string|max:10',
            'npwp' => 'nullable|string|max:50',
            'jamsostek' => 'nullable|string|max:50',
            'no_kpj_bpjstk' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'ktp' => 'nullable|string|max:50',
            'alamat_email' => 'nullable|email|max:255',
            'status_perkawinan' => 'nullable|string|max:50',
            'status_perkawinan_excel' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
            'asal_sekolah' => 'nullable|string|max:100',
            'ar' => 'nullable|string|max:50',
            'end' => 'nullable|date',
            'bulan_end' => 'nullable|string|max:20',
            'status_aktif' => 'nullable|string|max:20',
            'alamat_npwp' => 'nullable|string|max:255',
            'alamat_asal' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:50',
            'asal_kota' => 'nullable|string|max:100',
            'alamat_domisili_kecamatan' => 'nullable|string|max:100',
            'area_asal_kecamatan' => 'nullable|string|max:100',
            'area_asal' => 'nullable|string|max:100',
        ]);

        // Associate created record with creator for auditing; shared access controlled by role
        Employee::create(array_merge($validated, ['user_id' => Auth::id()]));
        ActivityLog::log('create', 'employee', 'Menambahkan karyawan: ' . $validated['nama']);
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        if (! (auth()->user()->isSuperAdmin() || auth()->user()->isInternalHR() || $employee->user_id === Auth::id()) ) {
            abort(403);
        }
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        if (! (auth()->user()->isSuperAdmin() || auth()->user()->isInternalHR() || $employee->user_id === Auth::id()) ) {
            abort(403);
        }

        $validated = $request->validate([
            'nik' => 'nullable|string|max:255',
            'nama' => 'required|string|max:255',
            'gol' => 'nullable|string|max:255',
            'dept' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'seksi' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'gol_darah' => 'nullable|string|max:10',
            'alamat_domisili' => 'nullable|string|max:255',
            'status_tempat_tinggal' => 'nullable|string|max:255',
            'no_telpon' => 'nullable|string|max:255',
            'no_wa' => 'nullable|string|max:255',
            'kontak_darurat' => 'nullable|string|max:255',
            'tgl_masuk' => 'nullable|date',
            'bulan_masuk' => 'nullable|string|max:20',
            'tahun_masuk' => 'nullable|string|max:10',
            'status_karyawan' => 'nullable|string|max:50',
            'status_pph' => 'nullable|string|max:50',
            'end_pkwt_1' => 'nullable|date',
            'end_pkwt_2' => 'nullable|date',
            'tgl_pengangkatan' => 'nullable|date',
            'tgl_sekarang' => 'nullable|date',
            'masa_kerja' => 'nullable|string|max:100',
            'usia' => 'nullable|string|max:10',
            'npwp' => 'nullable|string|max:50',
            'jamsostek' => 'nullable|string|max:50',
            'no_kpj_bpjstk' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'ktp' => 'nullable|string|max:50',
            'alamat_email' => 'nullable|email|max:255',
            'status_perkawinan' => 'nullable|string|max:50',
            'status_perkawinan_excel' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
            'asal_sekolah' => 'nullable|string|max:100',
            'ar' => 'nullable|string|max:50',
            'end' => 'nullable|date',
            'bulan_end' => 'nullable|string|max:20',
            'status_aktif' => 'nullable|string|max:20',
            'alamat_npwp' => 'nullable|string|max:255',
            'alamat_asal' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:50',
            'asal_kota' => 'nullable|string|max:100',
            'alamat_domisili_kecamatan' => 'nullable|string|max:100',
            'area_asal_kecamatan' => 'nullable|string|max:100',
            'area_asal' => 'nullable|string|max:100',
        ]);

        $employee->update($validated);
        ActivityLog::log('update', 'employee', 'Mengupdate karyawan: ' . $validated['nama']);
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        if (! (auth()->user()->isSuperAdmin() || auth()->user()->isInternalHR() || $employee->user_id === Auth::id()) ) {
            abort(403);
        }

        $employee->delete();
        ActivityLog::log('delete', 'employee', 'Menghapus karyawan: ' . $employee->nama);
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function showImport()
    {
        return view('employees.import');
    }


    public function downloadTemplate()
    {
        $headers = [
            'NIK', 'NAMA', 'GOL', 'DEPT', 'JABATAN', 'SEKSI', 'TEMPAT LAHIR', 'TGL. LAHIR', 'GOL. DARAH',
            'ALAMAT DOMISILI', 'STATUS TEMPAT TINGGAL', 'NO TELPON', 'NO. WA', 'PIHAK YANG DAPAT DIHUBUNGI SAAT DARURAT',
            'TGL. MASUK', 'BULAN MASUK', 'TAHUN MASUK', 'STATUS', 'STATUS PPH', 'END PKWT 1', 'END PKWT 2',
            'TGL. PENGANGKATAN', 'TGL SEKARANG', 'MASA KERJA DIA', 'USIA', 'NPWP', 'JAMSOSTEK', 'NO KPJ BPJSTK',
            'NO.KK', 'KTP', 'ALAMAT EMAIL', 'STATUS PERKAWINAN', 'STATUS PERKAWINAN (EXCEL)', 'PENDIDIKAN',
            'ASAL SEKOLAH', 'A.R.', 'END', 'BULAN END', 'STATUS (AKTIF/TIDAK AKTIF)', 'ALAMAT NPWP', 'ALAMAT ASAL',
            'AGAMA', 'ASAL KOTA', 'ALAMAT DOMISILI KECAMATAN', 'AREA ASAL KECAMATAN', 'AREA ASAL'
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Karyawan');

        // Write headers
        foreach ($headers as $col => $header) {
            $sheet->getCellByColumnAndRow($col + 1, 1)->setValue($header);
        }

        // Style header: blue background, white bold font, border
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = 'A1:' . $lastCol . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '003E6F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        // Auto-size columns
        foreach (range(1, count($headers)) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Sample data row
        $sampleData = [
            '12345678', 'Budi Santoso', 'III', 'HRD', 'Manager', 'SDM', 'Jakarta', '1980-01-01', 'O',
            'Jakarta Selatan', 'Milik Sendiri', '08123456789', '08123456789', 'Ibu Budi',
            '2000-01-01', '01', '2000', 'tetap', 'PTKP', '2022-01-01', '2023-01-01',
            '2021-01-01', '2026-02-09', '22 TAHUN 5 BULAN', '45', '1234567890', '1234567890', '1234567890',
            '1234567890', '1234567890', 'budi@email.com', 'Kawin', 'Kawin', 'S1',
            'UI', 'AR1', '2025-12-31', '12', 'AKTIF', 'Alamat NPWP', 'Alamat Asal',
            'Islam', 'Jakarta', 'Setiabudi', 'Setiabudi', 'Jakarta'
        ];
        foreach ($sampleData as $col => $val) {
            $sheet->getCellByColumnAndRow($col + 1, 2)->setValue($val);
        }

        $fileName = 'template_karyawan.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'tpl');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function exportExcel()
    {
        $headers = [
            'NIK', 'NAMA', 'GOL', 'DEPT', 'JABATAN', 'SEKSI', 'TEMPAT LAHIR', 'TGL. LAHIR', 'GOL. DARAH',
            'ALAMAT DOMISILI', 'STATUS TEMPAT TINGGAL', 'NO TELPON', 'NO. WA', 'PIHAK YANG DAPAT DIHUBUNGI SAAT DARURAT',
            'TGL. MASUK', 'BULAN MASUK', 'TAHUN MASUK', 'STATUS', 'STATUS PPH', 'END PKWT 1', 'END PKWT 2',
            'TGL. PENGANGKATAN', 'TGL SEKARANG', 'MASA KERJA DIA', 'USIA', 'NPWP', 'JAMSOSTEK', 'NO KPJ BPJSTK',
            'NO.KK', 'KTP', 'ALAMAT EMAIL', 'STATUS PERKAWINAN', 'STATUS PERKAWINAN (EXCEL)', 'PENDIDIKAN',
            'ASAL SEKOLAH', 'A.R.', 'END', 'BULAN END', 'STATUS (AKTIF/TIDAK AKTIF)', 'ALAMAT NPWP', 'ALAMAT ASAL',
            'AGAMA', 'ASAL KOTA', 'ALAMAT DOMISILI KECAMATAN', 'AREA ASAL KECAMATAN', 'AREA ASAL'
        ];

        $fields = [
            'nik', 'nama', 'gol', 'dept', 'jabatan', 'seksi', 'tempat_lahir', 'tgl_lahir', 'gol_darah',
            'alamat_domisili', 'status_tempat_tinggal', 'no_telpon', 'no_wa', 'kontak_darurat',
            'tgl_masuk', 'bulan_masuk', 'tahun_masuk', 'status_karyawan', 'status_pph', 'end_pkwt_1', 'end_pkwt_2',
            'tgl_pengangkatan', 'tgl_sekarang', 'masa_kerja', 'usia', 'npwp', 'jamsostek', 'no_kpj_bpjstk',
            'no_kk', 'ktp', 'alamat_email', 'status_perkawinan', 'status_perkawinan_excel', 'pendidikan',
            'asal_sekolah', 'ar', 'end', 'bulan_end', 'status_aktif', 'alamat_npwp', 'alamat_asal',
            'agama', 'asal_kota', 'alamat_domisili_kecamatan', 'area_asal_kecamatan', 'area_asal'
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Karyawan');

        // Write headers
        foreach ($headers as $col => $header) {
            $sheet->getCellByColumnAndRow($col + 1, 1)->setValue($header);
        }

        // Style header: blue background, white bold font, border
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = 'A1:' . $lastCol . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '003E6F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        // Write data
        $employees = Employee::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        $row = 2;
        foreach ($employees as $employee) {
            foreach ($fields as $col => $field) {
                $sheet->getCellByColumnAndRow($col + 1, $row)->setValue($employee->$field);
            }
            $row++;
        }

        // Style data rows border
        if ($row > 2) {
            $dataRange = 'A2:' . $lastCol . ($row - 1);
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
        }

        // Auto-size columns
        foreach (range(1, count($headers)) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $fileName = 'karyawan_' . date('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'exp');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        ActivityLog::log('export', 'employee', 'Export data karyawan ke Excel');

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            return redirect()->route('employees.index')->with('success', 'File kosong atau hanya header.');
        }

        $fields = [
            'nik', 'nama', 'gol', 'dept', 'jabatan', 'seksi', 'tempat_lahir', 'tgl_lahir', 'gol_darah',
            'alamat_domisili', 'status_tempat_tinggal', 'no_telpon', 'no_wa', 'kontak_darurat',
            'tgl_masuk', 'bulan_masuk', 'tahun_masuk', 'status_karyawan', 'status_pph', 'end_pkwt_1', 'end_pkwt_2',
            'tgl_pengangkatan', 'tgl_sekarang', 'masa_kerja', 'usia', 'npwp', 'jamsostek', 'no_kpj_bpjstk',
            'no_kk', 'ktp', 'alamat_email', 'status_perkawinan', 'status_perkawinan_excel', 'pendidikan',
            'asal_sekolah', 'ar', 'end', 'bulan_end', 'status_aktif', 'alamat_npwp', 'alamat_asal',
            'agama', 'asal_kota', 'alamat_domisili_kecamatan', 'area_asal_kecamatan', 'area_asal'
        ];

        $imported = 0;
        $errors = [];

        // Skip header row (index 0)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            // Skip empty rows
            $allEmpty = true;
            foreach ($row as $cell) {
                if (!empty(trim((string)$cell))) { $allEmpty = false; break; }
            }
            if ($allEmpty) continue;

            $data = ['user_id' => Auth::id()];
            foreach ($fields as $colIdx => $field) {
                $data[$field] = isset($row[$colIdx]) ? trim((string)$row[$colIdx]) : null;
            }

            // Nama wajib diisi
            if (empty($data['nama'])) {
                $errors[] = "Baris " . ($i + 1) . ": Nama kosong";
                continue;
            }

            try {
                Employee::create($data);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
            }
        }

        $message = "$imported karyawan berhasil diimport.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " baris error: " . implode(" | ", array_slice($errors, 0, 3));
        }

        ActivityLog::log('import', 'employee', 'Import data karyawan: ' . $imported . ' berhasil');

        return redirect()->route('employees.index')->with('success', $message);
    }
}
