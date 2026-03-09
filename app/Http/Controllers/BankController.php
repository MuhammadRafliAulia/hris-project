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

        $validated = $request->validate([
            'sub_test_title' => 'required|string|max:255',
            'sub_test_description' => 'nullable|string|max:1000',
            'sub_test_duration' => 'nullable|integer|min:1|max:600',
        ]);

        $order = $bank->subTests()->max('order') ?? -1;
        SubTest::create([
            'bank_id' => $bank->id,
            'title' => $validated['sub_test_title'],
            'description' => $validated['sub_test_description'] ?? null,
            'duration_minutes' => $validated['sub_test_duration'] ?? null,
            'order' => $order + 1,
        ]);

        ActivityLog::log('create', 'subtest', 'Menambahkan sub-test ke bank: ' . $bank->title);
        return redirect()->route('banks.edit', $bank)->with('success', 'Sub-test berhasil ditambahkan.');
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
