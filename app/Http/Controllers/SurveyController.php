<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveySection;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveyAnswer;
use App\Models\ActivityLog;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SurveyController extends Controller
{
    // ─── INDEX ──────────────────────────────────────────────
    public function index()
    {
        $surveys = Survey::withCount('responses', 'questions')
            ->orderByDesc('created_at')
            ->get();

        return view('surveys.index', compact('surveys'));
    }

    // ─── CREATE ─────────────────────────────────────────────
    public function create()
    {
        return view('surveys.create');
    }

    // ─── STORE ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'is_anonymous' => 'nullable|boolean',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'sections'     => 'required|array|min:1',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.description' => 'nullable|string',
            'sections.*.questions' => 'required|array|min:1',
            'sections.*.questions.*.question' => 'required|string',
            'sections.*.questions.*.type' => 'required|in:scale,multiple_choice,text',
            'sections.*.questions.*.options' => 'nullable|array',
            'sections.*.questions.*.is_required' => 'nullable|boolean',
        ]);

        $survey = Survey::create([
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'start_date'   => $validated['start_date'] ?? null,
            'end_date'     => $validated['end_date'] ?? null,
            'status'       => 'draft',
            'created_by'   => auth()->id(),
        ]);

        // Create sections with questions
        foreach ($validated['sections'] as $sIdx => $section) {
            $sec = SurveySection::create([
                'survey_id'   => $survey->id,
                'title'       => $section['title'],
                'description' => $section['description'] ?? null,
                'order'       => $sIdx,
            ]);

            foreach ($section['questions'] as $qIdx => $q) {
                SurveyQuestion::create([
                    'survey_id'    => $survey->id,
                    'section_id'   => $sec->id,
                    'type'         => $q['type'],
                    'question'     => $q['question'],
                    'options'      => $q['type'] === 'multiple_choice' ? ($q['options'] ?? []) : null,
                    'is_required'  => $q['is_required'] ?? true,
                    'order'        => $qIdx,
                ]);
            }
        }

        ActivityLog::log('create', 'survey', 'Membuat survey: ' . $survey->title);

        return redirect()->route('surveys.index')->with('success', 'Survey berhasil dibuat!');
    }

    // ─── EDIT ───────────────────────────────────────────────
    public function edit(Survey $survey)
    {
        $survey->load('sections.questions');
        return view('surveys.edit', compact('survey'));
    }

    // ─── UPDATE ─────────────────────────────────────────────
    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'is_anonymous' => 'nullable|boolean',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'sections'     => 'required|array|min:1',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.description' => 'nullable|string',
            'sections.*.questions' => 'required|array|min:1',
            'sections.*.questions.*.question' => 'required|string',
            'sections.*.questions.*.type' => 'required|in:scale,multiple_choice,text',
            'sections.*.questions.*.options' => 'nullable|array',
            'sections.*.questions.*.is_required' => 'nullable|boolean',
        ]);

        $survey->update([
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'start_date'   => $validated['start_date'] ?? null,
            'end_date'     => $validated['end_date'] ?? null,
        ]);

        // Delete all old sections and questions
        $survey->sections()->delete();

        // Create new sections with questions
        foreach ($validated['sections'] as $sIdx => $section) {
            $sec = SurveySection::create([
                'survey_id'   => $survey->id,
                'title'       => $section['title'],
                'description' => $section['description'] ?? null,
                'order'       => $sIdx,
            ]);

            foreach ($section['questions'] as $qIdx => $q) {
                SurveyQuestion::create([
                    'survey_id'    => $survey->id,
                    'section_id'   => $sec->id,
                    'type'         => $q['type'],
                    'question'     => $q['question'],
                    'options'      => $q['type'] === 'multiple_choice' ? ($q['options'] ?? []) : null,
                    'is_required'  => $q['is_required'] ?? true,
                    'order'        => $qIdx,
                ]);
            }
        }

        ActivityLog::log('update', 'survey', 'Mengupdate survey: ' . $survey->title);

        return redirect()->route('surveys.index')->with('success', 'Survey berhasil diupdate!');
    }

    // ─── DESTROY ────────────────────────────────────────────
    public function destroy(Survey $survey)
    {
        ActivityLog::log('delete', 'survey', 'Menghapus survey: ' . $survey->title);
        $survey->delete();
        return redirect()->route('surveys.index')->with('success', 'Survey berhasil dihapus!');
    }

    // ─── RESULTS ────────────────────────────────────────────
    public function results(Survey $survey)
    {
        $survey->load(['questions.answers', 'responses.answers']);

        // Compute stats per question
        $results = [];
        foreach ($survey->questions as $q) {
            $answers = $q->answers;
            $stat = ['question' => $q, 'total' => $answers->count()];

            if ($q->type === 'scale') {
                $values = $answers->pluck('scale_value')->filter();
                $stat['average'] = $values->count() ? round($values->avg(), 2) : 0;
                $stat['distribution'] = [];
                for ($v = 1; $v <= 5; $v++) {
                    $stat['distribution'][$v] = $values->filter(fn($x) => $x == $v)->count();
                }
            } elseif ($q->type === 'multiple_choice') {
                $choices = $answers->pluck('choice_value')->filter();
                $stat['choices'] = $choices->countBy()->sortDesc();
            } else {
                $stat['texts'] = $answers->pluck('text_value')->filter()->values();
            }

            $results[] = $stat;
        }

        return view('surveys.results', compact('survey', 'results'));
    }

    // ─── TOGGLE STATUS ──────────────────────────────────────
    public function toggleStatus(Survey $survey, Request $request)
    {
        $newStatus = $request->input('status', 'active');
        $survey->update(['status' => $newStatus]);
        return back()->with('success', 'Status survey diubah menjadi ' . $newStatus);
    }

    // ─── DELETE SINGLE RESPONSE ──────────────────────────────
    public function deleteResponse(Survey $survey, SurveyResponse $response)
    {
        if ($response->survey_id !== $survey->id) {
            abort(404);
        }
        $response->answers()->delete();
        $response->delete();
        ActivityLog::log('delete', 'survey_response', 'Menghapus responden dari survey: ' . $survey->title);
        return back()->with('success', 'Responden berhasil dihapus.');
    }

    // ─── BULK DELETE RESPONSES ───────────────────────────────
    public function bulkDeleteResponses(Survey $survey, Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada responden yang dipilih.');
        }
        $responses = SurveyResponse::where('survey_id', $survey->id)->whereIn('id', $ids)->get();
        foreach ($responses as $resp) {
            $resp->answers()->delete();
            $resp->delete();
        }
        ActivityLog::log('delete', 'survey_response', 'Bulk delete ' . $responses->count() . ' responden dari survey: ' . $survey->title);
        return back()->with('success', $responses->count() . ' responden berhasil dihapus.');
    }

    // ─── PUBLIC: FILL FORM ──────────────────────────────────
    public function fill($token)
    {
        $survey = Survey::where('token', $token)->with(['sections.questions', 'questions'])->firstOrFail();

        if (!$survey->isActive()) {
            return view('surveys.closed', compact('survey'));
        }

        $departments = Department::orderBy('name')->get();
        return view('surveys.fill', compact('survey', 'departments'));
    }

    // ─── PUBLIC: SUBMIT ─────────────────────────────────────
    public function submit($token, Request $request)
    {
        $survey = Survey::where('token', $token)->with(['sections.questions', 'questions'])->firstOrFail();

        if (!$survey->isActive()) {
            return redirect()->back()->with('error', 'Survey sudah ditutup.');
        }

        $rules = [];
        $messages = [];
        if (!$survey->is_anonymous) {
            $rules['respondent_name'] = 'required|string|max:255';
            $messages['respondent_name.required'] = 'Nama responden wajib diisi.';
            $rules['respondent_department'] = 'nullable|string|max:255';
            $rules['respondent_nik'] = 'nullable|string|max:50';
        }

        foreach ($survey->questions as $q) {
            $key = 'answers.' . $q->id;
            if ($q->is_required) {
                $rules[$key] = 'required';
                $label = Str::limit($q->question, 50);
                $messages[$key . '.required'] = "Pertanyaan \"{$label}\" wajib dijawab.";
            } else {
                $rules[$key] = 'nullable';
            }
        }

        $validated = $request->validate($rules, $messages);

        $response = SurveyResponse::create([
            'survey_id'            => $survey->id,
            'respondent_name'      => $request->input('respondent_name'),
            'respondent_department' => $request->input('respondent_department'),
            'respondent_nik'       => $request->input('respondent_nik'),
        ]);

        $answersData = $request->input('answers', []);
        foreach ($survey->questions as $q) {
            $val = $answersData[$q->id] ?? null;
            if ($val === null && !$q->is_required) continue;

            SurveyAnswer::create([
                'survey_response_id' => $response->id,
                'survey_question_id' => $q->id,
                'scale_value'        => $q->type === 'scale' ? (int)$val : null,
                'choice_value'       => $q->type === 'multiple_choice' ? $val : null,
                'text_value'         => $q->type === 'text' ? $val : null,
            ]);
        }

        return view('surveys.thankyou', compact('survey'));
    }

    // ─── EXPORT EXCEL ───────────────────────────────────────
    public function exportExcel(Survey $survey)
    {
        $survey->load(['questions', 'responses.answers']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Survey Results');

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '003E6F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        // Build header row
        $col = 'A';
        $sheet->setCellValue($col . '1', '#');
        $sheet->getColumnDimension($col)->setWidth(5);
        $col++;

        if (!$survey->is_anonymous) {
            $sheet->setCellValue($col . '1', 'Nama');
            $sheet->getColumnDimension($col)->setWidth(20);
            $col++;
            $sheet->setCellValue($col . '1', 'Departemen');
            $sheet->getColumnDimension($col)->setWidth(20);
            $col++;
            $sheet->setCellValue($col . '1', 'NIK');
            $sheet->getColumnDimension($col)->setWidth(15);
            $col++;
        }

        $sheet->setCellValue($col . '1', 'Tanggal Submit');
        $sheet->getColumnDimension($col)->setWidth(18);
        $col++;

        $questionCols = [];
        foreach ($survey->questions as $q) {
            $sheet->setCellValue($col . '1', $q->question);
            $sheet->getColumnDimension($col)->setWidth(30);
            $questionCols[$q->id] = $col;
            $lastCol = $col;
            $col++;
        }

        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Data rows
        $row = 2;
        $cellBorder = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]]];

        foreach ($survey->responses as $i => $resp) {
            $col = 'A';
            $sheet->setCellValue($col . $row, $i + 1);
            $col++;

            if (!$survey->is_anonymous) {
                $sheet->setCellValue($col . $row, $resp->respondent_name ?? '-');
                $col++;
                $sheet->setCellValue($col . $row, $resp->respondent_department ?? '-');
                $col++;
                $sheet->setCellValue($col . $row, $resp->respondent_nik ?? '-');
                $col++;
            }

            $sheet->setCellValue($col . $row, $resp->created_at->format('d/m/Y H:i'));
            $col++;

            foreach ($survey->questions as $q) {
                $answer = $resp->answers->where('survey_question_id', $q->id)->first();
                $val = '-';
                if ($answer) {
                    if ($q->type === 'scale') $val = $answer->scale_value;
                    elseif ($q->type === 'multiple_choice') $val = $answer->choice_value;
                    else $val = $answer->text_value;
                }
                $sheet->setCellValue($questionCols[$q->id] . $row, $val);
            }

            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray($cellBorder);

            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $row++;
        }

        // Summary sheet
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        $summarySheet->setCellValue('A1', 'Pertanyaan');
        $summarySheet->setCellValue('B1', 'Tipe');
        $summarySheet->setCellValue('C1', 'Total Jawaban');
        $summarySheet->setCellValue('D1', 'Rata-rata / Jawaban Terbanyak');
        $summarySheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $summarySheet->getColumnDimension('A')->setWidth(40);
        $summarySheet->getColumnDimension('B')->setWidth(18);
        $summarySheet->getColumnDimension('C')->setWidth(16);
        $summarySheet->getColumnDimension('D')->setWidth(30);

        $sRow = 2;
        foreach ($survey->questions as $q) {
            $answers = $q->answers;
            $summarySheet->setCellValue('A' . $sRow, $q->question);

            $typeLabel = match($q->type) {
                'scale' => 'Skala 1-5',
                'multiple_choice' => 'Pilihan Ganda',
                'text' => 'Isian',
            };
            $summarySheet->setCellValue('B' . $sRow, $typeLabel);
            $summarySheet->setCellValue('C' . $sRow, $answers->count());

            if ($q->type === 'scale') {
                $avg = $answers->pluck('scale_value')->filter()->avg();
                $summarySheet->setCellValue('D' . $sRow, $avg ? round($avg, 2) : '-');
            } elseif ($q->type === 'multiple_choice') {
                $top = $answers->pluck('choice_value')->filter()->countBy()->sortDesc()->keys()->first();
                $summarySheet->setCellValue('D' . $sRow, $top ?? '-');
            } else {
                $summarySheet->setCellValue('D' . $sRow, $answers->count() . ' jawaban teks');
            }
            $sRow++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'survey_' . \Illuminate\Support\Str::slug($survey->title) . '_' . date('Ymd') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'survey');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
