<?php

namespace App\Livewire;

use App\Models\Outlet;
use App\Support\Calendar;
use App\Support\Filters;
use App\Support\Options;
use Livewire\Component;

class FilterBar extends Component
{
    public string $monthKey = 'ALL';
    public string $bu = 'ALL';
    public string $state = 'ALL';
    public string $outletId = 'ALL';
    public string $employmentType = 'ALL';

    public function mount(): void
    {
        $current = Filters::current();
        $this->monthKey = $current['monthKey'];
        $this->bu = $current['bu'];
        $this->state = $current['state'];
        $this->outletId = $current['outletId'];
        $this->employmentType = $current['employmentType'];
    }

    public function updated(string $property, string $value): void
    {
        if ($property === 'bu' || $property === 'state') {
            $this->outletId = 'ALL';
        }

        Filters::set([
            'monthKey' => $this->monthKey,
            'bu' => $this->bu,
            'state' => $this->state,
            'outletId' => $this->outletId,
            'employmentType' => $this->employmentType,
        ]);

        $this->dispatch('filters-changed');
    }

    public function clear(): void
    {
        Filters::reset();
        $this->mount();
        $this->dispatch('filters-changed');
    }

    public function isFiltered(): bool
    {
        return $this->monthKey !== 'ALL' || $this->bu !== 'ALL' || $this->state !== 'ALL'
            || $this->outletId !== 'ALL' || $this->employmentType !== 'ALL';
    }

    public function render()
    {
        $outlets = Outlet::query()
            ->when($this->bu !== 'ALL', fn ($q) => $q->where('bu', $this->bu))
            ->when($this->state !== 'ALL', fn ($q) => $q->where('state', $this->state))
            ->orderBy('name')
            ->get();

        $months = collect(Calendar::trailing(windowMonths: 24))->reverse()->values();

        return view('livewire.filter-bar', [
            'outlets' => $outlets,
            'months' => $months,
            'businessUnits' => Options::BUSINESS_UNITS,
            'states' => Options::STATES,
            'employmentTypes' => Options::EMPLOYMENT_TYPES,
        ]);
    }
}
