<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentRequest;
use App\Models\Event;
use App\Models\Fixture;
use App\Models\Member;
use App\Models\MemberFeePayment;
use App\Models\ProductOrder;
use App\Models\Publication;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stats' => [
                'activeMembers' => Member::query()->count(),
                'pendingFees' => Member::query()->where('is_up_to_date', false)->count(),
                'newEnrollments' => EnrollmentRequest::query()
                    ->where('status', EnrollmentRequest::STATUS_PENDING)
                    ->count(),
                'pendingOrders' => ProductOrder::query()->where('status', 'pending')->count(),
            ],
            'latestPayments' => MemberFeePayment::query()
                ->with('member')
                ->latest()
                ->limit(5)
                ->get(),
            'latestEnrollments' => EnrollmentRequest::query()
                ->latest()
                ->limit(5)
                ->get(),
            'upcomingFixtures' => Fixture::query()
                ->where('is_active', true)
                ->whereDate('fixture_date', '>=', now()->toDateString())
                ->orderBy('fixture_date')
                ->orderBy('match_time')
                ->limit(3)
                ->get(),
            'upcomingEvents' => Event::query()
                ->where('is_completed', false)
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(3)
                ->get(),
            'latestPublications' => Publication::query()
                ->where('is_active', true)
                ->latest('published_at')
                ->latest()
                ->limit(3)
                ->get(),
        ]);
    }
}