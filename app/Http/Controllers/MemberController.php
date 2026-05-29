<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        // $members = Member::query()->latest()->paginate(15);
        $search = trim((string) $request->string('search'));
        $showOnlyDebtors = $request->boolean('only_debtors');

        // $members = Member::query()            
        //     ->when($search !== '', function ($query) use ($search) {
        //         //  $terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);

                
        //             $query->where(function ($subQuery) use ($search) {
        //             $subQuery->where('first_name', 'like', "%{$search}%")
        //                 ->orWhere('last_name', 'like', "%{$search}%")
        //                 ->orWhere('document_number', 'like', "%{$search}%")
        //                 ->orWhere('category', 'like', "%{$search}%");
        //         });
        //     })
        //     ->when($showOnlyDebtors, function ($query) {
        //         $query->where('is_up_to_date', false);    
        //     })
        $members = $this->membersQueryFromRequest($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('members.index', compact('members', 'search', 'showOnlyDebtors'));
    }

    public function exportExcel(Request $request)
    {
        $members = $this->membersQueryFromRequest($request)->latest()->get();
        $export = new MembersExport($members);

        $fileName = 'socios_'.now()->format('Ymd_His').'.csv';

        return Response::streamDownload(function () use ($export) {
            echo $export->toCsv();
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request): View
    {
        $members = $this->membersQueryFromRequest($request)->latest()->get();

        return view('members.exports.pdf', compact('members'));
    }


    public function create(): View
    {
        return view('members.create', ['member' => new Member(['is_up_to_date' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateMember($request);
        // $data['is_up_to_date'] = $request->boolean('is_up_to_date');
        $data['paid_months'] = $this->sanitizePaidMonths($request->input('paid_months', []));
        $data['is_up_to_date'] = empty($this->getMissingMonths($data['paid_months']));

        Member::create($data);

        return redirect()->route('members.index')->with('status', 'Socia creada correctamente.');
    }

    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $this->validateMember($request);
        // $data['is_up_to_date'] = $request->boolean('is_up_to_date');
        $data['paid_months'] = $this->sanitizePaidMonths($request->input('paid_months', []));
        $data['is_up_to_date'] = empty($this->getMissingMonths($data['paid_months']));

        $member->update($data);

        return redirect()->route('members.index')->with('status', 'Socia actualizada correctamente.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members.index')->with('status', 'Socia eliminada correctamente.');
    }

     private function membersQueryFromRequest(Request $request)
    {
        $search = trim((string) $request->string('search'));
        $showOnlyDebtors = $request->boolean('only_debtors');

        return Member::query()
            ->when($search !== '', function ($query) use ($search) {
                $terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];

                $query->where(function ($subQuery) use ($search, $terms) {
                    if (ctype_digit($search)) {
                        $subQuery->orWhere('id', (int) $search);
                    }

                    $subQuery->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");

                    foreach ($terms as $term) {
                        $subQuery->orWhere('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhere('document_number', 'like', "%{$term}%")
                            ->orWhere('category', 'like', "%{$term}%");
                    }    
                });
            })
            ->when($showOnlyDebtors, function ($query) {
                $query->where('is_up_to_date', false);
            });
    }


    private function validateMember(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:120'],
            'document_number' => ['required', 'string', 'max:40'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'responsible_adult_phone' => ['nullable', 'string', 'max:40'],
            // 'is_up_to_date' => ['nullable', 'boolean'],
            'paid_months' => ['nullable', 'array'],
            'paid_months.*' => ['integer', 'between:1,12'],
        ]);
    }

     private function sanitizePaidMonths(array $paidMonths): array
    {
        return collect($paidMonths)
            ->map(fn ($month) => (int) $month)
            ->filter(fn (int $month) => $month >= 1 && $month <= 12)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function getMissingMonths(array $paidMonths): array
    {
        $currentMonth = (int) now()->month;
        $expectedMonths = range(1, $currentMonth);

        return array_values(array_diff($expectedMonths, $paidMonths));
    }
}