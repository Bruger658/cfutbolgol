<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        $members = Member::query()->latest()->paginate(15);

        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        return view('members.create', ['member' => new Member(['is_up_to_date' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateMember($request);
        $data['is_up_to_date'] = $request->boolean('is_up_to_date');

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
        $data['is_up_to_date'] = $request->boolean('is_up_to_date');

        $member->update($data);

        return redirect()->route('members.index')->with('status', 'Socia actualizada correctamente.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members.index')->with('status', 'Socia eliminada correctamente.');
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
            'is_up_to_date' => ['nullable', 'boolean'],
        ]);
    }
}