<?php

namespace App\Http\Controllers;

use App\Models\ParticipantResponse;
use App\Models\Bank;
use App\Models\SubTest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    /**
     * Show registration / biodata form for a bank (shared link).
     */
    public function register($slug)
    {
        $bank = Bank::where('slug', $slug)->firstOrFail();

        if (!$bank->is_active) {
            abort(403, 'Tes ini sudah ditutup oleh admin.');
        }

        $departments = \App\Models\Department::orderBy('name')->get();
        if ($bank->target === 'calon_karyawan') {
            // show only credential entry page first
            return view('test.verify', compact('bank', 'slug'));
        }
        return view('test.form', compact('bank', 'slug', 'departments'));
    }

    // Show biodata form after credential verification (for calon_karyawan)
    public function biodata($slug)
    {
        $bank = Bank::where('slug', $slug)->firstOrFail();
        if ($bank->target !== 'calon_karyawan') abort(404);
        $departments = \App\Models\Department::orderBy('name')->get();
        // require that credential id is present in session
        if (!session()->has('applicant_credential_id')) {
            return redirect()->route('test.register', $slug)->withErrors(['applicant_username' => 'Silakan verifikasi kredensial terlebih dahulu.']);
        }
        return view('test.biodata', compact('bank', 'slug', 'departments'));
    }

    // Verify credential (calon_karyawan) and redirect to biodata
    public function verify(Request $request, $slug)
    {
        $bank = Bank::where('slug', $slug)->firstOrFail();
        if ($bank->target !== 'calon_karyawan') abort(404);

        $validated = $request->validate([
            'applicant_username' => 'required|string|max:255',
            'applicant_password' => 'required|string|min:4|max:100',
        ]);

        $cred = \App\Models\ApplicantCredential::where('bank_id', $bank->id)
            ->where('username', $validated['applicant_username'])
            ->where('used', false)
            ->first();

        if (!$cred) {
            return back()->withErrors(['applicant_username' => 'Kredensial tidak ditemukan atau sudah dipakai.'])->withInput();
        }

        try {
            $plain = Crypt::decryptString($cred->password_encrypted);
        } catch (\Exception $e) {
            return back()->withErrors(['applicant_username' => 'Kredensial rusak.'])->withInput();
        }

        if ($validated['applicant_password'] !== $plain) {
            return back()->withErrors(['applicant_password' => 'Password salah.'])->withInput();
        }

        // store credential id and plain password temporarily in session
        session(['applicant_credential_id' => $cred->id, 'applicant_plain_password' => $plain]);

        return redirect()->route('test.biodata', $slug);
    }

    /**
     * Submit biodata, create participant response, redirect to test.
     */
    public function start(Request $request, $slug)
    {
        $bank = Bank::where('slug', $slug)->firstOrFail();

        if (!$bank->is_active) {
            abort(403, 'Tes ini sudah ditutup oleh admin.');
        }

        // Validation differs by target
        if ($bank->target === 'calon_karyawan') {
            $rules = [
                'participant_name' => 'required|string|max:255',
                'participant_email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:2000',
            ];
        } else {
            $rules = [
                'nik' => 'required|string|max:50',
                'participant_name' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'participant_email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
            ];
        }

        $validated = $request->validate($rules);

        // For calon_karyawan, require credential verification previously stored in session
        if ($bank->target === 'calon_karyawan') {
            if (!session()->has('applicant_credential_id') || !session()->has('applicant_plain_password')) {
                return redirect()->route('test.register', $slug)->withErrors(['applicant_username' => 'Silakan verifikasi kredensial terlebih dahulu.']);
            }
            $credId = session('applicant_credential_id');
            $cred = \App\Models\ApplicantCredential::find($credId);
            if (!$cred || $cred->bank_id !== $bank->id || $cred->used) {
                return redirect()->route('test.register', $slug)->withErrors(['applicant_username' => 'Kredensial tidak valid atau sudah dipakai.']);
            }
        }

        // Check resume: for calon_karyawan use email, otherwise use NIK
        if ($bank->target === 'calon_karyawan') {
            $existing = ParticipantResponse::where('bank_id', $bank->id)
                ->where('participant_email', $validated['participant_email'])
                ->where('completed', false)
                ->first();
        } else {
            $existing = ParticipantResponse::where('bank_id', $bank->id)
                ->where('nik', $validated['nik'])
                ->where('completed', false)
                ->first();
        }

        if ($existing) {
            // Resume existing session
            return redirect()->route('test.show', $existing->token);
        }

        // Create new participant response
        $token = Str::random(32);
        if ($bank->target === 'calon_karyawan') {
            $createData = [
                'bank_id' => $bank->id,
                'participant_name' => $validated['participant_name'],
                'participant_email' => $validated['participant_email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'token' => $token,
                'started_at' => now(),
            ];

            // attach credential and mark used
            $cred->used = true;
            $cred->save();
            $createData['applicant_username'] = $cred->username;
            $createData['applicant_password'] = Hash::make(session('applicant_plain_password'));
            // clear session temp
            session()->forget(['applicant_credential_id', 'applicant_plain_password']);
        } else {
            $createData = [
                'bank_id' => $bank->id,
                'nik' => $validated['nik'],
                'participant_name' => $validated['participant_name'],
                'participant_email' => $validated['participant_email'],
                'phone' => $validated['phone'],
                'department' => $validated['department'],
                'position' => $validated['position'],
                'token' => $token,
                'started_at' => now(),
            ];
        }

        // If a record with same bank_id + participant_email already exists (possibly completed),
        // reset and reuse it instead of attempting to insert a duplicate (which would fail
        // due to the unique constraint). This allows participants to re-start even when
        // an earlier completed record exists.
        $existingAny = null;
        if (!empty($createData['participant_email'])) {
            $existingAny = ParticipantResponse::where('bank_id', $bank->id)
                ->where('participant_email', $createData['participant_email'])
                ->first();
        }

        if ($existingAny) {
            $existingAny->token = $token;
            $existingAny->participant_name = $createData['participant_name'] ?? $existingAny->participant_name;
            $existingAny->phone = $createData['phone'] ?? $existingAny->phone;
            $existingAny->address = $createData['address'] ?? $existingAny->address;
            $existingAny->nik = $createData['nik'] ?? $existingAny->nik;
            $existingAny->department = $createData['department'] ?? $existingAny->department;
            $existingAny->position = $createData['position'] ?? $existingAny->position;
            $existingAny->responses = null;
            $existingAny->score = 0;
            $existingAny->completed = false;
            $existingAny->started_at = now();
            $existingAny->completed_at = null;
            if (isset($createData['applicant_username'])) $existingAny->applicant_username = $createData['applicant_username'];
            if (isset($createData['applicant_password'])) $existingAny->applicant_password = $createData['applicant_password'];
            $existingAny->save();
        } else {
            ParticipantResponse::create($createData);
        }

        return redirect()->route('test.show', $token);
    }

    /**
     * Show test questions page.
     */
    public function show($token)
    {
        $response = ParticipantResponse::where('token', $token)->firstOrFail();
        $bank = $response->bank;

        if (!$bank->is_active) {
            abort(403, 'Tes ini sudah ditutup oleh admin.');
        }

        // Already completed → thank you page
        if ($response->completed) {
            return redirect()->route('test.thankyou', $token);
        }

        // Check if time has expired (auto-submit)
        if ($bank->duration_minutes) {
            $deadline = $response->started_at->copy()->addMinutes($bank->duration_minutes);
            if (now()->greaterThanOrEqualTo($deadline)) {
                if (!$response->completed) {
                    $this->autoSubmit($response);
                }
                return redirect()->route('test.thankyou', $token);
            }
            $remainingSeconds = now()->diffInSeconds($deadline, false);
        } else {
            $remainingSeconds = null;
        }

        $questions = $bank->questions;

        // Load sub-tests with their questions and example questions
        $subTests = $bank->subTests()->with(['questions', 'exampleQuestions'])->get();
        $hasSubTests = $subTests->count() > 0;

        return view('test.show', compact('response', 'bank', 'questions', 'remainingSeconds', 'subTests', 'hasSubTests'));
    }

    /**
     * Submit test answers - score and save.
     */
    public function submit(Request $request, $token)
    {
        $response = ParticipantResponse::where('token', $token)->firstOrFail();

        // Prevent resubmission
        if ($response->completed) {
            return redirect()->route('test.thankyou', $token);
        }

        // Accept answers - may be empty on auto-submit
        $answers = $request->input('answers', []);

        // Get only real questions (not examples) for scoring
        $bank = $response->bank;
        $subTests = $bank->subTests;
        if ($subTests->count() > 0) {
            // Sub-test mode: only score non-example questions from sub-tests
            $questions = collect();
            foreach ($subTests as $st) {
                $questions = $questions->merge($st->questions); // questions() already filters is_example=false
            }
        } else {
            $questions = $bank->questions;
        }

        $score = 0;
        $responsesData = [];

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $responsesData[$question->id] = $userAnswer;

            // Skip scoring for narrative and survey questions (no correct answer)
            if ($question->type === 'narrative' || $question->type === 'survey') {
                continue;
            }

            if ($question->type === 'text') {
                if ($userAnswer && strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer_text))) {
                    $score++;
                }
            } else {
                if ($userAnswer === $question->correct_answer) {
                    $score++;
                }
            }
        }

        // Collect anti-cheat violation data
        $violationCount = (int) $request->input('violation_count', 0);
        $violationLog = $request->input('violation_log') ? json_decode($request->input('violation_log'), true) : [];
        $antiCheatNote = $request->input('anti_cheat_note');

        try {
            $response->update([
                'responses' => $responsesData,
                'score' => $score,
                'completed' => true,
                'completed_at' => now(),
                'violation_count' => $violationCount,
                'violation_log' => $violationLog,
                'anti_cheat_note' => $antiCheatNote,
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan hasil tes: ' . $e->getMessage());
        }

        return redirect()->route('test.thankyou', $token);
    }

    /**
     * Auto-submit when time expires (no answers provided by user).
     */
    private function autoSubmit(ParticipantResponse $response)
    {
        $bank = $response->bank;
        $subTests = $bank->subTests;
        if ($subTests->count() > 0) {
            $questions = collect();
            foreach ($subTests as $st) {
                $questions = $questions->merge($st->questions);
            }
        } else {
            $questions = $bank->questions;
        }

        $responsesData = [];
        $score = 0;

        // Fill empty answers
        foreach ($questions as $question) {
            $responsesData[$question->id] = null;
        }

        $response->update([
            'responses' => $responsesData,
            'score' => $score,
            'completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Thank you page after test completion.
     */
    public function thankyou($token)
    {
        $response = ParticipantResponse::where('token', $token)->firstOrFail();

        if (!$response->completed) {
            return redirect()->route('test.show', $token);
        }

        return view('test.thankyou');
    }
}
