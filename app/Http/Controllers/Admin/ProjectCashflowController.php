<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectCashflowExport;
use App\Http\Controllers\Controller;
use App\Models\CashflowOption;
use App\Models\Project;
use App\Models\ProjectCashflow;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProjectCashflowController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ProjectCashflow::class);
        $query = Project::with('user');
        $search = is_string($request->query('search')) ? trim($request->query('search')) : '';

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

        $projects = $query->orderBy('nama_proyek')->orderBy('id')->paginate(15)->withQueryString();

        return view('admin.cashbook.index', compact('projects'));
    }

    public function show(Project $project): View
    {
        Gate::authorize('viewAny', ProjectCashflow::class);

        return view('admin.cashbook.show', [
            'project' => $project,
            'codeOptions' => CashflowOption::optionsFor('code'),
            'unitOptions' => CashflowOption::optionsFor('unit'),
        ] + ProjectCashflow::ledger($project));
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
        $project->cashflows()->create($this->validatedTransaction($request));

        return to_route('admin.cashbook.show', $project)->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectCashflow $cashflow): RedirectResponse
    {
        Gate::authorize('update', $cashflow);
        abort_unless((string) $cashflow->project_id === (string) $project->getKey(), 404);
        $request->merge(['_cashflow_id' => (string) $cashflow->getKey()]);
        $cashflow->update($this->validatedTransaction($request));

        return to_route('admin.cashbook.show', $project)->with('success', 'Transaksi berhasil diperbarui.');
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
