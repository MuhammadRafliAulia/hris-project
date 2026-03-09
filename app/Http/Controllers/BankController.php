<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Question;
use App\Models\SubTest;
use App\Models\ParticipantResponse;
use App\Models\ApplicantCredential;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class BankController extends Controller
{

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (optional($user)->isSuperAdmin() || optional($user)->isRecruitmentTeam()) {
            // Superadmin & recruitment team share the same bank soal data
            $sharedUserIds = \App\Models\User::whereIn('role', ['superadmin', 'recruitmentteam'])->pluck('id');
            $banks = Bank::whereIn('user_id', $sharedUserIds)->get();
        } else {
            $banks = Bank::where('user_id', $user->id)->get();
        }
        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        if ($request->has('bank_id') && $request->filled('bank_id') && $request->has('question')) {
            return $this->storeQuestion($request);
        }
        if ($request->has('bank_id') && $request->filled('bank_id') && $request->has('sub_test_title')) {
            return $this->storeSubTest($request);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'target' => 'nullable|in:karyawan,calon_karyawan',
        ]);

        // default to 'karyawan' when not provided
        if (empty($validated['target'])) $validated['target'] = 'karyawan';

        $bank = Bank::create(array_merge($validated, ['user_id' => Auth::id()]));
        ActivityLog::log('create', 'bank', 'Membuat bank soal: ' . $validated['title']);
        return redirect()->route('banks.edit', $bank)->with('success', 'Bank soal berhasil dibuat.');
    }

    private function storeQuestion(Request $request)
    {
        $bank = Bank::findOrFail($request->bank_id);
        $this->authorize('update', $bank);

        $type = $request->input('type', 'multiple_choice');

        // No debug logs (production-ready): prefer posted values or fallback fields if present

        // Trim text inputs to avoid whitespace-only values and merge back to request.
        // Also accept fallback values from client-side `_posted_*` hidden fields if present.
        $trimFields = ['question','option_a','option_b','option_c','option_d','option_e','option_f','correct_answer','correct_answer_text'];
        $trimmed = [];
        foreach ($trimFields as $f) {
            $val = $request->input($f);
            // fallback to client-supplied `_posted_` prefixed field when original is empty
            if (($val === null || $val === '') && $request->has('_posted_' . $f)) {
                $val = $request->input('_posted_' . $f);
            }
            if (is_string($val)) $val = trim($val);
            $trimmed[$f] = $val;
        }
        $request->merge($trimmed);

        // Quick pre-check to provide clearer errors when required MC fields missing
        if ($type === 'multiple_choice') {
            $requiredFields = ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'];
            $missing = [];
            foreach ($requiredFields as $f) {
                $v = $request->input($f);
                if ($v === null || $v === '') $missing[] = $f;
            }
            if (!empty($missing)) {
                $messages = $this->uploadValidationMessages();
                $errors = [];
                foreach ($missing as $m) {
                    $key = $m . '.required';
                    $errors[$m] = $messages[$key] ?? ($messages['required'] ?? 'Wajib diisi');
                }
                return back()->withInput()->withErrors($errors);
            }
        }

        $baseRules = [
            'question' => 'required|string|max:1000',
            'type' => 'required|in:text,multiple_choice,narrative,survey',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'audio' => 'nullable|mimes:mp3,wav,ogg|max:5120',
            'option_a_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_b_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_c_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_d_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_e_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_f_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];

        if ($type === 'narrative') {
            $validated = $request->validate($baseRules, $this->uploadValidationMessages());
            $validated['option_a'] = null;
            $validated['option_b'] = null;
            $validated['option_c'] = null;
            $validated['option_d'] = null;
            $validated['option_e'] = null;
            $validated['option_f'] = null;
            $validated['option_count'] = null;
            $validated['correct_answer'] = null;
            $validated['correct_answer_text'] = null;
        } elseif ($type === 'text') {
            $validated = $request->validate(array_merge($baseRules, [
                'correct_answer_text' => 'required|string|max:500',
            ]), $this->uploadValidationMessages());
            $validated['option_a'] = null;
            $validated['option_b'] = null;
            $validated['option_c'] = null;
            $validated['option_d'] = null;
            $validated['option_e'] = null;
            $validated['option_f'] = null;
            $validated['option_count'] = null;
            $validated['correct_answer'] = null;
        } elseif ($type === 'survey') {
            $optCount = (int) $request->input('option_count', 2);
            if ($optCount < 2) $optCount = 2;
            if ($optCount > 6) $optCount = 6;

            $surveyRules = ['option_count' => 'required|integer|min:2|max:6'];
            $labels = ['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d', 'E' => 'option_e', 'F' => 'option_f'];
            $i = 0;
            foreach ($labels as $letter => $field) {
                $i++;
                if ($i <= $optCount) {
                    $surveyRules[$field] = 'required|string|max:500';
                }
            }

            $validated = $request->validate(array_merge($baseRules, $surveyRules), $this->uploadValidationMessages());

            // Null out unused options
            $i = 0;
            foreach ($labels as $letter => $field) {
                $i++;
                if ($i > $optCount) {
                    $validated[$field] = null;
                }
            }
            $validated['option_count'] = $optCount;
            $validated['correct_answer'] = null;
            $validated['correct_answer_text'] = null;
        } else {
            $validated = $request->validate(array_merge($baseRules, [
                'option_a' => 'required|string|max:500',
                'option_b' => 'required|string|max:500',
                'option_c' => 'required|string|max:500',
                'option_d' => 'required|string|max:500',
                'option_e' => 'nullable|string|max:500',
                'option_f' => 'nullable|string|max:500',
                'correct_answer' => 'required|in:A,B,C,D,E,F',
            ]), $this->uploadValidationMessages());
            $validated['option_count'] = null;
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // double-check file size to avoid DB packet issues
            if ($file->getSize() > 5 * 1024 * 1024) {
                return back()->withInput()->with('error', 'gagal upload file lebih dari 5 mb');
            }
            $validated['image'] = $file->store('questions/images', 'public');
            $validated['image_data'] = file_get_contents($file->getRealPath());
            $validated['image_mime'] = $file->getClientMimeType();
        }
        // audio
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            if ($audio->getSize() > 5 * 1024 * 1024) {
                return back()->withInput()->with('error', 'gagal upload file lebih dari 5 mb');
            }
            $validated['audio'] = $audio->store('questions/audio', 'public');
        }

        // Option images (for multiple_choice)
        foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $letter) {
            $fieldName = 'option_' . $letter . '_image';
            if ($request->hasFile($fieldName)) {
                $f = $request->file($fieldName);
                if ($f->getSize() > 5 * 1024 * 1024) {
                    return back()->withInput()->with('error', 'gagal upload file lebih dari 5 mb');
                }
                $validated[$fieldName] = $f->store('questions/option-images', 'public');
            }
        }

        $order = Question::where('sub_test_id', $request->sub_test_id)->max('order') ?? -1;
        $validated['order'] = $order + 1;
        $validated['bank_id'] = $bank->id;
        $validated['sub_test_id'] = $request->sub_test_id;
        $validated['is_example'] = $request->boolean('is_example');

        Question::create($validated);
        ActivityLog::log('create', 'question', 'Menambahkan soal ke bank: ' . $bank->title);

        return redirect()->route('sub-tests.edit', $request->sub_test_id)->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Request $request, Bank $bank)
    {
        $this->authorize('update', $bank);
        $subTests = $bank->subTests()->withCount(['questions', 'exampleQuestions'])->get();
        // Load applicant credentials for calon_karyawan banks
        $applicantCredentials = [];
        if ($bank->target === 'calon_karyawan') {
            // support sorting via query params ?sort=column&dir=asc|desc
            $allowed = ['username', 'used', 'created_at'];
            $sort = $request->query('sort', 'created_at');
            $dir = strtolower($request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            if (!in_array($sort, $allowed)) {
                $sort = 'created_at';
            }

            $applicantCredentials = $bank->applicantCredentials()->orderBy($sort, $dir)->get()->map(function($c) {
                    try { 
                    $plain = Crypt::decryptString($c->password_encrypted);
                } catch (\Exception $e) {
                    $plain = null;
                }
                $c->plain_password = $plain;
                return $c;
            });
        }

        // pass current sort to view for toggling links
        return view('banks.edit', compact('bank', 'subTests', 'applicantCredentials'))
            ->with('currentSort', $request->query('sort'))
            ->with('currentDir', $request->query('dir'));
    }

    public function generateApplicantCredential(Request $request, Bank $bank)
    {
        $this->authorize('update', $bank);
        if ($bank->target !== 'calon_karyawan') {
            return back()->with('error', 'Fitur hanya untuk bank dengan target calon karyawan.');
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $username = $validated['email'];
        $password = \Illuminate\Support\Str::random(6);
        $encrypted = Crypt::encryptString($password);

        $cred = ApplicantCredential::create([
            'bank_id' => $bank->id,
            'username' => $username,
            'password_encrypted' => $encrypted,
            'used' => false,
        ]);

        ActivityLog::log('create', 'credential', 'Membuat kredensial calon karyawan untuk bank: ' . $bank->title . ' ('.$username.')');

        // Return back with the generated password visible once
        return redirect()->route('banks.edit', $bank)->with('generated', ['username' => $username, 'password' => $password]);
    }

    public function deleteApplicantCredential(Bank $bank, ApplicantCredential $credential)
    {
        $this->authorize('update', $bank);
        if ($credential->bank_id !== $bank->id) abort(404);
        ActivityLog::log('delete', 'credential', 'Menghapus kredensial: ' . $credential->username . ' dari bank: ' . $bank->title);
        $credential->delete();
        return back()->with('success', 'Kredensial dihapus.');
    }

    // Download Excel template (single header: email)
    public function downloadCredentialTemplate(Bank $bank)
    {
        $this->authorize('update', $bank);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'email');
        $sheet->setCellValue('A2', 'example@example.com');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'credentials_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

    // Export existing credentials (with decrypted plain password) as Excel
    public function exportCredentials(Bank $bank)
    {
        $this->authorize('update', $bank);

        $creds = $bank->applicantCredentials()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'username');
        $sheet->setCellValue('B1', 'password');

        $row = 2;
        foreach ($creds as $c) {
            $plain = null;
            try { $plain = Crypt::decryptString($c->password_encrypted); } catch (\Exception $e) { $plain = ''; }
            $sheet->setCellValue('A' . $row, $c->username);
            $sheet->setCellValue('B' . $row, $plain);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'credentials_export_' . $bank->id . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

    // Import credentials from uploaded Excel with single column 'email'
    public function importCredentials(Request $request, Bank $bank)
    {
        $this->authorize('update', $bank);
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $path = $request->file('file')->getPathname();

        // Read using PhpSpreadsheet
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $generated = [];
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        // If user checked to use a single password for all imported emails,
        // create one random password and reuse it for every row.
        $useSingle = $request->boolean('single_password');
        $singlePassword = null;
        if ($useSingle) {
            $p = '';
            for ($k = 0; $k < 6; $k++) { $p .= $chars[random_int(0, strlen($chars)-1)]; }
            $singlePassword = $p;
            $singleEncrypted = Crypt::encryptString($singlePassword);
        }

        for ($i = 2; $i <= count($rows); $i++) {
            $email = trim($rows[$i]['A'] ?? '');
            if (!$email) continue;
            $username = $email;

            if ($useSingle) {
                $encrypted = $singleEncrypted;
                $password = $singlePassword;
            } else {
                $password = '';
                for ($k = 0; $k < 6; $k++) { $password .= $chars[random_int(0, strlen($chars)-1)]; }
                $encrypted = Crypt::encryptString($password);
            }

            ApplicantCredential::create([
                'bank_id' => $bank->id,
                'username' => $username,
                'password_encrypted' => $encrypted,
                'used' => false,
            ]);

            $generated[] = ['username' => $username, 'password' => $password];
        }

        ActivityLog::log('create', 'credential_bulk', 'Import kredensial untuk bank: ' . $bank->title . ' — ' . count($generated) . ' dibuat');

        return redirect()->route('banks.edit', $bank)->with('generated_bulk', $generated);
    }

    // Bulk delete selected applicant credentials
    public function deleteMultipleCredentials(Request $request, Bank $bank)
    {
        $this->authorize('update', $bank);

        $ids = $request->input('credentials', []);
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Pilih kredensial terlebih dahulu.');
        }

        $toDelete = ApplicantCredential::where('bank_id', $bank->id)->whereIn('id', $ids);
        $count = $toDelete->count();
        $toDelete->delete();

        ActivityLog::log('delete', 'credential_bulk', 'Menghapus ' . $count . ' kredensial dari bank: ' . $bank->title);

        return back()->with('success', $count . ' kredensial dihapus.');
    }

    public function update(Request $request, Bank $bank)
    {
        $this->authorize('update', $bank);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'target' => 'nullable|in:karyawan,calon_karyawan',
        ]);

        $bank->update($validated);
        ActivityLog::log('update', 'bank', 'Mengupdate bank soal: ' . $validated['title']);
        return redirect()->route('banks.edit', $bank)->with('success', 'Bank soal berhasil diperbarui.');
    }

    /**
     * Return applicant credentials as JSON for AJAX sorting/refresh in admin UI.
     */
    public function credentialsList(Request $request, Bank $bank)
    {
        $this->authorize('update', $bank);
        if ($bank->target !== 'calon_karyawan') {
            return response()->json(['error' => 'Not available'], 400);
        }

        $allowed = ['username', 'used', 'created_at'];
        $sort = $request->query('sort', 'created_at');
        $dir = strtolower($request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sort, $allowed)) $sort = 'created_at';

        $creds = $bank->applicantCredentials()->orderBy($sort, $dir)->get()->map(function($c){
            try { $plain = Crypt::decryptString($c->password_encrypted); } catch (\Exception $e) { $plain = null; }
            return [
                'id' => $c->id,
                'username' => $c->username,
                'plain_password' => $plain,
                'used' => (bool) $c->used,
                'created_at' => $c->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json(['data' => $creds]);
    }

    public function destroy(Bank $bank)
    {
        $this->authorize('delete', $bank);
        ActivityLog::log('delete', 'bank', 'Menghapus bank soal: ' . $bank->title);
        $bank->delete();
        return redirect()->route('banks.index')->with('success', 'Bank soal berhasil dihapus.');
    }

    // ===== SUB-TEST CRUD =====

    private function storeSubTest(Request $request)
    {
        $bank = Bank::findOrFail($request->bank_id);
        $this->authorize('update', $bank);

        $subTestType = $request->input('sub_test_type', 'default');

        $validated = $request->validate([
            'sub_test_title' => 'required|string|max:255',
            'sub_test_description' => 'nullable|string|max:1000',
            'sub_test_duration' => 'nullable|integer|min:1|max:600',
            'sub_test_type' => 'nullable|in:default,kraepelin,disc,papikostik',
        ]);

        $order = $bank->subTests()->max('order') ?? -1;
        $data = [
            'bank_id' => $bank->id,
            'title' => $validated['sub_test_title'],
            'description' => $validated['sub_test_description'] ?? null,
            'duration_minutes' => $validated['sub_test_duration'] ?? null,
            'order' => $order + 1,
            'type' => $subTestType,
        ];

        if ($subTestType === 'kraepelin') {
            $request->validate([
                'kraepelin_columns' => 'required|integer|min:5|max:100',
                'kraepelin_digits' => 'required|integer|min:20|max:100',
                'kraepelin_min_seconds' => 'required|integer|min:5|max:120',
                'kraepelin_max_seconds' => 'required|integer|min:5|max:120|gte:kraepelin_min_seconds',
            ]);

            $columnsCount = (int) $request->input('kraepelin_columns', 50);
            $digitsPerCol = (int) $request->input('kraepelin_digits', 60);
            $minSeconds = (int) $request->input('kraepelin_min_seconds', 15);
            $maxSeconds = (int) $request->input('kraepelin_max_seconds', 45);

            $data['kraepelin_config'] = $this->generateKraepelinConfig($columnsCount, $digitsPerCol, $minSeconds, $maxSeconds);
            $data['duration_minutes'] = null; // kraepelin uses its own timing
        }

        if ($subTestType === 'disc') {
            $data['disc_config'] = $this->generateDiscConfig();
            $data['duration_minutes'] = null; // disc is self-paced
        }

        if ($subTestType === 'papikostik') {
            $data['papikostik_config'] = $this->generatePapikostikConfig();
            $data['duration_minutes'] = null; // papikostik is self-paced
        }

        SubTest::create($data);

        ActivityLog::log('create', 'subtest', 'Menambahkan sub-test ke bank: ' . $bank->title);
        return redirect()->route('banks.edit', $bank)->with('success', 'Sub-test berhasil ditambahkan.');
    }

    private function generateKraepelinConfig(int $columnsCount, int $digitsPerCol, int $minSeconds, int $maxSeconds): array
    {
        $digits = [];
        for ($c = 0; $c < $columnsCount; $c++) {
            $col = [];
            for ($d = 0; $d < $digitsPerCol; $d++) {
                $col[] = random_int(1, 9);
            }
            $digits[] = $col;
        }

        $columnDurations = [];
        for ($c = 0; $c < $columnsCount; $c++) {
            $columnDurations[] = random_int($minSeconds, $maxSeconds);
        }

        return [
            'columns_count' => $columnsCount,
            'digits_per_column' => $digitsPerCol,
            'min_seconds' => $minSeconds,
            'max_seconds' => $maxSeconds,
            'column_durations' => $columnDurations,
            'digits' => $digits,
        ];
    }

    private function generateDiscConfig(): array
    {
        $groups = [
            ['D' => 'Tegas dan langsung', 'I' => 'Antusias dan optimis', 'S' => 'Sabar dan tenang', 'C' => 'Teliti dan akurat'],
            ['D' => 'Suka tantangan', 'I' => 'Mudah bergaul', 'S' => 'Setia dan konsisten', 'C' => 'Mengikuti aturan'],
            ['D' => 'Berani mengambil risiko', 'I' => 'Meyakinkan orang lain', 'S' => 'Pendengar yang baik', 'C' => 'Berpikir analitis'],
            ['D' => 'Kompetitif', 'I' => 'Ekspresif', 'S' => 'Kooperatif', 'C' => 'Perfeksionis'],
            ['D' => 'Mandiri', 'I' => 'Ramah', 'S' => 'Suportif', 'C' => 'Hati-hati'],
            ['D' => 'Berorientasi hasil', 'I' => 'Ceria dan energik', 'S' => 'Sabar menunggu', 'C' => 'Sistematis'],
            ['D' => 'Dominan', 'I' => 'Inspiratif', 'S' => 'Stabil', 'C' => 'Kritis'],
            ['D' => 'Ambisius', 'I' => 'Percaya diri di depan orang', 'S' => 'Dapat diandalkan', 'C' => 'Disiplin'],
            ['D' => 'Suka memimpin', 'I' => 'Komunikatif', 'S' => 'Loyal', 'C' => 'Cermat'],
            ['D' => 'Cepat bertindak', 'I' => 'Kreatif', 'S' => 'Konsisten', 'C' => 'Terstruktur'],
            ['D' => 'Mengontrol situasi', 'I' => 'Membangun hubungan', 'S' => 'Mendukung tim', 'C' => 'Mengevaluasi data'],
            ['D' => 'Menuntut', 'I' => 'Spontan', 'S' => 'Tenang', 'C' => 'Logis'],
            ['D' => 'Mengarahkan orang lain', 'I' => 'Memotivasi orang lain', 'S' => 'Mendengarkan orang lain', 'C' => 'Menganalisis masalah'],
            ['D' => 'Kuat', 'I' => 'Populer', 'S' => 'Rendah hati', 'C' => 'Presisi'],
            ['D' => 'Suka bersaing', 'I' => 'Suka bersosialisasi', 'S' => 'Suka bekerja sama', 'C' => 'Suka merencanakan'],
            ['D' => 'Terus terang', 'I' => 'Humoris', 'S' => 'Bijaksana', 'C' => 'Faktual'],
            ['D' => 'Fokus pada tujuan', 'I' => 'Fokus pada orang', 'S' => 'Fokus pada harmoni', 'C' => 'Fokus pada kualitas'],
            ['D' => 'Menentukan arah', 'I' => 'Memberikan semangat', 'S' => 'Menjaga kestabilan', 'C' => 'Memastikan akurasi'],
            ['D' => 'Pelopor', 'I' => 'Promotor', 'S' => 'Pendukung', 'C' => 'Analis'],
            ['D' => 'Pengambil keputusan cepat', 'I' => 'Pembicara handal', 'S' => 'Penengah konflik', 'C' => 'Pemecah masalah detail'],
            ['D' => 'Energik dan intensif', 'I' => 'Antusias dan hangat', 'S' => 'Tenang dan sabar', 'C' => 'Serius dan berhati-hati'],
            ['D' => 'Berorientasi kekuasaan', 'I' => 'Berorientasi pengakuan', 'S' => 'Berorientasi keamanan', 'C' => 'Berorientasi prosedur'],
            ['D' => 'Gesit', 'I' => 'Menarik', 'S' => 'Stabil', 'C' => 'Rapi'],
            ['D' => 'Mengatasi hambatan', 'I' => 'Mempengaruhi orang', 'S' => 'Menjaga ketenangan', 'C' => 'Memperhatikan detail'],
        ];

        $questions = [];
        foreach ($groups as $gi => $group) {
            $statements = [];
            foreach ($group as $trait => $text) {
                $statements[] = ['trait' => $trait, 'text' => $text];
            }
            shuffle($statements);
            $questions[] = [
                'group' => $gi + 1,
                'statements' => $statements,
            ];
        }

        return [
            'question_count' => count($groups),
            'questions' => $questions,
        ];
    }

    private function generatePapikostikConfig(): array
    {
        // 90 forced-choice pairs mapping to 20 PAPIKOSTIK dimensions
        // Each dimension: N,G,A,L,P,I,T,V,S,R,D,C,X,B,O,Z,E,K,F,W (scored 0-9)
        $pairs = [
            ['a' => ['dim' => 'G', 'text' => 'Saya suka menjadi pemimpin dalam kelompok'], 'b' => ['dim' => 'N', 'text' => 'Saya butuh menyelesaikan tugas sendiri']],
            ['a' => ['dim' => 'A', 'text' => 'Saya nyaman bekerja dengan kecepatan tinggi'], 'b' => ['dim' => 'L', 'text' => 'Saya lebih suka rutinitas yang teratur']],
            ['a' => ['dim' => 'P', 'text' => 'Saya selalu ingin menampilkan yang terbaik'], 'b' => ['dim' => 'I', 'text' => 'Saya suka berpikir mandiri']],
            ['a' => ['dim' => 'T', 'text' => 'Saya suka bekerja dekat dengan orang lain'], 'b' => ['dim' => 'V', 'text' => 'Saya suka aktivitas yang penuh energi']],
            ['a' => ['dim' => 'S', 'text' => 'Saya suka berinteraksi sosial dengan banyak orang'], 'b' => ['dim' => 'R', 'text' => 'Saya lebih suka bekerja dengan konsep teoritis']],
            ['a' => ['dim' => 'D', 'text' => 'Saya suka memperhatikan detail pekerjaan'], 'b' => ['dim' => 'C', 'text' => 'Saya suka mengorganisir dan menata pekerjaan']],
            ['a' => ['dim' => 'X', 'text' => 'Saya membutuhkan variasi dalam pekerjaan'], 'b' => ['dim' => 'B', 'text' => 'Saya merasa nyaman mengikuti arahan orang lain']],
            ['a' => ['dim' => 'O', 'text' => 'Saya sering memilih berdasarkan perasaan'], 'b' => ['dim' => 'Z', 'text' => 'Saya lebih suka tugas yang menantang']],
            ['a' => ['dim' => 'E', 'text' => 'Saya suka memengaruhi pendapat orang lain'], 'b' => ['dim' => 'K', 'text' => 'Saya suka bekerja keras untuk mencapai tujuan']],
            ['a' => ['dim' => 'F', 'text' => 'Saya suka membantu dan mendukung orang lain'], 'b' => ['dim' => 'W', 'text' => 'Saya nyaman bekerja berdekatan dengan orang lain']],
            ['a' => ['dim' => 'N', 'text' => 'Saya ingin mencapai hasil kerja yang sempurna'], 'b' => ['dim' => 'A', 'text' => 'Saya suka menjalani hidup dengan cepat dan dinamis']],
            ['a' => ['dim' => 'G', 'text' => 'Saya suka mengambil tanggung jawab kepemimpinan'], 'b' => ['dim' => 'P', 'text' => 'Saya ingin dilihat sebagai orang yang berpengaruh']],
            ['a' => ['dim' => 'L', 'text' => 'Saya merasa tenang dengan jadwal yang tetap'], 'b' => ['dim' => 'I', 'text' => 'Saya suka menganalisis sebelum bertindak']],
            ['a' => ['dim' => 'T', 'text' => 'Saya menikmati kerja tim yang harmonis'], 'b' => ['dim' => 'S', 'text' => 'Saya mudah bergaul dan suka bertemu orang baru']],
            ['a' => ['dim' => 'V', 'text' => 'Saya suka pekerjaan yang menuntut fisik aktif'], 'b' => ['dim' => 'D', 'text' => 'Saya cermat memeriksa hasil pekerjaan saya']],
            ['a' => ['dim' => 'R', 'text' => 'Saya menikmati pekerjaan yang membutuhkan logika'], 'b' => ['dim' => 'X', 'text' => 'Saya bosan dengan pekerjaan yang monoton']],
            ['a' => ['dim' => 'C', 'text' => 'Saya suka merencanakan pekerjaan secara terstruktur'], 'b' => ['dim' => 'O', 'text' => 'Saya sering merasa empati yang kuat terhadap orang lain']],
            ['a' => ['dim' => 'B', 'text' => 'Saya nyaman bekerja di bawah pengawasan'], 'b' => ['dim' => 'E', 'text' => 'Saya suka membujuk orang untuk mengikuti ide saya']],
            ['a' => ['dim' => 'Z', 'text' => 'Saya terdorong untuk berprestasi tinggi'], 'b' => ['dim' => 'F', 'text' => 'Saya selalu siap membantu rekan kerja']],
            ['a' => ['dim' => 'K', 'text' => 'Saya gigih dan tidak mudah menyerah'], 'b' => ['dim' => 'W', 'text' => 'Saya butuh lingkungan kerja yang nyaman']],
            ['a' => ['dim' => 'N', 'text' => 'Saya teliti dalam setiap pekerjaan yang saya lakukan'], 'b' => ['dim' => 'G', 'text' => 'Saya senang mengambil peran sebagai koordinator']],
            ['a' => ['dim' => 'A', 'text' => 'Saya bekerja dengan tempo cepat'], 'b' => ['dim' => 'T', 'text' => 'Saya sangat menghargai hubungan kerja yang dekat']],
            ['a' => ['dim' => 'P', 'text' => 'Saya ingin diakui atas pencapaian saya'], 'b' => ['dim' => 'V', 'text' => 'Saya lebih suka pekerjaan yang melibatkan gerakan fisik']],
            ['a' => ['dim' => 'L', 'text' => 'Saya suka lingkungan kerja yang stabil dan terprediksi'], 'b' => ['dim' => 'S', 'text' => 'Saya mudah memulai percakapan dengan orang asing']],
            ['a' => ['dim' => 'I', 'text' => 'Saya sering mempertimbangkan keputusan dengan hati-hati'], 'b' => ['dim' => 'D', 'text' => 'Saya fokus pada akurasi dan ketepatan']],
            ['a' => ['dim' => 'R', 'text' => 'Saya menikmati memecahkan masalah yang rumit'], 'b' => ['dim' => 'C', 'text' => 'Saya suka mengatur jadwal dan prioritas']],
            ['a' => ['dim' => 'X', 'text' => 'Saya suka mencoba hal-hal baru dalam pekerjaan'], 'b' => ['dim' => 'Z', 'text' => 'Saya termotivasi oleh target yang menantang']],
            ['a' => ['dim' => 'B', 'text' => 'Saya lebih suka mengikuti prosedur yang sudah ada'], 'b' => ['dim' => 'K', 'text' => 'Saya bekerja keras meskipun tidak ada yang mengawasi']],
            ['a' => ['dim' => 'O', 'text' => 'Saya sensitif terhadap perasaan orang di sekitar saya'], 'b' => ['dim' => 'W', 'text' => 'Saya membutuhkan rasa aman dalam pekerjaan']],
            ['a' => ['dim' => 'E', 'text' => 'Saya percaya diri menyampaikan pendapat di depan banyak orang'], 'b' => ['dim' => 'F', 'text' => 'Saya suka menolong orang yang mengalami kesulitan']],
            ['a' => ['dim' => 'G', 'text' => 'Saya suka mengarahkan orang lain dalam bekerja'], 'b' => ['dim' => 'A', 'text' => 'Saya terbiasa menyelesaikan pekerjaan dengan cepat']],
            ['a' => ['dim' => 'N', 'text' => 'Saya berusaha agar pekerjaan saya sempurna'], 'b' => ['dim' => 'L', 'text' => 'Saya merasa aman dengan pola kerja yang teratur']],
            ['a' => ['dim' => 'P', 'text' => 'Saya ingin pekerjaan saya diperhatikan orang lain'], 'b' => ['dim' => 'T', 'text' => 'Saya membangun hubungan dekat dengan rekan kerja']],
            ['a' => ['dim' => 'I', 'text' => 'Saya hati-hati sebelum memutuskan sesuatu'], 'b' => ['dim' => 'V', 'text' => 'Saya penuh semangat dan berenergi tinggi']],
            ['a' => ['dim' => 'S', 'text' => 'Saya pandai mempengaruhi orang dalam pergaulan'], 'b' => ['dim' => 'R', 'text' => 'Saya menikmati pembelajaran hal-hal abstrak']],
            ['a' => ['dim' => 'D', 'text' => 'Saya tidak suka membuat kesalahan'], 'b' => ['dim' => 'X', 'text' => 'Saya merasa bosan jika pekerjaan itu-itu saja']],
            ['a' => ['dim' => 'C', 'text' => 'Saya suka membuat daftar tugas dan menaatinya'], 'b' => ['dim' => 'B', 'text' => 'Saya nyaman menerima instruksi dari atasan']],
            ['a' => ['dim' => 'Z', 'text' => 'Saya ambisius dalam mencapai tujuan karir saya'], 'b' => ['dim' => 'O', 'text' => 'Saya mudah merasakan apa yang orang lain rasakan']],
            ['a' => ['dim' => 'K', 'text' => 'Saya tekun mengerjakan tugas meskipun sulit'], 'b' => ['dim' => 'E', 'text' => 'Saya suka memimpin diskusi kelompok']],
            ['a' => ['dim' => 'F', 'text' => 'Saya rela berkorban demi membantu orang lain'], 'b' => ['dim' => 'W', 'text' => 'Saya menghindari konflik di tempat kerja']],
            ['a' => ['dim' => 'A', 'text' => 'Saya produktif dan cepat dalam bekerja'], 'b' => ['dim' => 'G', 'text' => 'Saya ingin memegang kendali dalam proyek']],
            ['a' => ['dim' => 'N', 'text' => 'Saya perfeksionis dalam hal pekerjaan'], 'b' => ['dim' => 'P', 'text' => 'Saya ingin terlihat menonjol di hadapan orang lain']],
            ['a' => ['dim' => 'L', 'text' => 'Saya lebih nyaman dengan cara kerja yang sudah terbukti'], 'b' => ['dim' => 'T', 'text' => 'Saya selalu mencari kedekatan emosional dengan tim']],
            ['a' => ['dim' => 'I', 'text' => 'Saya mempertimbangkan banyak hal sebelum bertindak'], 'b' => ['dim' => 'S', 'text' => 'Saya dikenal sebagai orang yang ramah dan terbuka']],
            ['a' => ['dim' => 'V', 'text' => 'Saya lebih suka bergerak aktif daripada duduk diam'], 'b' => ['dim' => 'R', 'text' => 'Saya suka menganalisis data dan informasi']],
            ['a' => ['dim' => 'D', 'text' => 'Saya mengutamakan kualitas daripada kuantitas'], 'b' => ['dim' => 'C', 'text' => 'Saya suka membuat rencana kerja yang terperinci']],
            ['a' => ['dim' => 'X', 'text' => 'Saya suka perubahan dan hal yang tidak terduga'], 'b' => ['dim' => 'B', 'text' => 'Saya lebih suka mendapat arahan yang jelas']],
            ['a' => ['dim' => 'Z', 'text' => 'Saya selalu ingin lebih baik dari sebelumnya'], 'b' => ['dim' => 'K', 'text' => 'Saya tidak mudah menyerah meskipun banyak hambatan']],
            ['a' => ['dim' => 'O', 'text' => 'Saya perhatian terhadap kebutuhan orang lain'], 'b' => ['dim' => 'E', 'text' => 'Saya suka meyakinkan orang dengan argumen saya']],
            ['a' => ['dim' => 'F', 'text' => 'Saya tulus dalam membantu orang tanpa pamrih'], 'b' => ['dim' => 'W', 'text' => 'Saya butuh kepastian dan keamanan dalam karir']],
            ['a' => ['dim' => 'G', 'text' => 'Saya percaya diri mengambil keputusan untuk tim'], 'b' => ['dim' => 'L', 'text' => 'Saya lebih memilih bekerja di zona nyaman saya']],
            ['a' => ['dim' => 'N', 'text' => 'Saya mengejar standar tinggi dalam pekerjaan'], 'b' => ['dim' => 'T', 'text' => 'Saya mementingkan kebersamaan dalam bekerja']],
            ['a' => ['dim' => 'A', 'text' => 'Saya menyelesaikan tugas lebih cepat dari kebanyakan orang'], 'b' => ['dim' => 'I', 'text' => 'Saya lebih suka bekerja sendiri daripada berkelompok']],
            ['a' => ['dim' => 'P', 'text' => 'Saya senang mendapat pengakuan publik'], 'b' => ['dim' => 'D', 'text' => 'Saya selalu memeriksa ulang hasil pekerjaan saya']],
            ['a' => ['dim' => 'V', 'text' => 'Saya menyukai pekerjaan lapangan yang aktif'], 'b' => ['dim' => 'X', 'text' => 'Saya suka tugas yang bervariasi setiap hari']],
            ['a' => ['dim' => 'S', 'text' => 'Saya aktif dalam kegiatan sosial di kantor'], 'b' => ['dim' => 'B', 'text' => 'Saya merasa nyaman bekerja sesuai panduan']],
            ['a' => ['dim' => 'R', 'text' => 'Saya suka memahami teori di balik suatu masalah'], 'b' => ['dim' => 'Z', 'text' => 'Saya memiliki standar pencapaian yang tinggi']],
            ['a' => ['dim' => 'C', 'text' => 'Saya selalu merencanakan langkah-langkah saya'], 'b' => ['dim' => 'F', 'text' => 'Saya senang mendukung orang lain untuk berhasil']],
            ['a' => ['dim' => 'O', 'text' => 'Saya peka terhadap suasana hati di lingkungan saya'], 'b' => ['dim' => 'K', 'text' => 'Saya bertekad kuat menyelesaikan apa yang saya mulai']],
            ['a' => ['dim' => 'E', 'text' => 'Saya yakin bisa meyakinkan orang lain'], 'b' => ['dim' => 'W', 'text' => 'Saya mencari stabilitas dalam hidup dan pekerjaan']],
            ['a' => ['dim' => 'G', 'text' => 'Saya senang memimpin rapat atau diskusi'], 'b' => ['dim' => 'V', 'text' => 'Saya menikmati pekerjaan yang melibatkan banyak gerakan']],
            ['a' => ['dim' => 'N', 'text' => 'Saya tidak puas jika pekerjaan belum sempurna'], 'b' => ['dim' => 'S', 'text' => 'Saya suka berkumpul dan berdiskusi dengan orang banyak']],
            ['a' => ['dim' => 'A', 'text' => 'Saya menyukai ritme kerja yang cepat'], 'b' => ['dim' => 'D', 'text' => 'Saya teliti dan memperhatikan hal-hal kecil']],
            ['a' => ['dim' => 'P', 'text' => 'Saya suka menjadi pusat perhatian'], 'b' => ['dim' => 'R', 'text' => 'Saya gemar mempelajari sesuatu secara mendalam']],
            ['a' => ['dim' => 'L', 'text' => 'Saya tidak suka perubahan mendadak'], 'b' => ['dim' => 'X', 'text' => 'Saya selalu mencari pengalaman baru']],
            ['a' => ['dim' => 'I', 'text' => 'Saya sering merenung sebelum mengambil tindakan'], 'b' => ['dim' => 'C', 'text' => 'Saya menyusun prioritas kerja dengan rapi']],
            ['a' => ['dim' => 'T', 'text' => 'Saya setia terhadap orang-orang terdekat saya'], 'b' => ['dim' => 'B', 'text' => 'Saya hormat pada otoritas dan patuh pada aturan']],
            ['a' => ['dim' => 'Z', 'text' => 'Saya punya ambisi yang besar dalam karir'], 'b' => ['dim' => 'E', 'text' => 'Saya pandai memengaruhi keputusan orang lain']],
            ['a' => ['dim' => 'K', 'text' => 'Saya pantang menyerah meski dalam tekanan'], 'b' => ['dim' => 'F', 'text' => 'Saya suka merawat dan memperhatikan orang lain']],
            ['a' => ['dim' => 'O', 'text' => 'Saya mudah terharu oleh cerita orang lain'], 'b' => ['dim' => 'W', 'text' => 'Saya perlu rasa nyaman dalam bekerja']],
            ['a' => ['dim' => 'G', 'text' => 'Saya suka mengambil inisiatif dalam kelompok'], 'b' => ['dim' => 'D', 'text' => 'Saya memeriksa pekerjaan hingga detail terkecil']],
            ['a' => ['dim' => 'N', 'text' => 'Saya berusaha keras agar hasilnya sempurna'], 'b' => ['dim' => 'R', 'text' => 'Saya senang mengkaji ide-ide baru']],
            ['a' => ['dim' => 'A', 'text' => 'Saya selalu ingin segera menyelesaikan tugas'], 'b' => ['dim' => 'X', 'text' => 'Saya tidak suka mengerjakan hal yang sama berulang-ulang']],
            ['a' => ['dim' => 'P', 'text' => 'Saya senang bila pencapaian saya dipuji'], 'b' => ['dim' => 'C', 'text' => 'Saya sangat terorganisir dalam bekerja']],
            ['a' => ['dim' => 'L', 'text' => 'Saya menghargai konsistensi dalam pekerjaan'], 'b' => ['dim' => 'B', 'text' => 'Saya mematuhi aturan dan prosedur yang ada']],
            ['a' => ['dim' => 'I', 'text' => 'Saya lebih suka mengerjakan tugas secara mandiri'], 'b' => ['dim' => 'Z', 'text' => 'Saya didorong oleh keinginan untuk sukses']],
            ['a' => ['dim' => 'T', 'text' => 'Saya menaruh perhatian besar pada perasaan rekan kerja'], 'b' => ['dim' => 'E', 'text' => 'Saya bisa membuat orang lain mengikuti visi saya']],
            ['a' => ['dim' => 'V', 'text' => 'Saya suka aktivitas yang melibatkan fisik'], 'b' => ['dim' => 'F', 'text' => 'Saya sukarela menawarkan bantuan kepada siapa saja']],
            ['a' => ['dim' => 'S', 'text' => 'Saya menikmati pesta dan acara sosial'], 'b' => ['dim' => 'W', 'text' => 'Saya menghindari situasi yang tidak pasti']],
            ['a' => ['dim' => 'K', 'text' => 'Saya terus berusaha meskipun menghadapi kegagalan'], 'b' => ['dim' => 'O', 'text' => 'Saya mudah memahami perasaan orang lain']],
            ['a' => ['dim' => 'G', 'text' => 'Saya nyaman mengambil keputusan sulit'], 'b' => ['dim' => 'R', 'text' => 'Saya suka berpikir kritis tentang suatu topik']],
            ['a' => ['dim' => 'N', 'text' => 'Saya menetapkan standar tinggi untuk diri sendiri'], 'b' => ['dim' => 'X', 'text' => 'Saya suka eksplorasi dan petualangan']],
            ['a' => ['dim' => 'A', 'text' => 'Saya bisa bekerja di bawah tekanan waktu'], 'b' => ['dim' => 'C', 'text' => 'Saya suka mengatur dan mengelola proyek']],
            ['a' => ['dim' => 'P', 'text' => 'Saya bangga dengan pencapaian saya'], 'b' => ['dim' => 'B', 'text' => 'Saya mengikuti petunjuk atasan dengan baik']],
            ['a' => ['dim' => 'L', 'text' => 'Saya memilih pekerjaan yang sudah familiar'], 'b' => ['dim' => 'Z', 'text' => 'Saya selalu mengejar target yang lebih tinggi']],
            ['a' => ['dim' => 'I', 'text' => 'Saya berhati-hati dan penuh pertimbangan'], 'b' => ['dim' => 'E', 'text' => 'Saya percaya diri dalam presentasi']],
            ['a' => ['dim' => 'T', 'text' => 'Saya menjaga hubungan baik dengan semua rekan'], 'b' => ['dim' => 'F', 'text' => 'Saya merasa puas ketika bisa membantu orang']],
            ['a' => ['dim' => 'V', 'text' => 'Saya tidak betah duduk lama di meja kerja'], 'b' => ['dim' => 'W', 'text' => 'Saya menghargai lingkungan kerja yang terkendali']],
            ['a' => ['dim' => 'S', 'text' => 'Saya senang berkenalan dengan orang-orang baru'], 'b' => ['dim' => 'O', 'text' => 'Saya berempati tinggi terhadap penderitaan orang lain']],
            ['a' => ['dim' => 'D', 'text' => 'Saya mengerjakan sesuatu dengan penuh kehati-hatian'], 'b' => ['dim' => 'K', 'text' => 'Saya pekerja keras yang tidak kenal lelah']],
        ];

        $questions = [];
        foreach ($pairs as $pi => $pair) {
            $questions[] = [
                'number' => $pi + 1,
                'a' => $pair['a'],
                'b' => $pair['b'],
            ];
        }

        return [
            'question_count' => count($pairs),
            'dimensions' => ['N','G','A','L','P','I','T','V','S','R','D','C','X','B','O','Z','E','K','F','W'],
            'questions' => $questions,
        ];
    }

    public function editSubTest(SubTest $subTest)
    {
        $bank = $subTest->bank;
        $this->authorize('update', $bank);
        $questions = $subTest->questions;
        $exampleQuestions = $subTest->exampleQuestions;
        return view('banks.edit-subtest', compact('bank', 'subTest', 'questions', 'exampleQuestions'));
    }

    public function updateSubTest(Request $request, SubTest $subTest)
    {
        $bank = $subTest->bank;
        $this->authorize('update', $bank);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
        ]);

        if ($subTest->type === 'kraepelin') {
            $validated['duration_minutes'] = null;

            if ($request->boolean('regenerate_kraepelin')) {
                $request->validate([
                    'kraepelin_columns' => 'required|integer|min:5|max:100',
                    'kraepelin_digits' => 'required|integer|min:20|max:100',
                    'kraepelin_min_seconds' => 'required|integer|min:5|max:120',
                    'kraepelin_max_seconds' => 'required|integer|min:5|max:120|gte:kraepelin_min_seconds',
                ]);

                $validated['kraepelin_config'] = $this->generateKraepelinConfig(
                    (int) $request->input('kraepelin_columns'),
                    (int) $request->input('kraepelin_digits'),
                    (int) $request->input('kraepelin_min_seconds'),
                    (int) $request->input('kraepelin_max_seconds')
                );
            }
        }

        if ($subTest->type === 'disc') {
            $validated['duration_minutes'] = null;

            if ($request->boolean('regenerate_disc')) {
                $validated['disc_config'] = $this->generateDiscConfig();
            }
        }

        if ($subTest->type === 'papikostik') {
            $validated['duration_minutes'] = null;

            if ($request->boolean('regenerate_papikostik')) {
                $validated['papikostik_config'] = $this->generatePapikostikConfig();
            }
        }

        $subTest->update($validated);
        ActivityLog::log('update', 'subtest', 'Mengupdate sub-test: ' . $validated['title']);
        return redirect()->route('sub-tests.edit', $subTest)->with('success', 'Sub-test berhasil diperbarui.');
    }

    public function deleteSubTest(SubTest $subTest)
    {
        $bank = $subTest->bank;
        $this->authorize('delete', $bank);
        ActivityLog::log('delete', 'subtest', 'Menghapus sub-test: ' . $subTest->title . ' dari bank: ' . $bank->title);
        $subTest->delete();
        return redirect()->route('banks.edit', $bank)->with('success', 'Sub-test berhasil dihapus.');
    }

    /**
     * Custom validation messages for upload errors to show friendly alerts.
     */
    private function uploadValidationMessages()
    {
        $sizeMsg = 'gagal upload file lebih dari 5 mb';
        $formatMsg = 'format salah';

        return [
            // General messages
            'required' => 'Wajib diisi',
            'question.required' => 'Soal harus diisi',
            'option_a.required' => 'Opsi A wajib diisi',
            'option_b.required' => 'Opsi B wajib diisi',
            'option_c.required' => 'Opsi C wajib diisi',
            'option_d.required' => 'Opsi D wajib diisi',
            'correct_answer.required' => 'Pilih jawaban benar',

            'image.max' => $sizeMsg,
            'image.mimes' => $formatMsg,
            'audio.max' => $sizeMsg,
            'audio.mimes' => $formatMsg,
            'option_a_image.max' => $sizeMsg,
            'option_a_image.mimes' => $formatMsg,
            'option_b_image.max' => $sizeMsg,
            'option_b_image.mimes' => $formatMsg,
            'option_c_image.max' => $sizeMsg,
            'option_c_image.mimes' => $formatMsg,
            'option_d_image.max' => $sizeMsg,
            'option_d_image.mimes' => $formatMsg,
            'option_e_image.max' => $sizeMsg,
            'option_e_image.mimes' => $formatMsg,
            'option_e.required' => 'Opsi E wajib diisi',
            'option_f_image.max' => $sizeMsg,
            'option_f_image.mimes' => $formatMsg,
            'option_f.required' => 'Opsi F wajib diisi',
        ];
    }

    public function results(Bank $bank, Request $request)
    {
        $this->authorize('view', $bank);
        $query = $bank->responses()
            ->where('completed', true);

        // Filter by participant name
        if ($request->filled('nama')) {
            $query->where('participant_name', 'like', '%' . $request->nama . '%');
        }

        // Filter by month (1-12)
        if ($request->filled('bulan')) {
            $query->whereMonth('completed_at', $request->bulan);
        }

        // Filter by specific date
        if ($request->filled('tanggal')) {
            $query->whereDate('completed_at', $request->tanggal);
        }

        $responses = $query->orderBy('completed_at', 'desc')->get();

        // Load questions: from sub-tests if any, otherwise direct
        $subTests = $bank->subTests()->with(['questions'])->get();
        if ($subTests->count() > 0) {
            $questions = collect();
            foreach ($subTests as $st) {
                $questions = $questions->merge($st->questions);
            }
        } else {
            $questions = $bank->questions()->orderBy('order')->get();
        }

        return view('banks.results', compact('bank', 'responses', 'questions', 'subTests'));
    }



    public function toggleBank(Bank $bank)
    {
        $this->authorize('update', $bank);
        $bank->update(['is_active' => !$bank->is_active]);
        $status = $bank->is_active ? 'dibuka' : 'ditutup';
        ActivityLog::log('update', 'bank', 'Toggle bank soal ' . $bank->title . ': ' . $status);
        return back()->with('success', 'Link soal berhasil ' . $status . '.');
    }

    public function editQuestion(Question $question)
    {
        $bank = $question->bank;
        $this->authorize('update', $bank);
        $subTest = $question->subTest;
        return view('banks.edit-question', compact('bank', 'question', 'subTest'));
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $bank = $question->bank;
        $this->authorize('update', $bank);
        $type = $request->input('type', 'multiple_choice');

        $baseRules = [
            'question' => 'required|string|max:1000',
            'type' => 'required|in:text,multiple_choice,narrative,survey',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'audio' => 'nullable|mimes:mp3,wav,ogg|max:5120',
            'option_a_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_b_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_c_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_d_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_e_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'option_f_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];

        if ($type === 'narrative') {
            $validated = $request->validate($baseRules, $this->uploadValidationMessages());
            $validated['option_a'] = null;
            $validated['option_b'] = null;
            $validated['option_c'] = null;
            $validated['option_d'] = null;
            $validated['option_e'] = null;
            $validated['option_f'] = null;
            $validated['option_count'] = null;
            $validated['correct_answer'] = null;
            $validated['correct_answer_text'] = null;
        } elseif ($type === 'text') {
            $validated = $request->validate(array_merge($baseRules, [
                'correct_answer_text' => 'required|string|max:500',
            ]), $this->uploadValidationMessages());
            $validated['option_a'] = null;
            $validated['option_b'] = null;
            $validated['option_c'] = null;
            $validated['option_d'] = null;
            $validated['option_e'] = null;
            $validated['option_f'] = null;
            $validated['option_count'] = null;
            $validated['correct_answer'] = null;
        } elseif ($type === 'survey') {
            $optCount = (int) $request->input('option_count', 2);
            if ($optCount < 2) $optCount = 2;
            if ($optCount > 6) $optCount = 6;

            $surveyRules = ['option_count' => 'required|integer|min:2|max:6'];
            $labels = ['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d', 'E' => 'option_e', 'F' => 'option_f'];
            $i = 0;
            foreach ($labels as $letter => $field) {
                $i++;
                if ($i <= $optCount) {
                    $surveyRules[$field] = 'required|string|max:500';
                }
            }

            $validated = $request->validate(array_merge($baseRules, $surveyRules), $this->uploadValidationMessages());

            $i = 0;
            foreach ($labels as $letter => $field) {
                $i++;
                if ($i > $optCount) {
                    $validated[$field] = null;
                }
            }
            $validated['option_count'] = $optCount;
            $validated['correct_answer'] = null;
            $validated['correct_answer_text'] = null;
        } else {
            $validated = $request->validate(array_merge($baseRules, [
                'option_a' => 'required|string|max:500',
                'option_b' => 'required|string|max:500',
                'option_c' => 'required|string|max:500',
                'option_d' => 'required|string|max:500',
                'option_e' => 'nullable|string|max:500',
                'option_f' => 'nullable|string|max:500',
                'correct_answer' => 'required|in:A,B,C,D,E,F',
            ]), $this->uploadValidationMessages());
            $validated['option_count'] = null;
        }

        if ($request->hasFile('image')) {
              $file = $request->file('image');
              if ($file->getSize() > 5 * 1024 * 1024) {
                  return back()->withInput()->with('error', 'gagal upload file lebih dari 5 mb');
              }
              $validated['image'] = $file->store('questions/images', 'public');
              $validated['image_data'] = file_get_contents($file->getRealPath());
              $validated['image_mime'] = $file->getClientMimeType();
        }
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            if ($audio->getSize() > 5 * 1024 * 1024) {
                return back()->withInput()->with('error', 'gagal upload file lebih dari 5 mb');
            }
            $validated['audio'] = $audio->store('questions/audio', 'public');
        }

        // Option images (for multiple_choice)
        foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $letter) {
            $fieldName = 'option_' . $letter . '_image';
            if ($request->hasFile($fieldName)) {
                $f = $request->file($fieldName);
                if ($f->getSize() > 5 * 1024 * 1024) {
                    return back()->withInput()->with('error', 'gagal upload file lebih dari 5 mb');
                }
                $validated[$fieldName] = $f->store('questions/option-images', 'public');
            } elseif ($request->input('remove_' . $fieldName) === '1') {
                $validated[$fieldName] = null;
            }
        }

        $question->update($validated);
        ActivityLog::log('update', 'question', 'Mengupdate soal di bank: ' . $bank->title);
        return redirect()->route('sub-tests.edit', $question->sub_test_id)->with('success', 'Soal berhasil diperbarui.');
    }

    // Serve question image from database if available, otherwise fall back to storage
    public function getQuestionImage(Question $question)
    {
        if ($question->image_data) {
            $mime = $question->image_mime ?? 'image/jpeg';
            return response($question->image_data, 200)->header('Content-Type', $mime);
        }

        if ($question->image && file_exists(storage_path('app/public/' . $question->image))) {
            return response()->file(storage_path('app/public/' . $question->image));
        }

        abort(404);
    }

    public function deleteQuestion(Question $question)
    {
        $bank = $question->bank;
        $this->authorize('update', $bank);
        ActivityLog::log('delete', 'question', 'Menghapus soal dari bank: ' . $bank->title);
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Export individual participant result as PDF.
     */
    public function exportParticipantPdf(Bank $bank, ParticipantResponse $response)
    {
        $this->authorize('view', $bank);

        if ($response->bank_id !== $bank->id) {
            abort(404);
        }

        $questions = $bank->questions()->orderBy('order')->get();

        // If bank has sub-tests, load questions from sub-tests instead
        $subTests = $bank->subTests()->with(['questions'])->get();
        if ($subTests->count() > 0) {
            $questions = collect();
            foreach ($subTests as $st) {
                $questions = $questions->merge($st->questions);
            }
        }

        $totalQuestions = $questions->count();
        $scoreableQuestions = $questions->whereNotIn('type', ['narrative', 'survey'])->count();
        $percentage = $scoreableQuestions > 0 ? round(($response->score / $scoreableQuestions) * 100, 2) : 0;
        $answers = $response->responses ?? [];

        $pdf = Pdf::loadView('banks.participant-pdf', compact(
            'bank', 'response', 'questions', 'totalQuestions', 'percentage', 'answers'
        ));

        $pdf->setPaper('A4', 'portrait');

        $fileName = 'hasil_tes_' . str_replace(' ', '_', $response->participant_name) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export individual kraepelin result as PDF with graph and full metrics.
     */
    public function exportKraepelinPdf(Bank $bank, ParticipantResponse $response, SubTest $subTest)
    {
        $this->authorize('view', $bank);

        if ($response->bank_id !== $bank->id) {
            abort(404);
        }
        if ($subTest->bank_id !== $bank->id || $subTest->type !== 'kraepelin') {
            abort(404);
        }

        $pdf = Pdf::loadView('banks.kraepelin-pdf', compact('bank', 'response', 'subTest'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'kraepelin_' . str_replace(' ', '_', $response->participant_name) . '_' . date('Ymd') . '.pdf';

        ActivityLog::log('export', 'bank', 'Export PDF Kraepelin: ' . $response->participant_name . ' - ' . $subTest->title);

        return $pdf->download($fileName);
    }

    public function exportDiscPdf(Bank $bank, ParticipantResponse $response, SubTest $subTest)
    {
        $this->authorize('view', $bank);

        if ($response->bank_id !== $bank->id) {
            abort(404);
        }
        if ($subTest->bank_id !== $bank->id || $subTest->type !== 'disc') {
            abort(404);
        }

        $pdf = Pdf::loadView('banks.disc-pdf', compact('bank', 'response', 'subTest'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'disc_' . str_replace(' ', '_', $response->participant_name) . '_' . date('Ymd') . '.pdf';

        ActivityLog::log('export', 'bank', 'Export PDF DISC: ' . $response->participant_name . ' - ' . $subTest->title);

        return $pdf->download($fileName);
    }

    public function exportPapikostikPdf(Bank $bank, ParticipantResponse $response, SubTest $subTest)
    {
        $this->authorize('view', $bank);

        if ($response->bank_id !== $bank->id) {
            abort(404);
        }
        if ($subTest->bank_id !== $bank->id || $subTest->type !== 'papikostik') {
            abort(404);
        }

        $pdf = Pdf::loadView('banks.papikostik-pdf', compact('bank', 'response', 'subTest'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'papikostik_' . str_replace(' ', '_', $response->participant_name) . '_' . date('Ymd') . '.pdf';

        ActivityLog::log('export', 'bank', 'Export PDF PAPIKOSTIK: ' . $response->participant_name . ' - ' . $subTest->title);

        return $pdf->download($fileName);
    }

    public function exportReportPdf(Bank $bank, ParticipantResponse $response)
    {
        $this->authorize('view', $bank);

        if ($response->bank_id !== $bank->id) {
            abort(404);
        }

        $subTests = $bank->subTests()->with(['questions'])->get();

        $pdf = Pdf::loadView('banks.report-pdf', compact('bank', 'response', 'subTests'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'laporan_' . str_replace(' ', '_', $response->participant_name) . '_' . date('Ymd') . '.pdf';

        ActivityLog::log('export', 'bank', 'Export Laporan PDF: ' . $response->participant_name);

        return $pdf->download($fileName);
    }

    /**
     * Export all participant results as Excel (.xlsx) - Google Forms style.
     * Includes full question text, answer options, and participant answers.
     */
    public function exportExcel(Bank $bank, Request $request)
    {
        $this->authorize('view', $bank);

        // Load questions: from sub-tests if any, otherwise direct
        $subTests = $bank->subTests()->with(['questions'])->get();
        if ($subTests->count() > 0) {
            $questions = collect();
            foreach ($subTests as $st) {
                $questions = $questions->merge($st->questions);
            }
        } else {
            $questions = $bank->questions()->orderBy('order')->get();
        }

        $query = $bank->responses()
            ->where('completed', true);

        // Apply same filters as results page
        if ($request->filled('nama')) {
            $query->where('participant_name', 'like', '%' . $request->nama . '%');
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('completed_at', $request->bulan);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('completed_at', $request->tanggal);
        }

        $responses = $query->orderBy('completed_at', 'asc')->get();

        $totalQuestions = $questions->count();
        $scoreableQuestions = $questions->whereNotIn('type', ['narrative', 'survey'])->count();

        // Build headers — different columns for calon_karyawan vs karyawan
        $isCalon = $bank->target === 'calon_karyawan';

        if ($isCalon) {
            $fixedHeaders = [
                'No',
                'Waktu Selesai',
                'Nama Peserta',
                'Email',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Usia',
                'Skor',
                'Total Soal Dinilai',
                'Persentase (%)',
                'Keterangan',
                'Durasi',
            ];
        } else {
            $fixedHeaders = [
                'No',
                'Waktu Selesai',
                'Nama Peserta',
                'NIK',
                'Email',
                'No. Telepon',
                'Departemen',
                'Jabatan',
                'Skor',
                'Total Soal Dinilai',
                'Persentase (%)',
                'Keterangan',
                'Durasi',
            ];
        }

        $headers = $fixedHeaders;

        // Add each question as a column header with full question text
        $typeLabelMap = ['narrative' => '[Narasi]', 'text' => '[Isian]'];
        foreach ($questions as $index => $question) {
            $typeLabel = isset($typeLabelMap[$question->type]) ? $typeLabelMap[$question->type] : '[PG]';
            $prefix = $question->subTest ? $question->subTest->title . ' - ' : '';
            $headers[] = 'Q' . ($index + 1) . ' ' . $typeLabel . ': ' . $prefix . $question->question;
        }

        $spreadsheet = new Spreadsheet();

        // ===== SHEET 1: Hasil Tes =====
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hasil Tes');

        $lastColIndex = count($headers);
        $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex);

        // -- Title row --
        $sheet->mergeCells('A1:' . $lastColLetter . '1');
        $sheet->getCellByColumnAndRow(1, 1)->setValue('HASIL TES: ' . mb_strtoupper($bank->title));
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '003E6F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // -- Info row --
        $sheet->mergeCells('A2:' . $lastColLetter . '2');
        $filterInfo = '';
        if ($request->filled('nama')) $filterInfo .= ' | Filter Nama: ' . $request->nama;
        if ($request->filled('bulan')) {
            $bulanNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $filterInfo .= ' | Filter Bulan: ' . ($bulanNames[$request->bulan - 1] ?? $request->bulan);
        }
        if ($request->filled('tanggal')) $filterInfo .= ' | Filter Tanggal: ' . $request->tanggal;
        $sheet->getCellByColumnAndRow(1, 2)->setValue(
            'Tanggal Export: ' . now()->format('d/m/Y H:i') .
            ' | Total Peserta: ' . $responses->count() .
            ' | Total Soal: ' . $totalQuestions .
            ' | Soal Dinilai: ' . $scoreableQuestions .
            ($totalQuestions > $scoreableQuestions ? ' | Soal Narasi/Survei: ' . ($totalQuestions - $scoreableQuestions) : '') .
            $filterInfo
        );
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '475569']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // -- Headers (row 4) --
        $headerRow = 4;
        foreach ($headers as $col => $header) {
            $sheet->getCellByColumnAndRow($col + 1, $headerRow)->setValue($header);
        }

        $headerRange = 'A' . $headerRow . ':' . $lastColLetter . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '003E6F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(50);

        // -- Correct answer reference row (row 5) --
        $refRow = $headerRow + 1;
        $fixedCols = count($fixedHeaders);
        $sheet->getCellByColumnAndRow(1, $refRow)->setValue('');
        $sheet->getCellByColumnAndRow(2, $refRow)->setValue('KUNCI JAWABAN');
        $mergeEndCol = Coordinate::stringFromColumnIndex($fixedCols);
        $sheet->mergeCells('B' . $refRow . ':' . $mergeEndCol . $refRow);

        foreach ($questions as $qIdx => $question) {
            $colIdx = $fixedCols + $qIdx + 1;
            if ($question->type === 'narrative') {
                $sheet->getCellByColumnAndRow($colIdx, $refRow)->setValue('(Narasi)');
            } elseif ($question->type === 'text') {
                $sheet->getCellByColumnAndRow($colIdx, $refRow)->setValue($question->correct_answer_text);
            } else {
                // For MC, show letter + option text
                $optionMap = ['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d];
                $correctLetter = $question->correct_answer;
                $correctText = $optionMap[$correctLetter] ?? '';
                $sheet->getCellByColumnAndRow($colIdx, $refRow)->setValue($correctLetter . '. ' . $correctText);
            }
        }

        $refRange = 'A' . $refRow . ':' . $lastColLetter . $refRow;
        $sheet->getStyle($refRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '065F46']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'A7F3D0']]],
        ]);

        // -- Participant data rows --
        $dataStartRow = $refRow + 1;
        $currentRow = $dataStartRow;

        foreach ($responses as $idx => $resp) {
            $percentage = $scoreableQuestions > 0 ? round(($resp->score / $scoreableQuestions) * 100, 2) : 0;
            $duration = ($resp->started_at && $resp->completed_at)
                ? $resp->started_at->diff($resp->completed_at)->format('%H:%I:%S')
                : '-';
            $keterangan = $percentage >= 70 ? 'BAIK' : ($percentage >= 50 ? 'CUKUP' : 'KURANG');

            $sheet->getCellByColumnAndRow(1, $currentRow)->setValue($idx + 1);
            $sheet->getCellByColumnAndRow(2, $currentRow)->setValue(
                $resp->completed_at ? $resp->completed_at->format('d/m/Y H:i:s') : '-'
            );
            $sheet->getCellByColumnAndRow(3, $currentRow)->setValue($resp->participant_name);

            if ($isCalon) {
                $usia = $resp->birth_date ? \Carbon\Carbon::parse($resp->birth_date)->age . ' tahun' : '-';
                $sheet->getCellByColumnAndRow(4, $currentRow)->setValue($resp->participant_email ?? '-');
                $sheet->getCellByColumnAndRow(5, $currentRow)->setValue($resp->birth_place ?? '-');
                $sheet->getCellByColumnAndRow(6, $currentRow)->setValue($resp->birth_date ? $resp->birth_date->format('d/m/Y') : '-');
                $sheet->getCellByColumnAndRow(7, $currentRow)->setValue($usia);
                $sheet->getCellByColumnAndRow(8, $currentRow)->setValue($resp->score);
                $sheet->getCellByColumnAndRow(9, $currentRow)->setValue($scoreableQuestions);
                $sheet->getCellByColumnAndRow(10, $currentRow)->setValue($percentage);
                $sheet->getCellByColumnAndRow(11, $currentRow)->setValue($keterangan);
                $sheet->getCellByColumnAndRow(12, $currentRow)->setValue($duration);
            } else {
                $sheet->getCellByColumnAndRow(4, $currentRow)->setValue($resp->nik ?? '-');
                $sheet->getCellByColumnAndRow(5, $currentRow)->setValue($resp->participant_email ?? '-');
                $sheet->getCellByColumnAndRow(6, $currentRow)->setValue($resp->phone ?? '-');
                $sheet->getCellByColumnAndRow(7, $currentRow)->setValue($resp->department ?? '-');
                $sheet->getCellByColumnAndRow(8, $currentRow)->setValue($resp->position ?? '-');
                $sheet->getCellByColumnAndRow(9, $currentRow)->setValue($resp->score);
                $sheet->getCellByColumnAndRow(10, $currentRow)->setValue($scoreableQuestions);
                $sheet->getCellByColumnAndRow(11, $currentRow)->setValue($percentage);
                $sheet->getCellByColumnAndRow(12, $currentRow)->setValue($keterangan);
                $sheet->getCellByColumnAndRow(13, $currentRow)->setValue($duration);
            }

            // Answer columns - show full answer text
            $answers = $resp->responses ?? [];
            foreach ($questions as $qIdx => $question) {
                $colIdx = $fixedCols + $qIdx + 1;
                $userAnswer = $answers[$question->id] ?? '';

                if ($question->type === 'narrative') {
                    // Show full narrative text
                    $sheet->getCellByColumnAndRow($colIdx, $currentRow)->setValue((string)$userAnswer);
                } elseif ($question->type === 'text') {
                    $sheet->getCellByColumnAndRow($colIdx, $currentRow)->setValue((string)$userAnswer);

                    // Color code text answers
                    $isCorrect = strtolower(trim((string)$userAnswer)) === strtolower(trim((string)$question->correct_answer_text));
                    if (!empty($userAnswer)) {
                        $cellRef = Coordinate::stringFromColumnIndex($colIdx) . $currentRow;
                        if ($isCorrect) {
                            $sheet->getStyle($cellRef)->getFont()->getColor()->setRGB('065F46');
                            $sheet->getStyle($cellRef)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1FAE5');
                        } else {
                            $sheet->getStyle($cellRef)->getFont()->getColor()->setRGB('991B1B');
                            $sheet->getStyle($cellRef)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                        }
                    }
                } else {
                    // MC: show letter + option text
                    $optionMap = ['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d];
                    $displayAnswer = '';
                    if (!empty($userAnswer) && isset($optionMap[$userAnswer])) {
                        $displayAnswer = $userAnswer . '. ' . $optionMap[$userAnswer];
                    } else {
                        $displayAnswer = (string)$userAnswer;
                    }
                    $sheet->getCellByColumnAndRow($colIdx, $currentRow)->setValue($displayAnswer);

                    // Color code MC answers
                    $isCorrect = $userAnswer === $question->correct_answer;
                    if (!empty($userAnswer)) {
                        $cellRef = Coordinate::stringFromColumnIndex($colIdx) . $currentRow;
                        if ($isCorrect) {
                            $sheet->getStyle($cellRef)->getFont()->getColor()->setRGB('065F46');
                            $sheet->getStyle($cellRef)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1FAE5');
                        } else {
                            $sheet->getStyle($cellRef)->getFont()->getColor()->setRGB('991B1B');
                            $sheet->getStyle($cellRef)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                        }
                    }
                }
            }

            $currentRow++;
        }

        // -- Style data rows --
        if ($currentRow > $dataStartRow) {
            $dataRange = 'A' . $dataStartRow . ':' . $lastColLetter . ($currentRow - 1);
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'font' => ['size' => 10],
            ]);

            // Center-align numeric/short columns
            for ($centerCol = 1; $centerCol <= $fixedCols; $centerCol++) {
                $colLetter = Coordinate::stringFromColumnIndex($centerCol);
                $sheet->getStyle($colLetter . $dataStartRow . ':' . $colLetter . ($currentRow - 1))
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Alternate row colors
            for ($r = $dataStartRow; $r < $currentRow; $r++) {
                if (($r - $dataStartRow) % 2 === 1) {
                    $sheet->getStyle('A' . $r . ':' . $lastColLetter . $r)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    ]);
                }
            }

            // Keterangan column coloring
            $ketColIdx = $isCalon ? $fixedCols - 1 : $fixedCols - 1;
            $ketCol = Coordinate::stringFromColumnIndex($ketColIdx);
            for ($r = $dataStartRow; $r < $currentRow; $r++) {
                $val = $sheet->getCell($ketCol . $r)->getValue();
                if ($val === 'BAIK') {
                    $sheet->getStyle($ketCol . $r)->getFont()->getColor()->setRGB('065F46');
                    $sheet->getStyle($ketCol . $r)->getFont()->setBold(true);
                } elseif ($val === 'CUKUP') {
                    $sheet->getStyle($ketCol . $r)->getFont()->getColor()->setRGB('92400E');
                    $sheet->getStyle($ketCol . $r)->getFont()->setBold(true);
                } else {
                    $sheet->getStyle($ketCol . $r)->getFont()->getColor()->setRGB('991B1B');
                    $sheet->getStyle($ketCol . $r)->getFont()->setBold(true);
                }
            }
        }

        // -- Auto-size fixed columns --
        foreach (range(1, $fixedCols) as $colIdx) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Question columns: wider to show answer text
        for ($qCol = $fixedCols + 1; $qCol <= $lastColIndex; $qCol++) {
            $colLetter = Coordinate::stringFromColumnIndex($qCol);
            $sheet->getColumnDimension($colLetter)->setWidth(30);
        }

        // Freeze header
        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter($headerRange);

        // ===== SHEET 2: Daftar Soal =====
        $questionSheet = $spreadsheet->createSheet();
        $questionSheet->setTitle('Daftar Soal');

        // Headers
        $qHeaders = ['No', 'Tipe Soal', 'Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Jawaban Benar'];
        foreach ($qHeaders as $col => $header) {
            $questionSheet->getCellByColumnAndRow($col + 1, 1)->setValue($header);
        }

        $qLastCol = Coordinate::stringFromColumnIndex(count($qHeaders));
        $questionSheet->getStyle('A1:' . $qLastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '003E6F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $questionSheet->getRowDimension(1)->setRowHeight(25);

        // Question data
        foreach ($questions as $index => $question) {
            $row = $index + 2;
            $typeMap2 = ['narrative' => 'Narasi', 'text' => 'Isian'];
            $typeLabel = isset($typeMap2[$question->type]) ? $typeMap2[$question->type] : 'Pilihan Ganda';

            $questionSheet->getCellByColumnAndRow(1, $row)->setValue($index + 1);
            $questionSheet->getCellByColumnAndRow(2, $row)->setValue($typeLabel);
            $questionSheet->getCellByColumnAndRow(3, $row)->setValue($question->question);

            if ($question->type === 'multiple_choice') {
                $questionSheet->getCellByColumnAndRow(4, $row)->setValue($question->option_a);
                $questionSheet->getCellByColumnAndRow(5, $row)->setValue($question->option_b);
                $questionSheet->getCellByColumnAndRow(6, $row)->setValue($question->option_c);
                $questionSheet->getCellByColumnAndRow(7, $row)->setValue($question->option_d);
                $optionMap = ['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d];
                $questionSheet->getCellByColumnAndRow(8, $row)->setValue($question->correct_answer . '. ' . ($optionMap[$question->correct_answer] ?? ''));
            } elseif ($question->type === 'text') {
                $questionSheet->getCellByColumnAndRow(4, $row)->setValue('-');
                $questionSheet->getCellByColumnAndRow(5, $row)->setValue('-');
                $questionSheet->getCellByColumnAndRow(6, $row)->setValue('-');
                $questionSheet->getCellByColumnAndRow(7, $row)->setValue('-');
                $questionSheet->getCellByColumnAndRow(8, $row)->setValue($question->correct_answer_text);
            } else {
                $questionSheet->getCellByColumnAndRow(4, $row)->setValue('-');
                $questionSheet->getCellByColumnAndRow(5, $row)->setValue('-');
                $questionSheet->getCellByColumnAndRow(6, $row)->setValue('-');
                $questionSheet->getCellByColumnAndRow(7, $row)->setValue('-');
                $questionSheet->getCellByColumnAndRow(8, $row)->setValue('(Tidak Ada - Narasi)');
            }

            // Alternate row colors
            if ($index % 2 === 1) {
                $questionSheet->getStyle('A' . $row . ':' . $qLastCol . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                ]);
            }
        }

        // Style question data
        if ($questions->count() > 0) {
            $questionSheet->getStyle('A2:' . $qLastCol . ($questions->count() + 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'font' => ['size' => 10],
            ]);
        }

        // Auto-size columns
        $questionSheet->getColumnDimension('A')->setWidth(6);
        $questionSheet->getColumnDimension('B')->setWidth(14);
        $questionSheet->getColumnDimension('C')->setWidth(50);
        foreach (['D', 'E', 'F', 'G'] as $col) {
            $questionSheet->getColumnDimension($col)->setWidth(25);
        }
        $questionSheet->getColumnDimension('H')->setWidth(30);

        // ===== SHEET 3 (conditional): Kraepelin =====
        $kraepelinSubTests = $subTests->where('type', 'kraepelin');
        if ($kraepelinSubTests->count() > 0) {
            foreach ($kraepelinSubTests as $kst) {
                $kSheet = $spreadsheet->createSheet();
                $sheetTitle = mb_substr('Kraepelin - ' . $kst->title, 0, 31);
                $kSheet->setTitle($sheetTitle);

                $kConfig = $kst->kraepelin_config ?? [];
                $colCount = $kConfig['columns_count'] ?? 50;

                // Title
                $kLastCol = Coordinate::stringFromColumnIndex(7 + $colCount);
                $kSheet->mergeCells('A1:' . $kLastCol . '1');
                $kSheet->getCellByColumnAndRow(1, 1)->setValue('KRAEPELIN: ' . mb_strtoupper($kst->title));
                $kSheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '5B21B6']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Headers
                $kHeaders = ['No', 'Nama'];
                for ($c = 1; $c <= $colCount; $c++) {
                    $kHeaders[] = 'K' . $c;
                }
                $kHeaders = array_merge($kHeaders, ['Total', 'Kecepatan', 'Ketelitian (%)', 'Ketahanan (%)', 'Stabilitas (SD)', 'Semangat']);

                $kHeaderRow = 3;
                foreach ($kHeaders as $ci => $h) {
                    $kSheet->getCellByColumnAndRow($ci + 1, $kHeaderRow)->setValue($h);
                }
                $kHdrEnd = Coordinate::stringFromColumnIndex(count($kHeaders));
                $kSheet->getStyle('A' . $kHeaderRow . ':' . $kHdrEnd . $kHeaderRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '5B21B6']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);

                // Data rows
                $kRow = $kHeaderRow + 1;
                foreach ($responses as $rIdx => $resp) {
                    $kData = ($resp->responses ?? [])['kraepelin_' . $kst->id] ?? null;
                    $cols = $kData['columns'] ?? [];

                    $correctPerCol = array_map(fn($c) => $c['correct_count'] ?? 0, $cols);
                    $attemptedPerCol = array_map(fn($c) => $c['attempted'] ?? 0, $cols);
                    $total = array_sum($correctPerCol);
                    $totalAttempted = array_sum($attemptedPerCol);
                    $colN = count($cols);

                    $speed = $colN > 0 ? round($total / $colN, 1) : 0;
                    $accuracy = $totalAttempted > 0 ? round(($total / $totalAttempted) * 100, 1) : 0;
                    $third = max(1, intval($colN / 3));
                    $firstThird = $colN > 0 ? array_sum(array_slice($correctPerCol, 0, $third)) / $third : 0;
                    $lastThird = $colN > 0 ? array_sum(array_slice($correctPerCol, -$third)) / $third : 0;
                    $endurance = $firstThird > 0 ? round(($lastThird / $firstThird) * 100, 0) : 0;
                    $mean = $speed;
                    $variance = $colN > 0 ? array_sum(array_map(fn($v) => pow($v - $mean, 2), $correctPerCol)) / $colN : 0;
                    $stdDev = round(sqrt($variance), 1);
                    $motivation = $lastThird > $firstThird ? 'Positif' : ($lastThird < $firstThird ? 'Menurun' : 'Stabil');

                    $kSheet->getCellByColumnAndRow(1, $kRow)->setValue($rIdx + 1);
                    $kSheet->getCellByColumnAndRow(2, $kRow)->setValue($resp->participant_name);

                    for ($ci = 0; $ci < $colCount; $ci++) {
                        $kSheet->getCellByColumnAndRow(3 + $ci, $kRow)->setValue($correctPerCol[$ci] ?? 0);
                    }

                    $metricStart = 3 + $colCount;
                    $kSheet->getCellByColumnAndRow($metricStart, $kRow)->setValue($total);
                    $kSheet->getCellByColumnAndRow($metricStart + 1, $kRow)->setValue($speed);
                    $kSheet->getCellByColumnAndRow($metricStart + 2, $kRow)->setValue($accuracy);
                    $kSheet->getCellByColumnAndRow($metricStart + 3, $kRow)->setValue($endurance);
                    $kSheet->getCellByColumnAndRow($metricStart + 4, $kRow)->setValue($stdDev);
                    $kSheet->getCellByColumnAndRow($metricStart + 5, $kRow)->setValue($motivation);

                    // Alternate rows
                    if ($rIdx % 2 === 1) {
                        $kSheet->getStyle('A' . $kRow . ':' . $kHdrEnd . $kRow)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F3FF']],
                        ]);
                    }

                    $kRow++;
                }

                // Style data
                if ($kRow > $kHeaderRow + 1) {
                    $kSheet->getStyle('A' . ($kHeaderRow + 1) . ':' . $kHdrEnd . ($kRow - 1))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['size' => 9],
                    ]);
                }

                // Column widths
                $kSheet->getColumnDimension('A')->setWidth(5);
                $kSheet->getColumnDimension('B')->setWidth(22);
                for ($ci = 3; $ci <= 2 + $colCount; $ci++) {
                    $kSheet->getColumnDimension(Coordinate::stringFromColumnIndex($ci))->setWidth(5);
                }
                for ($ci = $metricStart; $ci <= $metricStart + 5; $ci++) {
                    $kSheet->getColumnDimension(Coordinate::stringFromColumnIndex($ci))->setWidth(14);
                }

                $kSheet->freezePane('C' . ($kHeaderRow + 1));
            }
        }

        // ===== SHEET (conditional): DISC =====
        $discSubTests = $subTests->where('type', 'disc');
        if ($discSubTests->count() > 0) {
            foreach ($discSubTests as $dst) {
                $dSheet = $spreadsheet->createSheet();
                $dSheetTitle = mb_substr('DISC - ' . $dst->title, 0, 31);
                $dSheet->setTitle($dSheetTitle);

                // Title
                $dSheet->mergeCells('A1:L1');
                $dSheet->getCellByColumnAndRow(1, 1)->setValue('DISC: ' . mb_strtoupper($dst->title));
                $dSheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Headers
                $dHeaders = ['No', 'Nama', 'D (Most)', 'D (Least)', 'I (Most)', 'I (Least)', 'S (Most)', 'S (Least)', 'C (Most)', 'C (Least)', 'Profil'];
                $dHeaderRow = 3;
                foreach ($dHeaders as $ci => $h) {
                    $dSheet->getCellByColumnAndRow($ci + 1, $dHeaderRow)->setValue($h);
                }
                $dHdrEnd = Coordinate::stringFromColumnIndex(count($dHeaders));
                $dSheet->getStyle('A' . $dHeaderRow . ':' . $dHdrEnd . $dHeaderRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);

                // Data rows
                $dRow = $dHeaderRow + 1;
                foreach ($responses as $rIdx => $resp) {
                    $discData = ($resp->responses ?? [])['disc_' . $dst->id] ?? null;
                    $dScores = $discData['scores'] ?? ['D' => ['most' => 0, 'least' => 0], 'I' => ['most' => 0, 'least' => 0], 'S' => ['most' => 0, 'least' => 0], 'C' => ['most' => 0, 'least' => 0]];
                    $dProfile = $discData['profile_type'] ?? '-';

                    $dSheet->getCellByColumnAndRow(1, $dRow)->setValue($rIdx + 1);
                    $dSheet->getCellByColumnAndRow(2, $dRow)->setValue($resp->participant_name);
                    $dSheet->getCellByColumnAndRow(3, $dRow)->setValue($dScores['D']['most'] ?? 0);
                    $dSheet->getCellByColumnAndRow(4, $dRow)->setValue($dScores['D']['least'] ?? 0);
                    $dSheet->getCellByColumnAndRow(5, $dRow)->setValue($dScores['I']['most'] ?? 0);
                    $dSheet->getCellByColumnAndRow(6, $dRow)->setValue($dScores['I']['least'] ?? 0);
                    $dSheet->getCellByColumnAndRow(7, $dRow)->setValue($dScores['S']['most'] ?? 0);
                    $dSheet->getCellByColumnAndRow(8, $dRow)->setValue($dScores['S']['least'] ?? 0);
                    $dSheet->getCellByColumnAndRow(9, $dRow)->setValue($dScores['C']['most'] ?? 0);
                    $dSheet->getCellByColumnAndRow(10, $dRow)->setValue($dScores['C']['least'] ?? 0);
                    $dSheet->getCellByColumnAndRow(11, $dRow)->setValue($dProfile);

                    if ($rIdx % 2 === 1) {
                        $dSheet->getStyle('A' . $dRow . ':' . $dHdrEnd . $dRow)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDFA']],
                        ]);
                    }

                    $dRow++;
                }

                // Style data
                if ($dRow > $dHeaderRow + 1) {
                    $dSheet->getStyle('A' . ($dHeaderRow + 1) . ':' . $dHdrEnd . ($dRow - 1))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['size' => 9],
                    ]);
                }

                // Column widths
                $dSheet->getColumnDimension('A')->setWidth(5);
                $dSheet->getColumnDimension('B')->setWidth(22);
                for ($ci = 3; $ci <= 10; $ci++) {
                    $dSheet->getColumnDimension(Coordinate::stringFromColumnIndex($ci))->setWidth(12);
                }
                $dSheet->getColumnDimension('K')->setWidth(14);

                $dSheet->freezePane('C' . ($dHeaderRow + 1));
            }
        }

        // ===== SHEET (conditional): PAPIKOSTIK =====
        $papiSubTests = $subTests->where('type', 'papikostik');
        if ($papiSubTests->count() > 0) {
            $papiDims = ['N','G','A','L','P','I','T','V','S','R','D','C','X','B','O','Z','E','K','F','W'];
            foreach ($papiSubTests as $pst) {
                $pSheet = $spreadsheet->createSheet();
                $pSheetTitle = mb_substr('PAPI - ' . $pst->title, 0, 31);
                $pSheet->setTitle($pSheetTitle);

                $pLastCol = Coordinate::stringFromColumnIndex(2 + count($papiDims));
                $pSheet->mergeCells('A1:' . $pLastCol . '1');
                $pSheet->getCellByColumnAndRow(1, 1)->setValue('PAPIKOSTIK: ' . mb_strtoupper($pst->title));
                $pSheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $pHeaders = array_merge(['No', 'Nama'], $papiDims);
                $pHeaderRow = 3;
                foreach ($pHeaders as $ci => $h) {
                    $pSheet->getCellByColumnAndRow($ci + 1, $pHeaderRow)->setValue($h);
                }
                $pHdrEnd = Coordinate::stringFromColumnIndex(count($pHeaders));
                $pSheet->getStyle('A' . $pHeaderRow . ':' . $pHdrEnd . $pHeaderRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);

                $pRow = $pHeaderRow + 1;
                foreach ($responses as $rIdx => $resp) {
                    $papiData = ($resp->responses ?? [])['papikostik_' . $pst->id] ?? null;
                    $pScores = $papiData['scores'] ?? [];

                    $pSheet->getCellByColumnAndRow(1, $pRow)->setValue($rIdx + 1);
                    $pSheet->getCellByColumnAndRow(2, $pRow)->setValue($resp->participant_name);

                    foreach ($papiDims as $di => $dim) {
                        $pSheet->getCellByColumnAndRow(3 + $di, $pRow)->setValue($pScores[$dim] ?? 0);
                    }

                    if ($rIdx % 2 === 1) {
                        $pSheet->getStyle('A' . $pRow . ':' . $pHdrEnd . $pRow)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F3FF']],
                        ]);
                    }
                    $pRow++;
                }

                if ($pRow > $pHeaderRow + 1) {
                    $pSheet->getStyle('A' . ($pHeaderRow + 1) . ':' . $pHdrEnd . ($pRow - 1))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['size' => 9],
                    ]);
                }

                $pSheet->getColumnDimension('A')->setWidth(5);
                $pSheet->getColumnDimension('B')->setWidth(22);
                for ($ci = 3; $ci <= 2 + count($papiDims); $ci++) {
                    $pSheet->getColumnDimension(Coordinate::stringFromColumnIndex($ci))->setWidth(6);
                }
                $pSheet->freezePane('C' . ($pHeaderRow + 1));
            }
        }

        // Set active sheet back to results
        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'hasil_tes_' . str_replace(' ', '_', $bank->title) . '_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'results');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        ActivityLog::log('export', 'bank', 'Export hasil tes bank: ' . $bank->title);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Show cheat/violation log for all banks (recruitment feature).
     */
    public function cheatLog(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get all shared bank IDs
        if (optional($user)->isSuperAdmin() || optional($user)->isRecruitmentTeam()) {
            $sharedUserIds = \App\Models\User::whereIn('role', ['superadmin', 'recruitmentteam'])->pluck('id');
            $bankIds = Bank::whereIn('user_id', $sharedUserIds)->pluck('id');
        } else {
            $bankIds = Bank::where('user_id', $user->id)->pluck('id');
        }

        $query = ParticipantResponse::whereIn('bank_id', $bankIds)
            ->where('completed', true)
            ->where('violation_count', '>', 0)
            ->with('bank');

        // Filters
        if ($request->filled('nama')) {
            $query->where('participant_name', 'like', '%' . $request->nama . '%');
        }
        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->bank_id);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('completed_at', $request->bulan);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('completed_at', $request->tanggal);
        }

        $violations = $query->orderBy('violation_count', 'desc')->orderBy('completed_at', 'desc')->get();
        $banks = Bank::whereIn('id', $bankIds)->orderBy('title')->get();

        return view('banks.cheat-log', compact('violations', 'banks'));
    }

    /**
     * Helper: store image from cell content which may be data URI or base64 or URL/path.
     * Returns stored relative path on 'public' disk or null.
     */
    private function storeImageFromCell($cellValue, $destDir)
    {
        if (empty($cellValue)) return null;
        $val = trim($cellValue);

        // Data URI: data:{mime};base64,{data}
        if (preg_match('/^data:(image\/[a-zA-Z0-9+.-]+);base64,(.+)$/', $val, $m)) {
            $mime = $m[1];
            $b64 = $m[2];
            try {
                $data = base64_decode($b64);
            } catch (\Exception $e) {
                return null;
            }
            $ext = explode('/', $mime)[1] ?? 'png';
            $fileName = $destDir . '/' . uniqid() . '.' . $ext;
            Storage::disk('public')->put($fileName, $data);
            return $fileName;
        }

        // If it's an absolute URL, try to fetch (best-effort)
        if (preg_match('/^https?:\/\//', $val)) {
            try {
                $context = stream_context_create(['http' => ['timeout' => 5]]);
                $raw = @file_get_contents($val, false, $context);
                if ($raw !== false) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_buffer($finfo, $raw);
                    finfo_close($finfo);
                    $ext = 'png';
                    if (preg_match('/image\/(\w+)/', $mime, $mm)) $ext = $mm[1];
                    $fileName = $destDir . '/' . uniqid() . '.' . $ext;
                    Storage::disk('public')->put($fileName, $raw);
                    return $fileName;
                }
            } catch (\Exception $e) {
                return null;
            }
        }

        // If it's a local storage path (relative), try to move/copy from storage path
        if (strpos($val, 'storage/') === 0 || strpos($val, 'public/') === 0 || preg_match('/^[\w\-\/]+\.(png|jpe?g|gif)$/i', $val)) {
            // best-effort: if file exists in project, copy into public storage
            $candidate = base_path($val);
            if (!file_exists($candidate)) {
                // try storage path
                $candidate = storage_path('app/public/' . ltrim($val, '/'));
            }
            if (file_exists($candidate)) {
                $ext = pathinfo($candidate, PATHINFO_EXTENSION) ?: 'png';
                $fileName = $destDir . '/' . uniqid() . '.' . $ext;
                Storage::disk('public')->put($fileName, file_get_contents($candidate));
                return $fileName;
            }
        }

        return null;
    }

    // Download XLSX template for question import per type
    public function downloadQuestionTemplate(Bank $bank, $type)
    {
        $this->authorize('update', $bank);

        $type = strtolower($type);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($type === 'multiple_choice' || $type === 'pilgan') {
            $headers = ['sub_test_title', 'type', 'question', 'image', 'option_a', 'option_a_image', 'option_b', 'option_b_image', 'option_c', 'option_c_image', 'option_d', 'option_d_image', 'option_e', 'option_e_image', 'option_f', 'option_f_image', 'correct_answer', 'is_example'];
            $example = ['', 'multiple_choice', 'Contoh: Apa warna langit?', '<base64 or URL>', 'Biru', '<base64 or URL>', 'Merah', '', 'Kuning', '', 'Hijau', '', '', '', '', '', 'A', '0'];
        } elseif ($type === 'text' || $type === 'isian') {
            $headers = ['sub_test_title', 'type', 'question', 'image', 'correct_answer_text', 'is_example'];
            $example = ['', 'text', 'Contoh: Sebutkan ibu kota Indonesia?', '<base64 or URL>', 'Jakarta', '0'];
        } elseif ($type === 'survey') {
            $headers = ['sub_test_title', 'type', 'question', 'image', 'option_count', 'option_a', 'option_a_image', 'option_b', 'option_b_image', 'option_c', 'option_c_image', 'option_d', 'option_d_image', 'option_e', 'option_e_image', 'option_f', 'option_f_image', 'is_example'];
            $example = ['', 'survey', 'Contoh: Seberapa puas Anda?', '<base64 or URL>', 4, 'Sangat Puas', '', 'Puas', '', 'Cukup', '', 'Tidak Puas', '', '', '', '', '', '0'];
        } elseif ($type === 'narrative') {
            $headers = ['sub_test_title', 'type', 'question', 'image', 'is_example'];
            $example = ['', 'narrative', 'Contoh: Jelaskan pengalaman kerja Anda.', '<base64 or URL>', '0'];
        } else {
            abort(400, 'Tipe template tidak dikenali');
        }

        // Write headers
        foreach ($headers as $col => $h) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $h);
        }
        // Example row
        foreach ($example as $col => $v) {
            $sheet->setCellValueByColumnAndRow($col + 1, 2, $v);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'questions_template_' . $type . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

    // Export all questions for a bank as XLSX (flat list)
    public function exportQuestions(Bank $bank)
    {
        $this->authorize('view', $bank);

        // Load questions (include sub-tests)
        $subTests = $bank->subTests()->with(['questions'])->get();
        if ($subTests->count() > 0) {
            $questions = collect();
            foreach ($subTests as $st) {
                $questions = $questions->merge($st->questions);
            }
        } else {
            $questions = $bank->questions()->orderBy('order')->get();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Soal');

        $headers = ['type', 'sub_test_title', 'question', 'option_count', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'option_f', 'correct_answer', 'correct_answer_text', 'is_example'];
        foreach ($headers as $col => $h) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $h);
        }

        foreach ($questions as $i => $q) {
            $row = $i + 2;
            $typeLabel = $q->type;
            $sheet->setCellValueByColumnAndRow(1, $row, $typeLabel);
            $sheet->setCellValueByColumnAndRow(2, $row, $q->subTest ? $q->subTest->title : '');
            $sheet->setCellValueByColumnAndRow(3, $row, $q->question);
            $sheet->setCellValueByColumnAndRow(4, $row, $q->option_count);
            $sheet->setCellValueByColumnAndRow(5, $row, $q->option_a);
            $sheet->setCellValueByColumnAndRow(6, $row, $q->option_b);
            $sheet->setCellValueByColumnAndRow(7, $row, $q->option_c);
            $sheet->setCellValueByColumnAndRow(8, $row, $q->option_d);
            $sheet->setCellValueByColumnAndRow(9, $row, $q->option_e);
            $sheet->setCellValueByColumnAndRow(10, $row, $q->option_f);
            $sheet->setCellValueByColumnAndRow(11, $row, $q->correct_answer);
            $sheet->setCellValueByColumnAndRow(12, $row, $q->correct_answer_text);
            $sheet->setCellValueByColumnAndRow(13, $row, $q->is_example ? 1 : 0);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'questions');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        ActivityLog::log('export', 'question_list', 'Export daftar soal bank: ' . $bank->title);

        $fileName = 'daftar_soal_' . str_replace(' ', '_', $bank->title) . '_' . date('Y-m-d_His') . '.xlsx';
        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])->deleteFileAfterSend(true);
    }

    // Import questions from uploaded XLSX/CSV following template headers
    public function importQuestions(Request $request, Bank $bank)
    {
        $this->authorize('update', $bank);

        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $path = $request->file('file')->getPathname();
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return back()->with('error', 'File kosong atau tidak ada baris data.');
        }

        // Map headers from first row (column letter => header name)
        $headerRow = $rows[1];
        $colMap = [];
        foreach ($headerRow as $colLetter => $colVal) {
            $key = strtolower(trim($colVal));
            if ($key !== '') $colMap[$colLetter] = $key;
        }

        $created = 0;
        $errors = [];

        for ($r = 2; $r <= count($rows); $r++) {
            $row = $rows[$r];
            // Build associative data by header key
            $data = [];
            foreach ($colMap as $colLetter => $key) {
                $data[$key] = isset($row[$colLetter]) ? trim((string)$row[$colLetter]) : '';
            }

            if (empty($data['type']) || empty($data['question'])) {
                // skip blank rows
                continue;
            }

            $type = strtolower($data['type']);

            // Find or create sub-test if provided
            $subTestId = null;
            if (!empty($data['sub_test_title'])) {
                $title = $data['sub_test_title'];
                $subTest = SubTest::firstOrCreate(
                    ['bank_id' => $bank->id, 'title' => $title],
                    ['description' => null, 'duration_minutes' => null, 'order' => ($bank->subTests()->max('order') ?? -1) + 1]
                );
                $subTestId = $subTest->id;
            }

            // Determine order for this sub_test
            $maxOrder = Question::where('sub_test_id', $subTestId)->max('order');
            $order = ($maxOrder === null ? -1 : $maxOrder) + 1;

            try {
                $payload = [
                    'bank_id' => $bank->id,
                    'sub_test_id' => $subTestId,
                    'question' => $data['question'] ?? '',
                    'option_a' => $data['option_a'] ?? null,
                    'option_b' => $data['option_b'] ?? null,
                    'option_c' => $data['option_c'] ?? null,
                    'option_d' => $data['option_d'] ?? null,
                    'option_e' => $data['option_e'] ?? null,
                    'option_f' => $data['option_f'] ?? null,
                    'image' => $data['image'] ?? null,
                    'option_a_image' => $data['option_a_image'] ?? null,
                    'option_b_image' => $data['option_b_image'] ?? null,
                    'option_c_image' => $data['option_c_image'] ?? null,
                    'option_d_image' => $data['option_d_image'] ?? null,
                    'option_e_image' => $data['option_e_image'] ?? null,
                    'option_f_image' => $data['option_f_image'] ?? null,
                    'option_count' => isset($data['option_count']) ? (int)$data['option_count'] : null,
                    'correct_answer' => isset($data['correct_answer']) ? strtoupper($data['correct_answer']) : null,
                    'correct_answer_text' => $data['correct_answer_text'] ?? null,
                    'order' => $order,
                    'type' => in_array($type, ['multiple_choice','pilgan','text','survey','narrative']) ? ($type === 'pilgan' ? 'multiple_choice' : $type) : 'multiple_choice',
                    'is_example' => (!empty($data['is_example']) && in_array($data['is_example'], ['1','true','yes'])) ? 1 : 0,
                ];

                // Normalize type-specific fields
                if ($payload['type'] === 'narrative') {
                    $payload['option_a'] = $payload['option_b'] = $payload['option_c'] = $payload['option_d'] = $payload['option_e'] = $payload['option_f'] = null;
                    $payload['option_count'] = null;
                    $payload['correct_answer'] = null;
                    $payload['correct_answer_text'] = null;
                    // handle question image if provided (data URI)
                    if (!empty($data['image']) && is_string($data['image'])) {
                        $imgPath = $this->storeImageFromCell($data['image'], 'questions/images');
                        if ($imgPath) {
                            $payload['image'] = $imgPath;
                            $payload['image_data'] = Storage::disk('public')->get($imgPath);
                            $payload['image_mime'] = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $payload['image_data']);
                        }
                    }
                } elseif ($payload['type'] === 'text') {
                    $payload['option_a'] = $payload['option_b'] = $payload['option_c'] = $payload['option_d'] = $payload['option_e'] = $payload['option_f'] = null;
                    $payload['option_count'] = null;
                    // ensure correct_answer_text exists
                    $payload['correct_answer_text'] = $payload['correct_answer_text'] ?? '';
                    $payload['correct_answer'] = null;
                    if (!empty($data['image']) && is_string($data['image'])) {
                        $imgPath = $this->storeImageFromCell($data['image'], 'questions/images');
                        if ($imgPath) {
                            $payload['image'] = $imgPath;
                            $payload['image_data'] = Storage::disk('public')->get($imgPath);
                            $payload['image_mime'] = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $payload['image_data']);
                        }
                    }
                } elseif ($payload['type'] === 'survey') {
                    // option_count must be between 2 and 6
                    $oc = (int)($payload['option_count'] ?? 2);
                    if ($oc < 2) $oc = 2;
                    if ($oc > 6) $oc = 6;
                    $payload['option_count'] = $oc;
                    // null out unused options
                    $labels = ['option_a','option_b','option_c','option_d','option_e','option_f'];
                    for ($i = $oc; $i < 6; $i++) {
                        $payload[$labels[$i]] = null;
                    }
                    $payload['correct_answer'] = null;
                    $payload['correct_answer_text'] = null;
                    // store question image
                    if (!empty($data['image']) && is_string($data['image'])) {
                        $imgPath = $this->storeImageFromCell($data['image'], 'questions/images');
                        if ($imgPath) $payload['image'] = $imgPath;
                    }
                    // option images
                    foreach (['option_a_image','option_b_image','option_c_image','option_d_image','option_e_image','option_f_image'] as $optImg) {
                        if (!empty($data[$optImg]) && is_string($data[$optImg])) {
                            $p = $this->storeImageFromCell($data[$optImg], 'questions/option-images');
                            if ($p) $payload[$optImg] = $p;
                        }
                    }
                } else {
                    // multiple_choice
                    $payload['option_e'] = $payload['option_e'] ?? null;
                    $payload['option_f'] = $payload['option_f'] ?? null;
                    $payload['option_count'] = null;
                    // correct_answer should be one of A,B,C,D,E,F
                    if (!empty($payload['correct_answer'])) {
                        $ca = strtoupper(substr($payload['correct_answer'],0,1));
                        if (!in_array($ca, ['A','B','C','D','E','F'])) $ca = null;
                        $payload['correct_answer'] = $ca;
                    }
                    // handle question image and option images for MC
                    if (!empty($data['image']) && is_string($data['image'])) {
                        $imgPath = $this->storeImageFromCell($data['image'], 'questions/images');
                        if ($imgPath) {
                            $payload['image'] = $imgPath;
                            $payload['image_data'] = Storage::disk('public')->get($imgPath);
                            $payload['image_mime'] = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $payload['image_data']);
                        }
                    }
                    foreach (['option_a_image','option_b_image','option_c_image','option_d_image','option_e_image','option_f_image'] as $optImg) {
                        if (!empty($data[$optImg]) && is_string($data[$optImg])) {
                            $p = $this->storeImageFromCell($data[$optImg], 'questions/option-images');
                            if ($p) $payload[$optImg] = $p;
                        }
                    }
                }

                Question::create($payload);
                $created++;
            } catch (\Exception $e) {
                $errors[] = 'Baris ' . $r . ': ' . $e->getMessage();
            }
        }

        return redirect()->route('banks.edit', $bank)->with('success', $created . ' soal berhasil diimport.')->with('import_errors', $errors);

        ActivityLog::log('import', 'question_bulk', 'Import soal ke bank: ' . $bank->title . ' — ' . $created . ' dibuat');

        $msg = $created . ' soal berhasil diimport.';
        if (!empty($errors)) {
            $msg .= ' Terdapat ' . count($errors) . ' error.';
            return redirect()->route('banks.edit', $bank)->with('warning', $msg)->with('import_errors', $errors);
        }

        return redirect()->route('banks.edit', $bank)->with('success', $msg);
    }
}
