<?php

namespace App\Http\Controllers;

use App\Mail\EnrollmentRequestReceived;
use App\Models\EnrollmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EnrollmentRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'player_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'guardian_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:40'],
            'category' => ['required', Rule::in(['Edefi', 'Bafi', 'Futsala', 'Futsal Femenino'])],
        ]);

        $enrollmentRequest = EnrollmentRequest::create($data + [
            'status' => EnrollmentRequest::STATUS_PENDING,
        ]);

        $clubEmail = config('services.enrollment.club_email');

        if ($clubEmail) {
            Mail::to($clubEmail)->send(new EnrollmentRequestReceived($enrollmentRequest));
        }

        return redirect()
            ->route('index')
            ->withFragment('inscripcion')
            ->with('enrollment_status', 'Recibimos tu solicitud. El equipo técnico se pondrá en contacto para coordinar la prueba de nivel.');
    }

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $enrollmentRequests = EnrollmentRequest::query()
            ->when(array_key_exists($status, EnrollmentRequest::STATUSES), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('enrollment-requests.index', [
            'enrollmentRequests' => $enrollmentRequests,
            'statuses' => EnrollmentRequest::STATUSES,
            'selectedStatus' => $status,
        ]);
    }

    public function update(Request $request, EnrollmentRequest $enrollmentRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(EnrollmentRequest::STATUSES))],
        ]);

        $updates = ['status' => $data['status']];

        if ($data['status'] === EnrollmentRequest::STATUS_CONTACTED && ! $enrollmentRequest->contacted_at) {
            $updates['contacted_at'] = now();
        }

        if ($data['status'] === EnrollmentRequest::STATUS_TRIAL_SCHEDULED && ! $enrollmentRequest->trial_scheduled_at) {
            $updates['trial_scheduled_at'] = now();
            $updates['contacted_at'] = $enrollmentRequest->contacted_at ?? now();
        }

        if ($data['status'] === EnrollmentRequest::STATUS_ENROLLED && ! $enrollmentRequest->enrolled_at) {
            $updates['enrolled_at'] = now();
            $updates['contacted_at'] = $enrollmentRequest->contacted_at ?? now();
        }

        $enrollmentRequest->update($updates);

        return redirect()->route('enrollment-requests.index')->with('status', 'Estado de inscripción actualizado.');
    }

    public function destroy(EnrollmentRequest $enrollmentRequest): RedirectResponse
    {
        $enrollmentRequest->delete();

        return redirect()->route('enrollment-requests.index')->with('status', 'Solicitud de inscripción eliminada.');
    }
}