<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WarningLetter;
use App\Models\Bank;
use App\Models\ParticipantResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== EMPLOYEE STATS =====
        // Superadmin and Internal HR should see the full employee database.
        $isGlobalEmployeeView = auth()->user()->isSuperAdmin() || auth()->user()->isInternalHR();

        $totalEmployees = Employee::when(!$isGlobalEmployeeView, function($q) {
            $q->where('user_id', Auth::id());
        })->count();

        $employeesByDept = Employee::when(!$isGlobalEmployeeView, function($q) {
                $q->where('user_id', Auth::id());
            })
            ->selectRaw('dept, COUNT(*) as count')
            ->whereNotNull('dept')->where('dept', '!=', '')
            ->groupBy('dept')
            ->orderByDesc('count')
            ->pluck('count', 'dept');

        $employeesByStatus = Employee::when(!$isGlobalEmployeeView, function($q) {
                $q->where('user_id', Auth::id());
            })
            ->selectRaw('COALESCE(status_aktif, "Tidak Diketahui") as status_label, COUNT(*) as count')
            ->groupBy('status_label')
            ->pluck('count', 'status_label');

        $employeesByPendidikan = Employee::when(!$isGlobalEmployeeView, function($q) {
                $q->where('user_id', Auth::id());
            })
            ->selectRaw('COALESCE(pendidikan, "Tidak Diketahui") as pendidikan_label, COUNT(*) as count')
            ->whereNotNull('pendidikan')->where('pendidikan', '!=', '')
            ->groupBy('pendidikan_label')
            ->orderByDesc('count')
            ->pluck('count', 'pendidikan_label');

        $employeesByStatusKaryawan = Employee::when(!$isGlobalEmployeeView, function($q) {
                $q->where('user_id', Auth::id());
            })
            ->selectRaw('COALESCE(status_karyawan, "Tidak Diketahui") as sk_label, COUNT(*) as count')
            ->whereNotNull('status_karyawan')->where('status_karyawan', '!=', '')
            ->groupBy('sk_label')
            ->orderByDesc('count')
            ->pluck('count', 'sk_label');

        // ===== WARNING LETTER STATS =====
        $totalSP = WarningLetter::count();
        $spPending = WarningLetter::where('status', 'pending')->count();
        $spPendingHR = WarningLetter::where('status', 'pending_hr')->count();
        $spApproved = WarningLetter::where('status', 'approved')->count();

        // SP per bulan (last 12 months) grouped by sp_level
        $spMonthly = WarningLetter::selectRaw("
                DATE_FORMAT(tanggal_surat, '%Y-%m') as bulan,
                sp_level,
                COUNT(*) as count
            ")
            ->where('tanggal_surat', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan', 'sp_level')
            ->orderBy('bulan')
            ->get();

        // Build 12-month labels
        $monthLabels = [];
        $sp1Monthly = [];
        $sp2Monthly = [];
        $sp3Monthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $monthLabels[] = now()->subMonths($i)->translatedFormat('M Y');
            $sp1Monthly[] = $spMonthly->where('bulan', $m)->where('sp_level', 1)->first()->count ?? 0;
            $sp2Monthly[] = $spMonthly->where('bulan', $m)->where('sp_level', 2)->first()->count ?? 0;
            $sp3Monthly[] = $spMonthly->where('bulan', $m)->where('sp_level', 3)->first()->count ?? 0;
        }

        // SP per departemen
        $spByDept = WarningLetter::selectRaw('departemen, COUNT(*) as count')
            ->whereNotNull('departemen')->where('departemen', '!=', '')
            ->groupBy('departemen')
            ->orderByDesc('count')
            ->pluck('count', 'departemen');

        // SP level distribution
        $spByLevel = WarningLetter::selectRaw('sp_level, COUNT(*) as count')
            ->groupBy('sp_level')
            ->orderBy('sp_level')
            ->pluck('count', 'sp_level');

        // SP status distribution
        $spByStatus = WarningLetter::selectRaw("
                CASE status
                    WHEN 'pending' THEN 'Pending'
                    WHEN 'pending_hr' THEN 'Menunggu HR'
                    WHEN 'approved' THEN 'Approved'
                    ELSE status
                END as status_label,
                COUNT(*) as count
            ")
            ->groupBy('status')
            ->pluck('count', 'status_label');

        // SP terbaru (5 terakhir)
        $recentSP = WarningLetter::with('approver')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ===== PSIKOTEST STATS =====
        $totalPsikotest = Bank::count();

        return view('dashboard', compact(
            'totalEmployees',
            'employeesByDept', 'employeesByStatus', 'employeesByPendidikan', 'employeesByStatusKaryawan',
            'totalSP', 'spPending', 'spPendingHR', 'spApproved',
            'monthLabels', 'sp1Monthly', 'sp2Monthly', 'sp3Monthly',
            'spByDept', 'spByLevel', 'spByStatus', 'recentSP',
            'totalPsikotest'
        ));
    }

    /**
     * Dashboard khusus recruitment team — visualisasi data psikotest
     */
    public function recruitmentDashboard()
    {
        // Get shared bank IDs (same logic as BankController)
        $sharedUserIds = User::whereIn('role', ['superadmin', 'recruitmentteam'])->pluck('id');
        $bankIds = Bank::whereIn('user_id', $sharedUserIds)->pluck('id');

        // ===== SUMMARY CARDS =====
        $totalBanks = Bank::whereIn('user_id', $sharedUserIds)->count();
        $activeBanks = Bank::whereIn('user_id', $sharedUserIds)->where('is_active', true)->count();
        $totalParticipants = ParticipantResponse::whereIn('bank_id', $bankIds)->count();
        $completedParticipants = ParticipantResponse::whereIn('bank_id', $bankIds)->where('completed', true)->count();

        // ===== MINI STATS =====
        $participantsToday = ParticipantResponse::whereIn('bank_id', $bankIds)
            ->whereDate('created_at', today())->count();
        $participantsThisMonth = ParticipantResponse::whereIn('bank_id', $bankIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $completionRate = $totalParticipants > 0
            ? round(($completedParticipants / $totalParticipants) * 100, 1)
            : 0;

        // ===== MONTHLY TREND (last 6 months) =====
        $monthLabels = [];
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabels[] = $date->translatedFormat('M Y');
            $monthlyData[] = ParticipantResponse::whereIn('bank_id', $bankIds)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        // ===== POSITION DISTRIBUTION =====
        $positionStats = ParticipantResponse::whereIn('bank_id', $bankIds)
            ->selectRaw("COALESCE(NULLIF(position, ''), 'Tidak Diketahui') as pos, COUNT(*) as count")
            ->groupBy('pos')
            ->orderByDesc('count')
            ->get();
        $positionLabels = $positionStats->pluck('pos')->toArray();
        $positionData = $positionStats->pluck('count')->toArray();

        // ===== PARTICIPANTS PER BANK SOAL =====
        $bankStats = Bank::whereIn('banks.user_id', $sharedUserIds)
            ->leftJoin('participant_responses', 'banks.id', '=', 'participant_responses.bank_id')
            ->selectRaw('banks.title, COUNT(participant_responses.id) as count')
            ->groupBy('banks.id', 'banks.title')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        $bankLabels = $bankStats->pluck('title')->toArray();
        $bankData = $bankStats->pluck('count')->toArray();

        // ===== RECENT PARTICIPANTS =====
        $recentParticipants = ParticipantResponse::whereIn('bank_id', $bankIds)
            ->with('bank')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // ===== VIOLATION / CHEAT ANALYTICS =====
        $allCompleted = ParticipantResponse::whereIn('bank_id', $bankIds)
            ->where('completed', true)->get();

        // Total peserta curang (violation > 0)
        $totalCheaters = $allCompleted->where('violation_count', '>', 0)->count();
        $totalClean = $allCompleted->where('violation_count', 0)->count();
        $cheatRate = $allCompleted->count() > 0
            ? round(($totalCheaters / $allCompleted->count()) * 100, 1) : 0;

        // Forced submit (auto-submit karena batas pelanggaran)
        $forcedSubmits = $allCompleted->filter(function ($r) {
            $note = $r->anti_cheat_note ?? '';
            return stripos($note, 'batas pelanggaran') !== false || stripos($note, 'tercapai') !== false;
        })->count();

        // Distribusi level kecurangan
        $cheatLevelLabels = ['Bersih (0)', 'Ringan (1-2)', 'Sedang (3-4)', 'Berat (5+)'];
        $cheatLevelData = [
            $allCompleted->where('violation_count', 0)->count(),
            $allCompleted->whereBetween('violation_count', [1, 2])->count(),
            $allCompleted->whereBetween('violation_count', [3, 4])->count(),
            $allCompleted->where('violation_count', '>=', 5)->count(),
        ];

        // Jenis pelanggaran breakdown (parse violation_log)
        $violationTypes = [];
        foreach ($allCompleted as $resp) {
            $logs = $resp->violation_log;
            if (!is_array($logs)) continue;
            foreach ($logs as $entry) {
                $type = isset($entry['type']) ? $entry['type'] : 'Unknown';
                // Simplify type names
                if (stripos($type, 'berpindah tab') !== false || stripos($type, 'meninggalkan halaman') !== false) {
                    $key = 'Pindah Tab / Halaman';
                } elseif (stripos($type, 'meninggalkan jendela') !== false) {
                    $key = 'Keluar Jendela';
                } elseif (stripos($type, 'Copy') !== false || stripos($type, 'copy') !== false) {
                    $key = 'Copy';
                } elseif (stripos($type, 'Cut') !== false || stripos($type, 'cut') !== false) {
                    $key = 'Cut';
                } elseif (stripos($type, 'Paste') !== false || stripos($type, 'paste') !== false) {
                    $key = 'Paste';
                } elseif (stripos($type, 'Screenshot') !== false || stripos($type, 'PrintScreen') !== false || stripos($type, 'Snipping') !== false) {
                    $key = 'Screenshot';
                } elseif (stripos($type, 'Print') !== false) {
                    $key = 'Print';
                } elseif (stripos($type, 'Screen Sharing') !== false) {
                    $key = 'Screen Sharing';
                } elseif (stripos($type, 'home/app switch') !== false || stripos($type, 'aplikasi lain') !== false) {
                    $key = 'App Switch (Mobile)';
                } else {
                    $key = 'Lainnya';
                }
                if (!isset($violationTypes[$key])) $violationTypes[$key] = 0;
                $violationTypes[$key]++;
            }
        }
        arsort($violationTypes);
        $violationTypeLabels = array_keys($violationTypes);
        $violationTypeData = array_values($violationTypes);

        // Tren kecurangan per bulan (6 bulan terakhir)
        $cheatTrendLabels = [];
        $cheatTrendTotal = [];
        $cheatTrendCheaters = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $cheatTrendLabels[] = $date->translatedFormat('M Y');
            $monthResp = $allCompleted->filter(function ($r) use ($date) {
                return $r->completed_at
                    && $r->completed_at->month == $date->month
                    && $r->completed_at->year == $date->year;
            });
            $cheatTrendTotal[] = $monthResp->count();
            $cheatTrendCheaters[] = $monthResp->where('violation_count', '>', 0)->count();
        }

        // Top 10 pelanggar terbanyak
        $topViolators = $allCompleted->where('violation_count', '>', 0)
            ->sortByDesc('violation_count')
            ->take(10)
            ->values();

        // Rata-rata pelanggaran per peserta yang curang
        $avgViolation = $totalCheaters > 0
            ? round($allCompleted->where('violation_count', '>', 0)->avg('violation_count'), 1) : 0;

        // Korelasi skor vs pelanggaran (for scatter)
        $scatterData = $allCompleted->map(function ($r) {
            return ['x' => (int)$r->violation_count, 'y' => (int)$r->score];
        })->values()->toArray();

        return view('recruitment-dashboard', compact(
            'totalBanks', 'activeBanks', 'totalParticipants', 'completedParticipants',
            'participantsToday', 'participantsThisMonth', 'completionRate',
            'monthLabels', 'monthlyData',
            'positionLabels', 'positionData',
            'bankLabels', 'bankData',
            'recentParticipants',
            'totalCheaters', 'totalClean', 'cheatRate', 'forcedSubmits', 'avgViolation',
            'cheatLevelLabels', 'cheatLevelData',
            'violationTypeLabels', 'violationTypeData',
            'cheatTrendLabels', 'cheatTrendTotal', 'cheatTrendCheaters',
            'topViolators', 'scatterData'
        ));
    }
}
