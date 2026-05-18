<?php

use App\Models\Member;
use Carbon\Carbon;

it('only marks missing months up to current month', function () {
    Carbon::setTestNow('2026-05-18 12:00:00');

    $member = new Member([
        'paid_months' => [1, 2, 3, 4],
    ]);

    expect($member->missing_months)->toBe([5]);

    Carbon::setTestNow();
});