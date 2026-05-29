<?php

return [
    'monthly_fee' => (int) env('MEMBERSHIP_MONTHLY_FEE', 10000),
    'late_surcharge_percentage' => (int) env('MEMBERSHIP_LATE_SURCHARGE_PERCENTAGE', 10),
    'late_surcharge_starts_after_day' => (int) env('MEMBERSHIP_LATE_SURCHARGE_STARTS_AFTER_DAY', 10),
];