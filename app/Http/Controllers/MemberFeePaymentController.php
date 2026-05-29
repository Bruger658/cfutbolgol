<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberFeePayment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

    public function publicIndex(Request $request): View
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

        return view('members.fee-payments.public', [
            'search' => $search,
            'members' => $members,
            'selectedMember' => $selectedMember,
            'paymentSummary' => $selectedMember ? $this->paymentSummary($selectedMember) : null,
        ]);
    }

    public function publicStore(Request $request, Member $member): RedirectResponse
    {
        $result = $this->registerPayment($request, $member);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return redirect()
            ->route('fees.public.index', ['member' => $member->id])
            ->with('status', 'Recibimos el pago de cuota para '.$member->first_name.' '.$member->last_name.'. Total: $'.number_format($result['total'], 0, ',', '.'));
    }

    public function mercadoPagoStore(Request $request, Member $member): RedirectResponse
    {
        $monthsToPay = $this->monthsToPayFromRequest($request, $member);

        if ($monthsToPay === []) {
            return back()
                ->withErrors(['months' => 'Seleccioná al menos un mes adeudado para pagar con Mercado Pago.'])
                ->withInput();
        }

        $accessToken = config('services.mercado_pago.access_token');

        if (blank($accessToken)) {
            return back()->withErrors([
                'mercado_pago' => 'Mercado Pago no está configurado. Agregá MERCADO_PAGO_ACCESS_TOKEN en el archivo .env.',
            ])->withInput();
        }

        $summary = $this->paymentSummaryForMonths($monthsToPay);
        $baseAmount = $summary['months']->sum('base_amount');
        $surchargeAmount = $summary['months']->sum('surcharge_amount');

        $feePayment = MemberFeePayment::create([
            'member_id' => $member->id,
            'months' => $monthsToPay,
            'base_amount' => $baseAmount,
            'surcharge_amount' => $surchargeAmount,
            'total_amount' => $summary['total'],
            'status' => 'pending',
            'payment_provider' => 'mercado_pago',
        ]);

        try {
            $preference = $this->createMercadoPagoPreference($feePayment, $member, $summary, $accessToken);
        } catch (ConnectionException) {
            $feePayment->update(['status' => 'failed']);

            return back()->withErrors([
                'mercado_pago' => 'No pudimos conectar con Mercado Pago. Intentá nuevamente en unos minutos.',
            ])->withInput();
        }

        if ($preference->failed()) {
            $feePayment->update(['status' => 'failed']);

            return back()->withErrors([
                'mercado_pago' => 'Mercado Pago rechazó la creación del checkout. Revisá las credenciales y volvé a intentar.',
            ])->withInput();
        }

        $preferenceData = $preference->json();
        $checkoutUrl = $preferenceData['init_point']
            ?? $preferenceData['sandbox_init_point']
            ?? null;

        if (blank($checkoutUrl)) {
            $feePayment->update(['status' => 'failed']);

            return back()->withErrors([
                'mercado_pago' => 'Mercado Pago no devolvió una URL de pago válida.',
            ])->withInput();
        }

        $feePayment->update([
            'provider_reference' => $preferenceData['id'] ?? null,
            'checkout_url' => $checkoutUrl,
        ]);

        return redirect()->away($checkoutUrl);
    }

    public function mercadoPagoSuccess(Request $request, MemberFeePayment $feePayment): RedirectResponse
    {
        $this->syncMercadoPagoPaymentFromReturn($request, $feePayment, 'approved');

        return redirect()
            ->route('fees.public.index', ['member' => $feePayment->member_id])
            ->with('status', 'Pago recibido. Ya actualizamos los meses abonados del socio.');
    }

    public function mercadoPagoFailure(Request $request, MemberFeePayment $feePayment): RedirectResponse
    {
        $this->syncMercadoPagoPaymentFromReturn($request, $feePayment, 'failed');

        return redirect()
            ->route('fees.public.index', ['member' => $feePayment->member_id])
            ->withErrors(['mercado_pago' => 'El pago no fue aprobado por Mercado Pago. Podés intentarlo nuevamente.']);
    }

    public function mercadoPagoPending(Request $request, MemberFeePayment $feePayment): RedirectResponse
    {
        $this->syncMercadoPagoPaymentFromReturn($request, $feePayment, 'pending');

        return redirect()
            ->route('fees.public.index', ['member' => $feePayment->member_id])
            ->with('status', 'El pago quedó pendiente en Mercado Pago. Cuando se apruebe, el backend actualizará la cuota automáticamente.');
    }

    public function mercadoPagoWebhook(Request $request): JsonResponse
    {
        $paymentId = $request->input('data.id')
            ?? $request->input('id')
            ?? $request->query('data_id')
            ?? $request->query('id');

        if (blank($paymentId)) {
            return response()->json(['message' => 'No payment id received.'], 202);
        }

        $paymentData = $this->fetchMercadoPagoPayment((string) $paymentId);

        if ($paymentData === null) {
            return response()->json(['message' => 'Could not verify payment.'], 422);
        }

        $feePaymentId = $paymentData['external_reference']
            ?? data_get($paymentData, 'metadata.fee_payment_id');

        if (blank($feePaymentId)) {
            return response()->json(['message' => 'Payment has no external reference.'], 202);
        }

        $feePayment = MemberFeePayment::find($feePaymentId);

        if (! $feePayment) {
            return response()->json(['message' => 'Fee payment not found.'], 404);
        }

        $this->applyMercadoPagoStatus($feePayment, $paymentData['status'] ?? null, (string) $paymentId);

        return response()->json(['message' => 'Fee payment updated.']);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $result = $this->registerPayment($request, $member);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return redirect()
            ->route('members.fee-payments.index', ['member' => $member->id])
            ->with('status', 'Pago de cuota registrado para '.$member->first_name.' '.$member->last_name.'. Total: $'.number_format($result['total'], 0, ',', '.'));
    }

    private function registerPayment(Request $request, Member $member): array|RedirectResponse
    // public function store(Request $request, Member $member): RedirectResponse
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

        return $this->paymentSummaryForMonths($monthsToPay);
    }

     private function monthsToPayFromRequest(Request $request, Member $member): array
    {
        $validated = $request->validate([
            'months' => ['required', 'array', 'min:1'],
            'months.*' => ['integer', 'between:1,12'],
        ]);

        $missingMonths = $member->missing_months;

        return collect($validated['months'])
            ->map(fn ($month) => (int) $month)
            ->unique()
            ->filter(fn (int $month) => in_array($month, $missingMonths, true))
            ->sort()
            ->values()
            ->all();
    }

    private function createMercadoPagoPreference(MemberFeePayment $feePayment, Member $member, array $summary, string $accessToken)
    {
        $months = $summary['months']->pluck('name')->implode(', ');

        return Http::withToken($accessToken)
            ->acceptJson()
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [[
                    'id' => 'fee-payment-'.$feePayment->id,
                    'title' => 'Cuota social - '.$member->first_name.' '.$member->last_name,
                    'description' => 'Meses: '.$months,
                    'quantity' => 1,
                    'currency_id' => config('services.mercado_pago.currency', 'ARS'),
                    'unit_price' => (float) $summary['total'],
                ]],
                'external_reference' => (string) $feePayment->id,
                'back_urls' => [
                    'success' => route('fees.mercado-pago.success', $feePayment),
                    'failure' => route('fees.mercado-pago.failure', $feePayment),
                    'pending' => route('fees.mercado-pago.pending', $feePayment),
                ],
                'auto_return' => 'approved',
                'notification_url' => route('fees.mercado-pago.webhook'),
                'metadata' => [
                    'fee_payment_id' => $feePayment->id,
                    'member_id' => $member->id,
                    'months' => $feePayment->months,
                ],
            ]);
    }

    private function syncMercadoPagoPaymentFromReturn(Request $request, MemberFeePayment $feePayment, ?string $fallbackStatus = null): void
    {
        $paymentId = $request->query('payment_id')
            ?? $request->query('collection_id');

        if (filled($paymentId)) {
            $paymentData = $this->fetchMercadoPagoPayment((string) $paymentId);

            if ($paymentData !== null) {
                $this->applyMercadoPagoStatus($feePayment, $paymentData['status'] ?? $fallbackStatus, (string) $paymentId);

                return;
            }
        }

        $status = $request->query('collection_status')
            ?? $request->query('status')
            ?? $fallbackStatus;

        $this->applyMercadoPagoStatus($feePayment, $status, filled($paymentId) ? (string) $paymentId : null);
    }

    private function fetchMercadoPagoPayment(string $paymentId): ?array
    {
        $accessToken = config('services.mercado_pago.access_token');

        if (blank($accessToken)) {
            return null;
        }

        try {
            $payment = Http::withToken($accessToken)
                ->acceptJson()
                ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");
        } catch (ConnectionException) {
            return null;
        }

        if ($payment->failed()) {
            return null;
        }

        return $payment->json();
    }

    private function applyMercadoPagoStatus(MemberFeePayment $feePayment, ?string $mercadoPagoStatus, ?string $paymentId = null): void
    {
        $status = match ($mercadoPagoStatus) {
            'approved' => 'paid',
            'rejected', 'cancelled', 'refunded', 'charged_back', 'failed' => 'failed',
            'in_process', 'in_mediation', 'pending' => 'pending',
            default => $feePayment->status,
        };

        DB::transaction(function () use ($feePayment, $status, $paymentId): void {
            $feePayment->refresh();

            $updates = ['status' => $status];

            if (filled($paymentId)) {
                $updates['provider_payment_id'] = $paymentId;
            }

            if ($status === 'paid' && $feePayment->status !== 'paid') {
                $updates['paid_at'] = Carbon::now();

                $member = Member::whereKey($feePayment->member_id)->lockForUpdate()->first();

                if ($member) {
                    $paidMonths = collect($member->paid_months ?? [])
                        ->map(fn ($month) => (int) $month)
                        ->merge(collect($feePayment->months ?? [])->map(fn ($month) => (int) $month))
                        ->unique()
                        ->sort()
                        ->values()
                        ->all();

                    $member->update([
                        'paid_months' => $paidMonths,
                        'is_up_to_date' => empty($this->missingMonthsFrom($paidMonths)),
                    ]);
                }
            }

            $feePayment->update($updates);
        });
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
       $monthlyFee = (int) config('membership.monthly_fee', 35000);
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