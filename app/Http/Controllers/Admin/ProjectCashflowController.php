<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectCashflowExport;
use App\Http\Controllers\Controller;
use App\Models\CashflowOption;
use App\Models\Project;
use App\Models\ProjectCashflow;
use App\Models\TransactionCategory;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProjectCashflowController extends Controller
{
    /**
     * Resolusi parameter filter periode bersama untuk Keuangan Umum & Proyek.
     *
     * @return array<string, mixed>
     */
    private function resolvePeriodFilters(Request $request): array
    {
        $filters = $request->validate([
            'period_mode' => ['nullable', 'string', 'in:lengkap,bulan,tahun,range,month,year'],
            'preset' => ['nullable', 'string', 'in:today,this_month,this_year,all,custom_date,custom_month'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2099'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'start_month' => ['nullable', 'integer', 'between:1,12'],
            'end_month' => ['nullable', 'integer', 'between:1,12'],
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $periodMode = $request->query('period_mode');
        $preset = $request->query('preset');
        $selectedYear = ! empty($filters['year']) ? (int) $filters['year'] : (int) now()->year;
        $selectedMonth = ! empty($filters['month']) ? (int) $filters['month'] : null;

        $startDate = null;
        $endDate = null;
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        // Evaluasi 3 Bagian Periode: Lengkap, Bulan, Tahun
        if ($periodMode === 'lengkap' || $periodMode === 'range' || $request->filled('start_date') || $request->filled('end_date')) {
            $activeMode = 'lengkap';
            $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
            $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;
            $startStr = $startDate ? $startDate->format('d/m/Y') : 'Awal';
            $endStr = $endDate ? $endDate->format('d/m/Y') : 'Sekarang';
            $periodTitle = "Rentang: {$startStr} s/d {$endStr}";
        } elseif ($periodMode === 'tahun' || $periodMode === 'year' || ($request->filled('year') && empty($request->query('month')) && empty($periodMode) && empty($preset))) {
            $activeMode = 'tahun';
            $selectedMonth = null;
            $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
            $periodTitle = 'Tahun ' . $selectedYear . ' (Semua Bulan)';
        } elseif ($preset === 'today') {
            $activeMode = 'lengkap';
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $periodTitle = 'Hari Ini (' . now()->translatedFormat('d M Y') . ')';
        } elseif ($preset === 'all') {
            $activeMode = 'tahun';
            $startDate = null;
            $endDate = null;
            $selectedMonth = null;
            $periodTitle = 'Semua Periode';
        } else {
            // Mode 'bulan' (Default: Bulan Ini atau Bulan + Tahun yang dipilih)
            $activeMode = 'bulan';
            if ($selectedMonth === null && ! $request->has('month')) {
                $selectedMonth = (int) now()->month;
            }

            if ($selectedMonth) {
                $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
                $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
                $periodTitle = ($monthNames[$selectedMonth] ?? 'Bulan ' . $selectedMonth) . ' ' . $selectedYear;
            } else {
                $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
                $endDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
                $periodTitle = 'Tahun ' . $selectedYear . ' (Semua Bulan)';
            }
        }

        $years = ProjectCashflow::pluck('transaction_date')
            ->map(fn ($d) => (int) ($d instanceof CarbonInterface ? $d->year : Carbon::parse($d)->year))
            ->push((int) now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        return [
            'activeMode' => $activeMode,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodTitle' => $periodTitle,
            'years' => $years,
            'monthNames' => $monthNames,
            'preset' => $preset,
        ];
    }

    /**
     * Halaman 1: Overview Keuangan Umum (Konsolidasi Finansial Perusahaan)
     */
    public function general(Request $request): View
    {
        Gate::authorize('viewAny', ProjectCashflow::class);

        $period = $this->resolvePeriodFilters($request);
        $activeMode = $period['activeMode'];
        $selectedYear = $period['selectedYear'];
        $selectedMonth = $period['selectedMonth'];
        $startDate = $period['startDate'];
        $endDate = $period['endDate'];
        $periodTitle = $period['periodTitle'];
        $years = $period['years'];
        $monthNames = $period['monthNames'];
        $preset = $period['preset'];

        $allProjects = Project::with('user')->orderBy('nama_proyek')->orderBy('id')->get();
        $projectIds = $allProjects->pluck('id');
        $allCashflows = ProjectCashflow::with('category')->whereIn('project_id', $projectIds)->get();
        $cashflowsByProject = $allCashflows->groupBy('project_id');

        $grandPeriodIncome = BigDecimal::of('0.00');
        $grandPeriodExpense = BigDecimal::of('0.00');
        $grandLifetimeBalance = BigDecimal::of('0.00');

        $monthlyMatrix = [];
        $matrixMonthTotals = array_fill(1, 12, BigDecimal::of('0.00'));
        $matrixGrandIncome = BigDecimal::of('0.00');

        $categoryIncomeTotals = [];
        $categoryExpenseTotals = [];
        $totalPendingCount = 0;

        foreach ($allProjects as $project) {
            $cfs = $cashflowsByProject->get($project->id, collect());
            $pIncome = BigDecimal::of('0.00');
            $pExpense = BigDecimal::of('0.00');
            $pLifetimeBalance = BigDecimal::of('0.00');

            $projectMonthly = array_fill(1, 12, BigDecimal::of('0.00'));
            $projectYearIncome = BigDecimal::of('0.00');

            foreach ($cfs as $cf) {
                if ($cf->status === 'ditolak') {
                    continue;
                }
                if ($cf->status === 'pending') {
                    $totalPendingCount++;
                }
                $isLunas = in_array($cf->status, ['lunas', null], true);
                if (! $isLunas) {
                    continue;
                }

                $cfTotal = BigDecimal::of($cf->total);
                $date = $cf->transaction_date;
                $cfDate = $date instanceof CarbonInterface ? $date : Carbon::parse($date);
                $m = (int) $cfDate->month;
                $y = (int) $cfDate->year;

                if ($cf->type === 'penerimaan') {
                    $pLifetimeBalance = $pLifetimeBalance->plus($cfTotal);
                } else {
                    $pLifetimeBalance = $pLifetimeBalance->minus($cfTotal);
                }

                if ($y === $selectedYear && $cf->type === 'penerimaan') {
                    $projectMonthly[$m] = $projectMonthly[$m]->plus($cfTotal);
                    $projectYearIncome = $projectYearIncome->plus($cfTotal);
                    $matrixMonthTotals[$m] = $matrixMonthTotals[$m]->plus($cfTotal);
                    $matrixGrandIncome = $matrixGrandIncome->plus($cfTotal);
                }

                $matchPeriod = ($startDate === null || $cfDate->gte($startDate->startOfDay()))
                    && ($endDate === null || $cfDate->lte($endDate->endOfDay()));

                if ($matchPeriod) {
                    $catName = $cf->category?->name ?? ($cf->type === 'penerimaan' ? 'Pembayaran Klien' : 'Operasional');
                    if ($cf->type === 'penerimaan') {
                        $pIncome = $pIncome->plus($cfTotal);
                        $categoryIncomeTotals[$catName] = ($categoryIncomeTotals[$catName] ?? BigDecimal::of('0.00'))->plus($cfTotal);
                    } else {
                        $pExpense = $pExpense->plus($cfTotal);
                        $categoryExpenseTotals[$catName] = ($categoryExpenseTotals[$catName] ?? BigDecimal::of('0.00'))->plus($cfTotal);
                    }
                }
            }

            $grandPeriodIncome = $grandPeriodIncome->plus($pIncome);
            $grandPeriodExpense = $grandPeriodExpense->plus($pExpense);
            $grandLifetimeBalance = $grandLifetimeBalance->plus($pLifetimeBalance);

            $monthlyMatrix[] = [
                'project' => $project,
                'months' => array_map(fn ($bd) => (string) $bd, $projectMonthly),
                'year_income' => (string) $projectYearIncome,
            ];
        }

        $grandPeriodBalance = $grandPeriodIncome->minus($grandPeriodExpense);
        $grandPeriodIncomeFloat = $grandPeriodIncome->toFloat();
        $grandPeriodBalanceFloat = $grandPeriodBalance->toFloat();
        $grandMargin = $grandPeriodIncomeFloat > 0 ? round(($grandPeriodBalanceFloat / $grandPeriodIncomeFloat) * 100, 1) : null;

        $incomeBreakdown = [];
        $incomeTotalFloat = $grandPeriodIncome->toFloat();
        foreach ($categoryIncomeTotals as $cat => $amountBd) {
            $amtFloat = $amountBd->toFloat();
            $percent = $incomeTotalFloat > 0 ? round(($amtFloat / $incomeTotalFloat) * 100, 1) : 0;
            $incomeBreakdown[] = [
                'name' => $cat,
                'amount' => (string) $amountBd,
                'percent' => $percent,
            ];
        }
        usort($incomeBreakdown, fn ($a, $b) => $b['percent'] <=> $a['percent']);

        $expenseBreakdown = [];
        $expenseTotalFloat = $grandPeriodExpense->toFloat();
        foreach ($categoryExpenseTotals as $cat => $amountBd) {
            $amtFloat = $amountBd->toFloat();
            $percent = $expenseTotalFloat > 0 ? round(($amtFloat / $expenseTotalFloat) * 100, 1) : 0;
            $expenseBreakdown[] = [
                'name' => $cat,
                'amount' => (string) $amountBd,
                'percent' => $percent,
            ];
        }
        usort($expenseBreakdown, fn ($a, $b) => $b['percent'] <=> $a['percent']);

        $expenseRatio = $incomeTotalFloat > 0 ? min(round(($expenseTotalFloat / $incomeTotalFloat) * 100, 1), 100) : 0;

        return view('admin.cashbook.general', [
            'monthlyMatrix' => $monthlyMatrix,
            'matrixMonthTotals' => array_map(fn ($bd) => (string) $bd, $matrixMonthTotals),
            'matrixGrandIncome' => (string) $matrixGrandIncome,
            'grandPeriodIncome' => (string) $grandPeriodIncome,
            'grandPeriodExpense' => (string) $grandPeriodExpense,
            'grandPeriodBalance' => (string) $grandPeriodBalance,
            'grandLifetimeBalance' => (string) $grandLifetimeBalance,
            'grandMargin' => $grandMargin,
            'incomeBreakdown' => $incomeBreakdown,
            'expenseBreakdown' => $expenseBreakdown,
            'expenseRatio' => $expenseRatio,
            'totalPendingCount' => $totalPendingCount,
            'totalProjectsCount' => $allProjects->count(),
            'periodTitle' => $periodTitle,
            'activeMode' => $activeMode,
            'filterMode' => $activeMode,
            'preset' => $preset,
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'months' => $monthNames,
            'years' => $years,
        ]);
    }

    /**
     * Halaman 2: Daftar Keuangan Proyek (Pengelolaan Arus Kas per Proyek)
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ProjectCashflow::class);

        $period = $this->resolvePeriodFilters($request);
        $activeMode = $period['activeMode'];
        $selectedYear = $period['selectedYear'];
        $selectedMonth = $period['selectedMonth'];
        $startDate = $period['startDate'];
        $endDate = $period['endDate'];
        $periodTitle = $period['periodTitle'];
        $years = $period['years'];
        $monthNames = $period['monthNames'];
        $preset = $period['preset'];

        $search = is_string($request->query('search')) ? trim($request->query('search')) : '';
        $statusFilter = is_string($request->query('status')) ? trim($request->query('status')) : '';

        $query = Project::with('user');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_proyek', 'like', "%{$search}%")
                    ->orWhere('alamat_proyek', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        $allFilteredProjects = $query->orderBy('nama_proyek')->orderBy('id')->get();
        $projectIds = $allFilteredProjects->pluck('id');
        $allCashflows = ProjectCashflow::with('category')->whereIn('project_id', $projectIds)->get();
        $cashflowsByProject = $allCashflows->groupBy('project_id');

        $projectsData = [];
        $totalPendingCount = 0;
        $grandPeriodIncome = BigDecimal::of('0.00');
        $grandPeriodExpense = BigDecimal::of('0.00');
        $grandPeriodReceivable = BigDecimal::of('0.00');
        $grandLifetimeBalance = BigDecimal::of('0.00');

        foreach ($allFilteredProjects as $project) {
            $cfs = $cashflowsByProject->get($project->id, collect());
            $pIncome = BigDecimal::of('0.00');
            $pExpense = BigDecimal::of('0.00');
            $pReceivable = BigDecimal::of('0.00');
            $pLifetimeBalance = BigDecimal::of('0.00');
            $pLifetimeIncome = BigDecimal::of('0.00');

            foreach ($cfs as $cf) {
                if ($cf->status === 'ditolak') {
                    continue;
                }

                $cfTotal = BigDecimal::of($cf->total);
                $date = $cf->transaction_date;
                $cfDate = $date instanceof CarbonInterface ? $date : Carbon::parse($date);
                $matchPeriod = ($startDate === null || $cfDate->gte($startDate->startOfDay()))
                    && ($endDate === null || $cfDate->lte($endDate->endOfDay()));

                if ($cf->status === 'pending' || $cf->status === 'piutang') {
                    $totalPendingCount++;
                    if ($matchPeriod) {
                        $pReceivable = $pReceivable->plus($cfTotal);
                    }
                }

                $isLunas = in_array($cf->status, ['lunas', null], true);
                if (! $isLunas) {
                    continue;
                }

                if ($cf->type === 'penerimaan') {
                    $pLifetimeBalance = $pLifetimeBalance->plus($cfTotal);
                    $pLifetimeIncome = $pLifetimeIncome->plus($cfTotal);
                } else {
                    $pLifetimeBalance = $pLifetimeBalance->minus($cfTotal);
                }

                if ($matchPeriod) {
                    if ($cf->type === 'penerimaan') {
                        $pIncome = $pIncome->plus($cfTotal);
                    } else {
                        $pExpense = $pExpense->plus($cfTotal);
                    }
                }
            }

            $pBalance = $pIncome->minus($pExpense);
            $grandPeriodIncome = $grandPeriodIncome->plus($pIncome);
            $grandPeriodExpense = $grandPeriodExpense->plus($pExpense);
            $grandPeriodReceivable = $grandPeriodReceivable->plus($pReceivable);
            $grandLifetimeBalance = $grandLifetimeBalance->plus($pLifetimeBalance);

            $lifetimeIncomeFloat = $pLifetimeIncome->toFloat();
            $lifetimeBalanceFloat = $pLifetimeBalance->toFloat();
            $lifetimeMargin = $lifetimeIncomeFloat > 0 ? round(($lifetimeBalanceFloat / $lifetimeIncomeFloat) * 100, 1) : null;

            $periodIncomeFloat = $pIncome->toFloat();
            $periodBalanceFloat = $pBalance->toFloat();
            $periodMargin = $periodIncomeFloat > 0 ? round(($periodBalanceFloat / $periodIncomeFloat) * 100, 1) : null;

            $projectsData[] = [
                'project' => $project,
                'period_income' => (string) $pIncome,
                'period_expense' => (string) $pExpense,
                'period_receivable' => (string) $pReceivable,
                'period_balance' => (string) $pBalance,
                'lifetime_balance' => (string) $pLifetimeBalance,
                'lifetime_margin' => $lifetimeMargin,
                'period_margin' => $periodMargin,
                'pending_count' => $cfs->filter(fn ($c) => in_array($c->status, ['pending', 'piutang'], true))->count(),
                'has_transactions' => $cfs->isNotEmpty(),
            ];
        }

        $grandPeriodBalance = $grandPeriodIncome->minus($grandPeriodExpense);
        $grandPeriodIncomeFloat = $grandPeriodIncome->toFloat();
        $grandPeriodBalanceFloat = $grandPeriodBalance->toFloat();
        $grandMargin = $grandPeriodIncomeFloat > 0 ? round(($grandPeriodBalanceFloat / $grandPeriodIncomeFloat) * 100, 1) : null;

        $projects = (clone $query)->paginate(15)->withQueryString();

        return view('admin.cashbook.index', [
            'projects' => $projects,
            'projectsData' => $projectsData,
            'totalPendingCount' => $totalPendingCount,
            'grandPeriodIncome' => (string) $grandPeriodIncome,
            'grandPeriodExpense' => (string) $grandPeriodExpense,
            'grandPeriodReceivable' => (string) $grandPeriodReceivable,
            'grandPeriodBalance' => (string) $grandPeriodBalance,
            'grandLifetimeBalance' => (string) $grandLifetimeBalance,
            'grandMargin' => $grandMargin,
            'periodTitle' => $periodTitle,
            'activeMode' => $activeMode,
            'filterMode' => $activeMode,
            'preset' => $preset,
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'years' => $years,
            'months' => $monthNames,
            'search' => $search,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function show(Request $request, Project $project): View
    {
        Gate::authorize('viewAny', ProjectCashflow::class);
        $filters = $request->validate([
            'period_mode' => ['nullable', 'string', 'in:lengkap,bulan,tahun,range,month,year'],
            'preset' => ['nullable', 'string', 'in:today,this_month,this_year,all,custom_date,custom_month'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:1000,9999'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'start_month' => ['nullable', 'integer', 'between:1,12'],
            'end_month' => ['nullable', 'integer', 'between:1,12'],
            'status' => ['nullable', 'string', 'in:all,lunas,piutang,pending,ditolak'],
            'type' => ['nullable', 'string', 'in:all,penerimaan,pengeluaran'],
            'code' => ['nullable', 'string', 'max:255'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $periodMode = $request->query('period_mode');
        $preset = $request->query('preset');
        $selectedYear = ! empty($filters['year']) ? (int) $filters['year'] : (int) now()->year;
        $selectedMonth = ! empty($filters['month']) ? (int) $filters['month'] : null;
        $startDate = null;
        $endDate = null;
        $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        // Evaluasi 3 Bagian Periode: Lengkap, Bulan, Tahun
        if ($periodMode === 'lengkap' || $periodMode === 'range' || $request->filled('start_date') || $request->filled('end_date')) {
            $activeMode = 'lengkap';
            $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
            $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;
            $startStr = $startDate ? $startDate->format('d/m/Y') : 'Awal';
            $endStr = $endDate ? $endDate->format('d/m/Y') : 'Sekarang';
            $periodTitle = "Rentang: {$startStr} s/d {$endStr}";
        } elseif ($periodMode === 'tahun' || $periodMode === 'year' || ($request->filled('year') && empty($request->query('month')) && empty($periodMode) && empty($preset))) {
            $activeMode = 'tahun';
            $selectedMonth = null;
            $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
            $periodTitle = 'Tahun ' . $selectedYear . ' (Semua Bulan)';
        } elseif ($preset === 'today') {
            $activeMode = 'lengkap';
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $periodTitle = 'Hari Ini (' . now()->translatedFormat('d M Y') . ')';
        } elseif ($preset === 'all') {
            $activeMode = 'tahun';
            $startDate = null;
            $endDate = null;
            $selectedMonth = null;
            $periodTitle = 'Semua Periode';
        } elseif ($request->filled('month') && $selectedMonth) {
            $activeMode = 'bulan';
            $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
            $periodTitle = ($monthNames[$selectedMonth] ?? 'Bulan ' . $selectedMonth) . ' ' . $selectedYear;
        } elseif ($request->filled('year') && $request->has('period_mode')) {
            $activeMode = 'bulan';
            $selectedMonth = null;
            $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
            $periodTitle = 'Tahun ' . $selectedYear . ' (Semua Bulan)';
        } else {
            // Unfiltered initial state: tampilkan seluruh data buku kas
            $activeMode = 'bulan';
            $selectedMonth = null;
            $startDate = null;
            $endDate = null;
            $periodTitle = 'Semua Transaksi';
        }

        $ledger = ProjectCashflow::ledger($project);
        $years = $ledger['transactions']->map(fn ($transaction) => $transaction->transaction_date->year)->unique()->sortDesc()->values();

        $statusFilter = $request->query('status');
        $typeFilter = $request->query('type');
        $codeFilter = $request->query('code');
        $searchFilter = is_string($request->query('search')) ? trim($request->query('search')) : '';

        $ledger['transactions'] = $ledger['transactions']->filter(function ($transaction) use ($startDate, $endDate, $statusFilter, $typeFilter, $codeFilter, $searchFilter) {
            $cfDate = $transaction->transaction_date instanceof CarbonInterface ? $transaction->transaction_date : Carbon::parse($transaction->transaction_date);
            $matchDate = ($startDate === null || $cfDate->gte($startDate->startOfDay()))
                && ($endDate === null || $cfDate->lte($endDate->endOfDay()));
            $matchStatus = $statusFilter === null || $statusFilter === '' || $statusFilter === 'all'
                || ($statusFilter === 'lunas' && in_array($transaction->status, ['lunas', null], true))
                || (in_array($statusFilter, ['piutang', 'pending'], true) && in_array($transaction->status, ['pending', 'piutang'], true))
                || ($transaction->status === $statusFilter);
            $matchType = empty($typeFilter) || $typeFilter === 'all' || $transaction->type === $typeFilter;
            $matchCode = empty($codeFilter) || strcasecmp((string) $transaction->code, $codeFilter) === 0;
            $trxCode = 'TRX-' . $cfDate->format('Ym') . '-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT);
            $matchSearch = empty($searchFilter)
                || str_contains(strtolower($transaction->description ?? ''), strtolower($searchFilter))
                || str_contains(strtolower($transaction->code ?? ''), strtolower($searchFilter))
                || str_contains(strtolower($transaction->notes ?? ''), strtolower($searchFilter))
                || str_contains(strtolower($trxCode), strtolower($searchFilter));

            return $matchDate && $matchStatus && $matchType && $matchCode && $matchSearch;
        });

        $income = $expense = $receivable = BigDecimal::of('0.00');
        foreach ($ledger['transactions'] as $transaction) {
            $cfTotal = BigDecimal::of($transaction->total);
            if (in_array($transaction->status, ['pending', 'piutang'], true)) {
                $receivable = $receivable->plus($cfTotal);
            } elseif (in_array($transaction->status, ['lunas', null], true)) {
                if ($transaction->type === 'penerimaan') {
                    $income = $income->plus($cfTotal);
                } else {
                    $expense = $expense->plus($cfTotal);
                }
            }
        }
        $ledger['income'] = (string) $income;
        $ledger['expense'] = (string) $expense;
        $ledger['receivable'] = (string) $receivable;
        $ledger['balance'] = (string) $income->minus($expense);

        $incomeFloat = $income->toFloat();
        $balanceFloat = $income->minus($expense)->toFloat();
        $marginPercent = $incomeFloat > 0 ? round(($balanceFloat / $incomeFloat) * 100, 1) : null;

        return view('admin.cashbook.show', [
            'project' => $project,
            'codeOptions' => CashflowOption::optionsFor('code'),
            'unitOptions' => CashflowOption::optionsFor('unit'),
            'years' => $years,
            'months' => $monthNames,
            'filters' => $filters,
            'preset' => $preset,
            'activeMode' => $activeMode,
            'periodTitle' => $periodTitle,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'filterMode' => $activeMode,
            'marginPercent' => $marginPercent,
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
            'startMonth' => $request->query('start_month'),
            'endMonth' => $request->query('end_month'),
            'statusFilter' => $statusFilter,
            'typeFilter' => $typeFilter,
            'codeFilter' => $codeFilter,
            'searchFilter' => $searchFilter,
        ] + $ledger);
    }

    public function storeOption(Request $request, Project $project): JsonResponse
    {
        Gate::authorize('create', ProjectCashflow::class);
        $type = $request->input('option_type');
        $validated = $request->validate([
            'option_type' => ['required', 'in:code,unit'],
            'option_value' => ['required', 'string', 'max:'.($type === 'unit' ? 30 : 50), 'regex:/\S/u', 'not_regex:/[\p{Cc}\p{Cf}]/u'],
        ]);

        if (! Schema::hasTable('cashflow_options')) {
            return response()->json([
                'message' => 'Tabel pilihan belum tersedia. Jalankan migrasi terlebih dahulu.',
                'errors' => ['option_value' => ['Tabel pilihan belum tersedia. Jalankan migrasi terlebih dahulu.']],
            ], 503);
        }

        $value = trim($validated['option_value']);

        return response()->json(['value' => CashflowOption::remember($validated['option_type'], $value)]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('create', ProjectCashflow::class);
        $request->merge(['_cashflow_id' => null]);
        $validated = $this->validatedTransaction($request);
        $attachment = $request->file('attachment');
        unset($validated['attachment'], $validated['remove_attachment']);

        $validated['created_by'] = auth()->id();
        if (empty($validated['category_id'])) {
            $validated['category_id'] = $this->resolveCategoryId($validated['code'] ?? null, $validated['type']);
        }
        $validated['status'] = $validated['status'] ?? (auth()->user()?->role === 'accounting' ? 'pending' : 'lunas');
        if ($validated['status'] === 'lunas' && auth()->user()?->role === 'admin') {
            $validated['approved_by'] = auth()->id();
            $validated['approved_at'] = now();
        }
        $cashflow = $project->cashflows()->create($validated);

        if ($attachment && $attachment->isValid()) {
            $path = $attachment->store('cashflows/'.$project->id, 'public');
            $cashflow->attachments()->create([
                'disk' => 'public',
                'file_path' => $path,
                'original_name' => $attachment->getClientOriginalName(),
                'mime_type' => $attachment->getClientMimeType() ?: ($attachment->getMimeType() ?? 'image/jpeg'),
                'file_size' => $attachment->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        return to_route('admin.cashbook.show', $project)->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectCashflow $cashflow): RedirectResponse
    {
        Gate::authorize('update', $cashflow);
        abort_unless((string) $cashflow->project_id === (string) $project->getKey(), 404);
        $request->merge(['_cashflow_id' => (string) $cashflow->getKey()]);
        $validated = $this->validatedTransaction($request);
        $attachment = $request->file('attachment');
        $removeAttachment = $request->boolean('remove_attachment');
        unset($validated['attachment'], $validated['remove_attachment']);

        $validated['updated_by'] = auth()->id();
        $validated['last_edited_at'] = now();
        if (empty($validated['category_id'])) {
            $validated['category_id'] = $this->resolveCategoryId($validated['code'] ?? null, $validated['type']);
        }

        if (isset($validated['status']) && $validated['status'] === 'lunas' && $cashflow->status !== 'lunas') {
            $validated['approved_by'] = auth()->id();
            $validated['approved_at'] = now();
        }

        $cashflow->fill($validated);
        $cashflow->updated_at = now();
        $cashflow->save();

        if ($removeAttachment || ($attachment && $attachment->isValid())) {
            foreach ($cashflow->attachments as $existingAttachment) {
                Storage::disk($existingAttachment->disk)->delete($existingAttachment->file_path);
                $existingAttachment->delete();
            }
        }

        if ($attachment && $attachment->isValid()) {
            $path = $attachment->store('cashflows/'.$project->id, 'public');
            $cashflow->attachments()->create([
                'disk' => 'public',
                'file_path' => $path,
                'original_name' => $attachment->getClientOriginalName(),
                'mime_type' => $attachment->getClientMimeType() ?: ($attachment->getMimeType() ?? 'image/jpeg'),
                'file_size' => $attachment->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        return to_route('admin.cashbook.show', $project)->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function invoice(Project $project, ProjectCashflow $cashflow): View
    {
        Gate::authorize('view', $cashflow);
        abort_unless((string) $cashflow->project_id === (string) $project->getKey(), 404);

        $cashflow->loadMissing(['creator', 'project.user']);

        return view('admin.cashbook.invoice', [
            'project' => $project,
            'cashflow' => $cashflow,
        ]);
    }

    public function approve(Project $project, ProjectCashflow $cashflow): RedirectResponse
    {
        abort_unless(auth()->user()?->role === 'admin', 403, 'Hanya Super Admin / Manager yang dapat menyetujui transaksi.');
        abort_unless((string) $cashflow->project_id === (string) $project->getKey(), 404);

        $cashflow->update([
            'status' => 'lunas',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', "Transaksi #{$cashflow->id} ({$cashflow->description}) telah disetujui (Lunas).");
    }

    public function reject(Project $project, ProjectCashflow $cashflow): RedirectResponse
    {
        abort_unless(auth()->user()?->role === 'admin', 403, 'Hanya Super Admin / Manager yang dapat menolak transaksi.');
        abort_unless((string) $cashflow->project_id === (string) $project->getKey(), 404);

        $cashflow->update([
            'status' => 'ditolak',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('info', "Transaksi #{$cashflow->id} ({$cashflow->description}) telah ditolak.");
    }

    public function destroy(Project $project, ProjectCashflow $cashflow): RedirectResponse
    {
        Gate::authorize('delete', $cashflow);
        abort_unless((string) $cashflow->project_id === (string) $project->getKey(), 404);
        $cashflow->delete();

        return to_route('admin.cashbook.show', $project)->with('success', 'Transaksi berhasil dihapus.');
    }

    public function export(Project $project): BinaryFileResponse
    {
        Gate::authorize('viewAny', ProjectCashflow::class);

        return (new ProjectCashflowExport)->download($project);
    }

    private function validatedTransaction(Request $request): array
    {
        $positiveDecimal = static function (string $maximum): Closure {
            return static function (string $attribute, mixed $value, Closure $fail) use ($maximum): void {
                $decimal = BigDecimal::of($value);
                if (! $decimal->isPositive() || $decimal->isGreaterThan($maximum)) {
                    $fail("Nilai :attribute harus lebih dari nol dan maksimal {$maximum}.");
                }
            };
        };

        $validated = $request->validate([
            'transaction_date' => ['required', 'date_format:Y-m-d'],
            'type' => ['required', 'in:penerimaan,pengeluaran'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:2000'],
            'unit' => ['nullable', 'string', 'max:30'],
            'quantity' => ['bail', 'required', 'string', 'max:32', 'regex:/\A[0-9]+(?:\.[0-9]{1,3})?\z/', $positiveDecimal(ProjectCashflow::MAX_QUANTITY)],
            'unit_price' => ['bail', 'required', 'string', 'max:32', 'regex:/\A[0-9]+(?:\.[0-9]{1,2})?\z/', $positiveDecimal(ProjectCashflow::MAX_UNIT_PRICE)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:pending,lunas,ditolak'],
            'attachment' => ['nullable', 'file', 'image', 'max:10240'],
            'remove_attachment' => ['nullable', 'boolean'],
        ]);

        if (ProjectCashflow::calculateTotal($validated['quantity'], $validated['unit_price'])->isGreaterThan(ProjectCashflow::MAX_TOTAL)) {
            throw ValidationException::withMessages(['quantity' => 'Total transaksi maksimal '.ProjectCashflow::MAX_TOTAL.'.']);
        }

        $validated['quantity_millis'] = BigDecimal::of($validated['quantity'])->toScale(3, RoundingMode::Unnecessary)->getUnscaledValue()->toInt();
        $validated['unit_price_cents'] = BigDecimal::of($validated['unit_price'])->toScale(2, RoundingMode::Unnecessary)->getUnscaledValue()->toInt();
        unset($validated['quantity'], $validated['unit_price']);

        return $validated;
    }

    private function resolveCategoryId(?string $code, string $type): ?int
    {
        $codeUpper = strtoupper(trim((string) $code));
        $slug = null;
        if (str_starts_with($codeUpper, 'MAT')) {
            $slug = 'pembelian-material';
        } elseif (str_starts_with($codeUpper, 'UPAH')) {
            $slug = 'gaji';
        } elseif (str_starts_with($codeUpper, 'SEWA')) {
            $slug = 'peralatan';
        } elseif (str_starts_with($codeUpper, 'KONS')) {
            $slug = 'konsumsi';
        } elseif (str_starts_with($codeUpper, 'TRANS')) {
            $slug = 'transportasi';
        } elseif (str_starts_with($codeUpper, 'ADM')) {
            $slug = 'administrasi';
        } elseif (str_starts_with($codeUpper, 'OPS')) {
            $slug = 'operasional';
        } elseif (str_starts_with($codeUpper, 'INV') || $type === 'penerimaan') {
            $slug = 'pembayaran-klien';
        } else {
            $slug = 'operasional';
        }

        return TransactionCategory::where('slug', $slug)->value('id');
    }
}
