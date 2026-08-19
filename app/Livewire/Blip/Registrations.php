<?php

namespace App\Livewire\Blip;

use App\Models\BlipCohort;
use App\Models\BlipEnrollment;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * The registration workflow from Volume 0 §3-4: outlet manager endorses (or holds,
 * with a reason), then Head of People Development confirms eligibility and places
 * the Traveller into a cohort — which issues their (blank) passport.
 */
#[Layout('layouts.app')]
class Registrations extends Component
{
    public ?int $decidingId = null;

    public string $decision = 'endorsed';

    public string $note = '';

    public ?int $placingId = null;

    /** String, not int: bound directly to a <select>, whose empty placeholder option is "". */
    public ?string $cohortId = null;

    public ?string $error = null;

    public function openDecision(int $id, string $decision): void
    {
        $this->decidingId = $id;
        $this->decision = $decision;
        $this->note = '';
        $this->error = null;
    }

    public function saveDecision(): void
    {
        $enrollment = BlipEnrollment::findOrFail($this->decidingId);

        $enrollment->update([
            'status' => $this->decision,
            'endorsed_by' => 'Outlet Manager',
            'endorsed_at' => now(),
            'endorsement_note' => $this->note ?: null,
        ]);

        $this->decidingId = null;
    }

    public function openPlacement(int $id): void
    {
        $this->placingId = $id;
        $this->cohortId = null;
        $this->error = null;
    }

    public function confirmPlacement(): void
    {
        if (! $this->cohortId) {
            $this->error = 'Choose a cohort.';

            return;
        }

        BlipEnrollment::findOrFail($this->placingId)->update([
            'status' => 'cohort_placed',
            'cohort_id' => $this->cohortId,
            'confirmed_at' => now(),
        ]);

        $this->placingId = null;
    }

    public function cancel(): void
    {
        $this->decidingId = null;
        $this->placingId = null;
        $this->error = null;
    }

    public function render()
    {
        $enrollments = BlipEnrollment::with(['employee.outlet', 'cohort'])
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.blip.registrations', [
            'enrollments' => $enrollments,
            'cohorts' => BlipCohort::orderBy('start_date')->get(),
        ]);
    }
}
