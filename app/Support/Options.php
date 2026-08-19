<?php

namespace App\Support;

class Options
{
    public const BUSINESS_UNITS = [
        'Retail Stores',
        'F&B Outlets',
        'Warehouse & Logistics',
        'Corporate Support',
        'Marketing & Digital',
    ];

    public const STATES = [
        'Selangor',
        'Kuala Lumpur',
        'Penang',
        'Johor',
        'Perak',
        'Sabah',
        'Sarawak',
        'Melaka',
    ];

    public const EMPLOYMENT_TYPES = ['Permanent', 'Contract', 'Part-Time', 'Intern'];

    public const LEVELS = ['Executive', 'Senior Executive', 'Manager', 'Senior Manager', 'Head'];

    public const SEVERITIES = ['Near Miss', 'First Aid', 'Medical Treatment', 'Lost Time Injury'];

    public const EXIT_TYPES = ['Voluntary', 'Involuntary'];

    /** BUs whose outlets actually generate revenue — used to scope Business Performance entry/charts. */
    public const OPERATING_BUS = ['Retail Stores', 'F&B Outlets', 'Warehouse & Logistics'];

    /** Minimum tenure, in whole months, to register for BLIP (Volume 0, section 2). */
    public const BLIP_MIN_TENURE_MONTHS = 6;

    public const BLIP_ENROLLMENT_STATUSES = [
        'interest_submitted',
        'endorsed',
        'on_hold',
        'declined',
        'cohort_placed',
    ];

    public const BLIP_COHORT_STATUSES = ['planned', 'open', 'running', 'completed'];

    public const BLIP_STAMP_STATUSES = ['scheduled', 'earned'];
}
