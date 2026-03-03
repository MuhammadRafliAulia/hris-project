<?php

namespace App\Http\Controllers;

use App\Models\WarningLetter;
use App\Models\Department;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class WarningLetterController extends Controller
{
    /**
     * @return User
     */
    private function authUser()
    {
        /** @var User $user */
        $user = Auth::user();
        return $user;
    }

    public function index(Request $request)
    {
        // admin_prod cannot access list
        if ($this->authUser()->isAdminProd()) {
            return redirect()->route('warning-letters.create');
        }

        // superadmin sees ALL warning letters from all users
        $query = WarningLetter::query();

        // If current user is HR/internal or superadmin, allow filtering between ready/archive/all
        $user = $this->authUser();
        $viewMode = $request->get('view', 'all');
        if ($user->isInternalHR() || $user->isSuperAdmin()) {
            if ($viewMode === 'ready') {
                $query->where('status', 'pending_hr');
            } elseif ($viewMode === 'archive') {
                $query->where('status', 'approved');
            } else {
                $query->whereIn('status', ['pending_hr', 'approved']);
            }
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%$q%")
                    ->orWhere('departemen', 'like', "%$q%")
                    ->orWhere('jabatan', 'like', "%$q%");
            });
        }

        if ($request->filled('sp_level')) {
            $query->where('sp_level', $request->sp_level);
        }

        $letters = $query->with('approver')->orderBy('created_at', 'desc')->get();

        return view('warning-letters.index', compact('letters'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $isAdminProd = $this->authUser()->isAdminProd();
        return view('warning-letters.create', compact('departments', 'isAdminProd'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255',
            'jabatan' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'alasan' => 'required|string',
            'paragraf_kedua' => 'nullable|string',
            'tempat' => 'nullable|string|max:255',
            'proses' => 'nullable|string|max:255',
            'sp_level' => 'required|in:1,2,3',
            'tanggal_surat' => 'nullable|date',
            'nomor_surat' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();
        if (empty($validated['tanggal_surat'])) {
            $validated['tanggal_surat'] = now()->toDateString();
        }

        $letter = WarningLetter::create($validated);
        ActivityLog::log('create', 'warning_letter', 'Membuat Surat Peringatan SP' . $validated['sp_level'] . ' untuk ' . $validated['nama']);

        // admin_prod: redirect to sign form to fill 4 layers first
        if ($this->authUser()->isAdminProd()) {
            return redirect()->route('warning-letters.sign-form', $letter->id)
                ->with('success', 'Surat Peringatan berhasil dibuat. Silakan tanda tangan 4 layer terlebih dahulu.');
        }

        return redirect()->route('warning-letters.show-pdf', $letter->id)
            ->with('success', 'Surat Peringatan berhasil dibuat.');
    }

    public function edit(WarningLetter $warningLetter)
    {
        // admin_prod cannot edit
        if ($this->authUser()->isAdminProd()) {
            abort(403);
        }

        $departments = Department::orderBy('name')->get();

        return view('warning-letters.edit', compact('warningLetter', 'departments'));
    }

    public function update(Request $request, WarningLetter $warningLetter)
    {
        if ($this->authUser()->isAdminProd()) {
            abort(403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255',
            'jabatan' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'alasan' => 'required|string',
            'paragraf_kedua' => 'nullable|string',
            'tempat' => 'nullable|string|max:255',
            'proses' => 'nullable|string|max:255',
            'sp_level' => 'required|in:1,2,3',
            'tanggal_surat' => 'nullable|date',
            'nomor_surat' => 'nullable|string|max:255',
        ]);

        $warningLetter->update($validated);
        ActivityLog::log('update', 'warning_letter', 'Mengupdate Surat Peringatan: ' . $validated['nama']);

        return redirect()->route('warning-letters.index')
            ->with('success', 'Surat Peringatan berhasil diupdate.');
    }

    public function destroy(WarningLetter $warningLetter)
    {
        if ($this->authUser()->isAdminProd()) {
            abort(403);
        }

        $warningLetter->delete();
        ActivityLog::log('delete', 'warning_letter', 'Menghapus Surat Peringatan: ' . $warningLetter->nama);

        return redirect()->route('warning-letters.index')
            ->with('success', 'Surat Peringatan berhasil dihapus.');
    }

    public function showPdf(WarningLetter $warningLetter)
    {
        // PDF viewing for authenticated roles is allowed; preview/embed uses a separate signed route.

        // Generate and save the PDF into storage (storage/app/public/pdfsp/)
        $relative = $this->generateAndSavePdf($warningLetter);
        $fullPath = storage_path('app/public/' . $relative);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }

    /**
     * Generate PDF and save to storage/app/public/pdfsp/sp_{id}.pdf
     * Returns relative path under storage/app/public (e.g. 'pdfsp/sp_1.pdf')
     */
    private function generateAndSavePdf(WarningLetter $warningLetter)
    {
        $warningLetter->load('approver');
        $pdf = PDF::loadView('warning-letters.pdf', ['letter' => $warningLetter]);
        $pdf->setPaper('A4', 'portrait');

        $dir = storage_path('app/public/pdfsp');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = "sp_{$warningLetter->id}.pdf";
        $full = $dir . DIRECTORY_SEPARATOR . $filename;
        // save output
        file_put_contents($full, $pdf->output());

        return 'pdfsp/' . $filename;
    }

    public function downloadPdf(WarningLetter $warningLetter)
    {
        if ($this->authUser()->isAdminProd()) {
            abort(403);
        }

        $warningLetter->load('approver');
        $pdf = PDF::loadView('warning-letters.pdf', ['letter' => $warningLetter]);
        $pdf->setPaper('A4', 'portrait');

        $spLabel = $warningLetter->sp_label;
        $filename = "Surat_Peringatan_{$spLabel}_{$warningLetter->nama}.pdf";

        return $pdf->download($filename);
    }

    public function showSign(WarningLetter $warningLetter)
    {
        $user = $this->authUser();

        if ($warningLetter->isApproved()) {
            $redirect = $user->isAdminProd() ? 'warning-letters.create' : 'warning-letters.index';
            return redirect()->route($redirect)
                ->with('success', 'Surat ini sudah ditandatangani.');
        }

        // admin_prod: can only sign layer 1-4 when status is pending
        if ($user->isAdminProd()) {
            if (!$warningLetter->isPending()) {
                return redirect()->route('warning-letters.create')
                    ->with('success', 'Surat ini sudah ditandatangani oleh Anda. Menunggu tanda tangan HR.');
            }
            $signMode = 'admin_prod';
        } else {
            // Only allow HR / superadmin to sign HR layer when the 4 layers are already approved
            if ($warningLetter->isPendingHr()) {
                $signMode = 'hr_only';
            } else {
                // do not allow superadmin/internal_hr to sign all 5 at once anymore
                return redirect()->route('warning-letters.index')
                    ->with('error', 'Tanda tangan HR hanya muncul setelah 4 layer pertama disetujui.');
            }
        }

        return view('warning-letters.sign', compact('warningLetter', 'signMode'));
    }

    public function sign(Request $request, WarningLetter $warningLetter)
    {
        $user = $this->authUser();
        $signMode = $request->input('sign_mode', 'all');

        if ($warningLetter->isApproved()) {
            $redirect = $user->isAdminProd() ? 'warning-letters.create' : 'warning-letters.index';
            return redirect()->route($redirect)
                ->with('success', 'Surat ini sudah ditandatangani.');
        }

        // Phase 1: admin_prod signs layers 1-4, status becomes pending_hr
        if ($user->isAdminProd() || $signMode === 'admin_prod') {
            $request->validate([
                'signer_name_1' => 'required|string|max:255',
                'signer_jabatan_1' => 'required|string|max:255',
                'signature_1' => 'required|string',
                'signer_name_2' => 'required|string|max:255',
                'signer_jabatan_2' => 'required|string|max:255',
                'signature_2' => 'required|string',
                'signer_name_3' => 'required|string|max:255',
                'signer_jabatan_3' => 'required|string|max:255',
                'signature_3' => 'required|string',
                'signer_name_4' => 'required|string|max:255',
                'signer_jabatan_4' => 'required|string|max:255',
                'signature_4' => 'required|string',
            ]);

            $warningLetter->update([
                'signer_name_1' => $request->signer_name_1,
                'signer_jabatan_1' => $request->signer_jabatan_1,
                'signature_1' => $request->signature_1,
                'signer_name_2' => $request->signer_name_2,
                'signer_jabatan_2' => $request->signer_jabatan_2,
                'signature_2' => $request->signature_2,
                'signer_name_3' => $request->signer_name_3,
                'signer_jabatan_3' => $request->signer_jabatan_3,
                'signature_3' => $request->signature_3,
                'signer_name_4' => $request->signer_name_4,
                'signer_jabatan_4' => $request->signer_jabatan_4,
                'signature_4' => $request->signature_4,
                'status' => 'pending_hr',
            ]);
            ActivityLog::log('sign', 'warning_letter', 'Menandatangani 4 layer Surat Peringatan: ' . $warningLetter->nama);

            return redirect()->route('warning-letters.create')
                ->with('success', 'Surat Peringatan berhasil ditandatangani (4 layer). Menunggu tanda tangan HR.');
        }

        // Phase 2: superadmin/internal_hr signs HR layer 5 only (when status is pending_hr)
        if ($signMode === 'hr_only') {
            $request->validate([
                'signer_name_5' => 'required|string|max:255',
                'signer_jabatan_5' => 'required|string|max:255',
                'signature_5' => 'required|string',
            ]);

            $warningLetter->update([
                'signer_name_5' => $request->signer_name_5,
                'signer_jabatan_5' => $request->signer_jabatan_5,
                'signature_5' => $request->signature_5,
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            ActivityLog::log('sign', 'warning_letter', 'Menandatangani HR layer Surat Peringatan: ' . $warningLetter->nama);

            return redirect()->route('warning-letters.show-pdf', $warningLetter->id)
                ->with('success', 'Surat Peringatan berhasil ditandatangani oleh HR. Surat sudah approved.');
        }
        // Phase ALL: signing all 5 layers at once is no longer permitted for HR/superadmin
        // Keep legacy path only for non-HR (should not reach here normally)
        $request->validate([
            'signer_name_1' => 'required|string|max:255',
            'signer_jabatan_1' => 'required|string|max:255',
            'signature_1' => 'required|string',
            'signer_name_2' => 'required|string|max:255',
            'signer_jabatan_2' => 'required|string|max:255',
            'signature_2' => 'required|string',
            'signer_name_3' => 'required|string|max:255',
            'signer_jabatan_3' => 'required|string|max:255',
            'signature_3' => 'required|string',
            'signer_name_4' => 'required|string|max:255',
            'signer_jabatan_4' => 'required|string|max:255',
            'signature_4' => 'required|string',
            'signer_name_5' => 'required|string|max:255',
            'signer_jabatan_5' => 'required|string|max:255',
            'signature_5' => 'required|string',
        ]);

        $warningLetter->update([
            'signer_name_1' => $request->signer_name_1,
            'signer_jabatan_1' => $request->signer_jabatan_1,
            'signature_1' => $request->signature_1,
            'signer_name_2' => $request->signer_name_2,
            'signer_jabatan_2' => $request->signer_jabatan_2,
            'signature_2' => $request->signature_2,
            'signer_name_3' => $request->signer_name_3,
            'signer_jabatan_3' => $request->signer_jabatan_3,
            'signature_3' => $request->signature_3,
            'signer_name_4' => $request->signer_name_4,
            'signer_jabatan_4' => $request->signer_jabatan_4,
            'signature_4' => $request->signature_4,
            'signer_name_5' => $request->signer_name_5,
            'signer_jabatan_5' => $request->signer_jabatan_5,
            'signature_5' => $request->signature_5,
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        ActivityLog::log('sign', 'warning_letter', 'Menandatangani Surat Peringatan (5 layer): ' . $warningLetter->nama);

        return redirect()->route('warning-letters.show-pdf', $warningLetter->id)
            ->with('success', 'Surat Peringatan berhasil ditandatangani.');
    }

    public function exportExcel()
    {
        if ($this->authUser()->isAdminProd()) {
            abort(403);
        }

        // superadmin sees all
        $letters = WarningLetter::orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Surat Peringatan');

        // Headers
        $headers = ['No', 'Tanggal Surat', 'Nomor Surat', 'Nama', 'Jabatan', 'Departemen', 'SP Level', 'Alasan'];
        foreach ($headers as $col => $header) {
            $sheet->getCellByColumnAndRow($col + 1, 1)->setValue($header);
        }

        // Style header
        $headerRange = 'A1:H1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '003E6F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data rows
        foreach ($letters as $idx => $letter) {
            $row = $idx + 2;
            $sheet->getCellByColumnAndRow(1, $row)->setValue($idx + 1);
            $sheet->getCellByColumnAndRow(2, $row)->setValue($letter->tanggal_surat ? $letter->tanggal_surat->format('d/m/Y') : '-');
            $sheet->getCellByColumnAndRow(3, $row)->setValue($letter->nomor_surat ?: '-');
            $sheet->getCellByColumnAndRow(4, $row)->setValue($letter->nama);
            $sheet->getCellByColumnAndRow(5, $row)->setValue($letter->jabatan);
            $sheet->getCellByColumnAndRow(6, $row)->setValue($letter->departemen);
            $sheet->getCellByColumnAndRow(7, $row)->setValue($letter->sp_label);
            $sheet->getCellByColumnAndRow(8, $row)->setValue($letter->alasan);

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        // Auto width
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Surat_Peringatan_' . date('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'sp_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        ActivityLog::log('export', 'warning_letter', 'Export data surat peringatan ke Excel');

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function showImport()
    {
        if ($this->authUser()->isAdminProd()) {
            abort(403);
        }
        return view('warning-letters.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $imported = 0;
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            $nama = trim($row[0] ?? '');
            $jabatan = trim($row[1] ?? '');
            $departemen = trim($row[2] ?? '');
            $spLevel = (int) trim($row[3] ?? 1);
            $alasan = trim($row[4] ?? '');
            $nomorSurat = trim($row[5] ?? '');
            $tanggalSurat = trim($row[6] ?? '');

            if (empty($nama)) continue;

            if (!in_array($spLevel, [1, 2, 3])) {
                $spLevel = 1;
            }

            $data = [
                'user_id' => Auth::id(),
                'nama' => $nama,
                'jabatan' => $jabatan,
                'departemen' => $departemen,
                'sp_level' => $spLevel,
                'alasan' => $alasan,
                'nomor_surat' => $nomorSurat ?: null,
                'tanggal_surat' => $tanggalSurat ?: now()->toDateString(),
            ];

            WarningLetter::create($data);
            $imported++;
        }

        ActivityLog::log('import', 'warning_letter', 'Import data surat peringatan: ' . $imported . ' berhasil');

        return redirect()->route('warning-letters.index')
            ->with('success', "Berhasil import {$imported} data surat peringatan.");
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template SP');

        $headers = ['Nama', 'Jabatan', 'Departemen', 'SP Level (1/2/3)', 'Alasan', 'Nomor Surat', 'Tanggal Surat (YYYY-MM-DD)'];
        foreach ($headers as $col => $header) {
            $sheet->getCellByColumnAndRow($col + 1, 1)->setValue($header);
        }

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '003E6F']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Example row
        $sheet->getCellByColumnAndRow(1, 2)->setValue('John Doe');
        $sheet->getCellByColumnAndRow(2, 2)->setValue('Operator');
        $sheet->getCellByColumnAndRow(3, 2)->setValue('Produksi');
        $sheet->getCellByColumnAndRow(4, 2)->setValue('1');
        $sheet->getCellByColumnAndRow(5, 2)->setValue('Terlambat masuk kerja berulang kali');
        $sheet->getCellByColumnAndRow(6, 2)->setValue('SP/001/II/2026');
        $sheet->getCellByColumnAndRow(7, 2)->setValue('2026-02-09');

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'tmpl_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, 'Template_Surat_Peringatan.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Progress view for admin_dept: shows SP submissions and layer statuses
     */
    public function progress(Request $request)
    {
        $user = $this->authUser();
        if (! $user->isAdminProd()) {
            abort(403);
        }

        // Admin_dept should see SPs for their department if user's department info exists.
        // Fallback: show all if department not defined on user.
        $query = WarningLetter::query();
        if (method_exists($user, 'department') || isset($user->department)) {
            // attempt to filter by department name if user has department attribute
            if (! empty($user->department)) {
                $query->where('departemen', $user->department);
            }
        }

        $letters = $query->orderBy('created_at', 'desc')->get();

        return view('warning-letters.progress', compact('letters'));
    }

    /**
     * Bulk delete selected warning letters from progress view.
     * Admin Prod can delete letters only when not all 4 layers are signed.
     */
    public function bulkDelete(Request $request)
    {
        $user = $this->authUser();
        if (! $user->isAdminProd()) {
            abort(403);
        }

        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return redirect()->route('warning-letters.progress')->with('error', 'Tidak ada data yang dipilih.');
        }

        $letters = WarningLetter::whereIn('id', $ids)->get();
        $deleted = 0;
        $skipped = [];

        foreach ($letters as $letter) {
            $all4 = true;
            for ($i = 1; $i <= 4; $i++) {
                $s = $letter->{'signature_' . $i};
                if (empty($s) || (is_string($s) && strpos($s, 'rejected') !== false)) {
                    $all4 = false;
                    break;
                }
            }

            if ($all4) {
                $skipped[] = $letter->id;
                continue;
            }

            $letter->delete();
            ActivityLog::log('delete', 'warning_letter', 'Menghapus Surat Peringatan (bulk) : ' . $letter->nama);
            $deleted++;
        }

        $msg = '';
        if ($deleted) {
            $msg .= "Berhasil menghapus {$deleted} data. ";
        }
        if (!empty($skipped)) {
            $msg .= 'Beberapa data tidak dihapus karena sudah lengkap 4 tanda tangan.';
        }

        return redirect()->route('warning-letters.progress')->with('success', trim($msg));
    }

    /**
     * Show approval form via signed public link for a specific layer (1-4)
     */
    public function showApprovalForm(Request $request, WarningLetter $warningLetter, $layer)
    {
        $layer = (int) $layer;
        if ($layer < 1 || $layer > 4) {
            abort(404);
        }
        // Manually validate signature to provide diagnostics when invalid
        $isValid = \Illuminate\Support\Facades\URL::hasValidSignature($request);

        // If already approved fully, show simple message
        if ($warningLetter->isApproved()) {
            return view('warning-letters.approval_result', [
                'warningLetter' => $warningLetter,
                'layer' => $layer,
                'action' => 'already_approved',
            ]);
        }

        if (! $isValid) {
            // Build the originally-generated link for comparison
            $generated = \Illuminate\Support\Facades\URL::temporarySignedRoute('warning-letters.approval', now()->addDays(14), ['warning_letter' => $warningLetter->id, 'layer' => $layer]);

            // Collect request / environment info
            $info = [
                'request_full_url' => $request->fullUrl(),
                'request_scheme_host' => $request->getSchemeAndHttpHost(),
                'generated_link' => $generated,
                'app_url' => config('app.url'),
                'headers' => [
                    'host' => $request->header('host'),
                    'x_forwarded_proto' => $request->header('x-forwarded-proto'),
                    'x_forwarded_host' => $request->header('x-forwarded-host'),
                ],
                'is_valid_signature' => $isValid,
            ];

            // Log for server-side inspection
            ActivityLog::log('debug', 'warning_letter', 'Invalid signature detected for approval link: ' . $warningLetter->id);

            return view('warning-letters.approval_debug', compact('warningLetter', 'layer', 'info'));
        }

        // Ensure PDF saved to storage and provide its storage URL for preview iframe
        $relative = $this->generateAndSavePdf($warningLetter);
        $previewLink = asset('storage/' . $relative);

        return view('warning-letters.approval', compact('warningLetter', 'layer', 'previewLink'));
    }

    /**
     * Public signed preview for embedding the PDF in approval page.
     */
    public function showPreviewPdf(Request $request, WarningLetter $warningLetter)
    {
        // must be accessed via a valid signed URL
        if (!\Illuminate\Support\Facades\URL::hasValidSignature($request)) {
            abort(403);
        }

        $warningLetter->load('approver');
        $pdf = PDF::loadView('warning-letters.pdf', ['letter' => $warningLetter]);
        $pdf->setPaper('A4', 'portrait');

        $spLabel = $warningLetter->sp_label;
        $filename = "Surat_Peringatan_{$spLabel}_{$warningLetter->nama}.pdf";

        $response = $pdf->stream($filename);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
        return $response;
    }

    /**
     * Handle approval/rejection submitted from signed link
     */
    public function handleApproval(Request $request, WarningLetter $warningLetter, $layer)
    {
        $layer = (int) $layer;
        if ($layer < 1 || $layer > 4) {
            abort(404);
        }

        $validated = $request->validate([
            'nama' => 'required_if:action,approve|string|max:255',
            'jabatan' => 'required_if:action,approve|string|max:255',
            'nik' => 'nullable|string|max:255',
            'action' => 'required|in:approve,reject',
        ]);

        $action = $validated['action'];

        if ($action === 'approve') {
            $request->validate([
                'signature' => 'required|string',
            ]);

            $signatureValue = $request->input('signature');

            $warningLetter->update([
                'nik' => $validated['nik'] ?: $warningLetter->nik,
                'signer_name_' . $layer => $validated['nama'],
                'signer_jabatan_' . $layer => $validated['jabatan'],
                'signature_' . $layer => $signatureValue,
            ]);

            // If all 4 layers now have signer_name set, move to pending_hr
            $warningLetter->refresh();
            $allFilled = true;
            for ($i = 1; $i <= 4; $i++) {
                if (empty($warningLetter->{'signer_name_' . $i})) {
                    $allFilled = false;
                    break;
                }
            }
            if ($allFilled) {
                $warningLetter->status = 'pending_hr';
                $warningLetter->save();
            }

            ActivityLog::log('approve_link', 'warning_letter', "Approve via link layer {$layer} untuk {$warningLetter->nama}");

            return view('warning-letters.approval_result', [
                'warningLetter' => $warningLetter,
                'layer' => $layer,
                'action' => 'approved',
            ]);
        }

        // reject
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);
        $reason = trim($request->input('reason', ''));
        $ts = now();
        $nameForSig = $request->input('nama') ?: 'Via Link';
        $jabForSig = $request->input('jabatan') ?: 'Via Link';
        $sigValue = 'rejected_via_link:' . $nameForSig;
        if ($reason !== '') {
            // store as: rejected_via_link:NAME|REASON @ TIMESTAMP
            $sigValue .= '|' . str_replace(["\r", "\n"], [' ', ' '], $reason) . ' @ ' . $ts;
        } else {
            $sigValue .= ' @ ' . $ts;
        }

        $warningLetter->update([
            'signature_' . $layer => $sigValue,
            'signer_name_' . $layer => $nameForSig,
            'signer_jabatan_' . $layer => $jabForSig,
            'status' => 'rejected',
        ]);

        ActivityLog::log('reject_link', 'warning_letter', "Rejected via link layer {$layer} untuk {$warningLetter->nama}");

        return view('warning-letters.approval_result', [
            'warningLetter' => $warningLetter,
            'layer' => $layer,
            'action' => 'rejected',
        ]);
    }
}
