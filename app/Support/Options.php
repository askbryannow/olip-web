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
}
