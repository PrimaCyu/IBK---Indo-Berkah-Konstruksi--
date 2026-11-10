<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectCashflowExport;
use App\Http\Controllers\Controller;
use App\Models\CashflowOption;
use App\Models\Project;
use App\Models\ProjectCashflow;
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
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ProjectCashflow::class);

        $filters = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2099'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $selectedMonth = ! empty($filters['month']) ? (int) $filters['month'] : null;
        $selectedYear = ! empty($filters['year']) ? (int) $filters['year'] : (int) now()->year;
        $search = is_string($request->query('search')) ? trim($request->query('search')) : '';

        // Extract transaction years from cashflow dates
        $years = ProjectCashflow::pluck('transaction_date')
            ->map(fn ($d) => (int) ($d instanceof CarbonInterface ? $d->year : Carbon::parse($d)->year))
            ->push((int) now()->year)
            ->unique()
            ->sortDesc()
            ->values();

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

        $allProjects = $query->orderBy('nama_proyek')->orderBy('id')->get();
        $projectIds = $allProjects->pluck('id');
        $allCashflows = ProjectCashflow::whereIn('project_id', $projectIds)->get();
        $cashflowsByProject = $allCashflows->groupBy('project_id');

        $projectsData = [];
        $grandPeriodIncome = BigDecimal::of('0.00');
        $grandPeriodExpense = BigDecimal::of('0.00');
        $grandLifetimeBalance = BigDecimal::of('0.00');

        $monthlyMatrix = [];
        $matrixMonthTotals = array_fill(1, 12, BigDecimal::of('0.00'));
        $matrixGrandIncome = BigDecimal::of('0.00');

        foreach ($allProjects as $project) {
            $cfs = $cashflowsByProject->get($project->id, collect());

            $pIncome = BigDecimal::of('0.00');
            $pExpense = BigDecimal::of('0.00');
            $pLifetimeBalance = BigDecimal::of('0.00');

            $projectMonthly = array_fill(1, 12, BigDecimal::of('0.00'));
            $projectYearIncome = BigDecimal::of('0.00');

            foreach ($cfs as $cf) {
                $cfTotal = BigDecimal::of($cf->total);
                $date = $cf->transaction_date;
                $m = (int) $date->month;
                $y = (int) $date->year;

                // Lifetime balance
                if ($cf->type === 'penerimaan') {
                    $pLifetimeBalance = $pLifetimeBalance->plus($cfTotal);
                } else {
                    $pLifetimeBalance = $pLifetimeBalance->minus($cfTotal);
                }

                // Monthly matrix for the selected year (penerimaan only)
                if ($y === $selectedYear && $cf->type === 'penerimaan') {
                    $projectMonthly[$m] = $projectMonthly[$m]->plus($cfTotal);
                    $projectYearIncome = $projectYearIncome->plus($cfTotal);
                    $matrixMonthTotals[$m] = $matrixMonthTotals[$m]->plus($cfTotal);
                    $matrixGrandIncome = $matrixGrandIncome->plus($cfTotal);
                }

                // Filter for selected period
                $matchMonth = $selectedMonth === null || $m === $selectedMonth;
                $matchYear = $y === $selectedYear;

                if ($matchYear && $matchMonth) {
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
            $grandLifetimeBalance = $grandLifetimeBalance->plus($pLifetimeBalance);

            $projectsData[] = [
                'project' => $project,
                'period_income' => (string) $pIncome,
                'period_expense' => (string) $pExpense,
                'period_balance' => (string) $pBalance,
                'lifetime_balance' => (string) $pLifetimeBalance,
                'has_transactions' => $cfs->isNotEmpty(),
            ];

            $monthlyMatrix[] = [
                'project' => $project,
                'months' => array_map(fn ($bd) => (string) $bd, $projectMonthly),
                'year_income' => (string) $projectYearIncome,
            ];
        }

        $grandPeriodBalance = $grandPeriodIncome->minus($grandPeriodExpense);

        $projects = (clone $query)->paginate(15)->withQueryString();

        return view('admin.cashbook.index', [
            'projects' => $projects,
            'projectsData' => $projectsData,
            'monthlyMatrix' => $monthlyMatrix,
            'matrixMonthTotals' => array_map(fn ($bd) => (string) $bd, $matrixMonthTotals),
            'matrixGrandIncome' => (string) $matrixGrandIncome,
            'grandPeriodIncome' => (string) $grandPeriodIncome,
            'grandPeriodExpense' => (string) $grandPeriodExpense,
            'grandPeriodBalance' => (string) $grandPeriodBalance,
            'grandLifetimeBalance' => (string) $grandLifetimeBalance,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'years' => $years,
            'search' => $search,
        ]);
    }

    public function show(Request $request, Project $project): View
    {
        Gate::authorize('viewAny', ProjectCashflow::class);
        $filters = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:1000,9999'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        $ledger = ProjectCashflow::ledger($project);
        $years = $ledger['transactions']->map(fn ($transaction) => $transaction->transaction_date->year)->unique()->sortDesc()->values();
        $ledger['transactions'] = $ledger['transactions']->filter(function ($transaction) use ($filters) {
            $tDate = $transaction->transaction_date->format('Y-m-d');
            
            if (!empty($filters['start_date']) && $tDate < $filters['start_date']) {
                return false;
            }
            if (!empty($filters['end_date']) && $tDate > $filters['end_date']) {
                return false;
            }

            return (empty($filters['month']) || $transaction->transaction_date->month === (int) $filters['month'])
                && (empty($filters['year']) || $transaction->transaction_date->year === (int) $filters['year']);
        });
        $income = $expense = BigDecimal::of('0.00');
        foreach ($ledger['transactions'] as $transaction) {
            if ($transaction->type === 'penerimaan') {
                $income = $income->plus($transaction->total);
            } else {
                $expense = $expense->plus($transaction->total);
            }
        }
        $ledger['income'] = (string) $income;
        $ledger['expense'] = (string) $expense;
        $ledger['balance'] = (string) $income->minus($expense);

        return view('admin.cashbook.show', [
            'project' => $project,
            'codeOptions' => CashflowOption::optionsFor('code'),
            'unitOptions' => CashflowOption::optionsFor('unit'),
            'years' => $years,
            'filters' => $filters,
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
}
