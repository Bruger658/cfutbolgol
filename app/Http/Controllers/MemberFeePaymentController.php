<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberFeePaymentController extends Controller
{
    private const MONTH_NAMES = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $selectedMember = $request->filled('member')
            ? Member::find($request->integer('member'))
            : null;

        $members = collect();

        if ($search !== '') {
            $members = $this->searchMembers($search)->limit(8)->get();

            if ($members->count() === 1) {
                $selectedMember = $members->first();
            }
        }

        return view('members.fee-payments.index', [
            'search' => $search,
            'members' => $members,
            'selectedMember' => $selectedMember,
            'paymentSummary' => $selectedMember ? $this->paymentSummary($selectedMember) : null,
        ]);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'months' => ['required', 'array', 'min:1'],
            'months.*' => ['integer', 'between:1,12'],
        ]);

        $missingMonths = $member->missing_months;
        $monthsToPay = collect($validated['months'])
            ->map(fn ($month) => (int) $month)
            ->unique()
            ->filter(fn (int $month) => in_array($month, $missingMonths, true))
            ->sort()
            ->values()
            ->all();

        if ($monthsToPay === []) {
            return back()
                ->withErrors(['months' => 'Seleccioná al menos un mes adeudado para registrar el pago.'])
                ->withInput();
        }

        $paidMonths = collect($member->paid_months ?? [])
            ->map(fn ($month) => (int) $month)
            ->merge($monthsToPay)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $member->update([
            'paid_months' => $paidMonths,
            'is_up_to_date' => empty($this->missingMonthsFrom($paidMonths)),
        ]);

        $summary = $this->paymentSummaryForMonths($monthsToPay);

        return redirect()
            ->route('members.fee-payments.index', ['member' => $member->id])
            ->with('status', 'Pago de cuota registrado para '.$member->first_name.' '.$member->last_name.'. Total: $'.number_format($summary['total'], 0, ',', '.'));
    }

    private function searchMembers(string $search)
    {
        $terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return Member::query()
            ->where(function ($query) use ($search, $terms) {
                if (ctype_digit($search)) {
                    $query->orWhere('id', (int) $search);
                }

                $query->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");

                foreach ($terms as $term) {
                    $query->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('document_number', 'like', "%{$term}%");
                }
            })
            ->orderBy('last_name')
            ->orderBy('first_name');
    }

    private function paymentSummary(Member $member): array
    {
        return $this->paymentSummaryForMonths($member->missing_months);
    }

    private function paymentSummaryForMonths(array $months): array
    {
        $monthlyFee = (int) config('membership.monthly_fee', 10000);
        $surchargePercentage = (int) config('membership.late_surcharge_percentage', 10);
        $surchargeApplies = now()->day > (int) config('membership.late_surcharge_starts_after_day', 10);

        $rows = collect($months)
            ->map(fn ($month) => (int) $month)
            ->unique()
            ->sort()
            ->map(function (int $month) use ($monthlyFee, $surchargePercentage, $surchargeApplies) {
                $surcharge = $surchargeApplies ? (int) round($monthlyFee * ($surchargePercentage / 100)) : 0;

                return [
                    'number' => $month,
                    'name' => self::MONTH_NAMES[$month] ?? (string) $month,
                    'base_amount' => $monthlyFee,
                    'surcharge_amount' => $surcharge,
                    'total' => $monthlyFee + $surcharge,
                ];
            })
            ->values();

        return [
            'monthly_fee' => $monthlyFee,
            'surcharge_percentage' => $surchargePercentage,
            'surcharge_applies' => $surchargeApplies,
            'months' => $rows,
            'total' => $rows->sum('total'),
        ];
    }

    private function missingMonthsFrom(array $paidMonths): array
    {
        $expectedMonths = range(1, now()->month);

        return array_values(array_diff($expectedMonths, $paidMonths));
    }
}