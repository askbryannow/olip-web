<?php

namespace App\Livewire\Blip;

use App\Models\BlipEmbassy;
use App\Models\BlipEnrollment;
use App\Models\BlipStamp;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** Business Ambassadors award stamps here as each Traveller completes an Embassy visit. */
#[Layout('layouts.app')]
class Stamps extends Component
{
    public ?int $enrollmentId = null;

    public ?string $embassyId = null;

    public string $visitDate = '';

    public string $awardedBy = '';

    public string $notes = '';

    public function openAward(int $enrollmentId, string $embassyId): void
    {
        $this->enrollmentId = $enrollmentId;
        $this->embassyId = $embassyId;
        $this->visitDate = now()->toDateString();
        $this->awardedBy = '';
        $this->notes = '';
    }

    public function award(): void
    {
        BlipStamp::updateOrCreate(
            ['enrollment_id' => $this->enrollmentId, 'embassy_id' => $this->embassyId],
            [
                'status' => 'earned',
                'visit_date' => $this->visitDate,
                'earned_at' => now(),
                'awarded_by' => $this->awardedBy ?: null,
                'notes' => $this->notes ?: null,
            ],
        );

        $this->cancel();
    }

    public function cancel(): void
    {
        $this->enrollmentId = null;
        $this->embassyId = null;
    }

    public function render()
    {
        $enrollments = BlipEnrollment::query()
            ->whereNotNull('confirmed_at')
            ->with(['employee.outlet', 'stamps'])
            ->orderBy('confirmed_at')
            ->get()
            ->map(fn (BlipEnrollment $e) => [
                'enrollment' => $e,
                'stampsByEmbassy' => $e->stamps->keyBy('embassy_id'),
            ]);

        return view('livewire.blip.stamps', [
            'rows' => $enrollments,
            'embassies' => BlipEmbassy::orderBy('sequence')->get(),
        ]);
    }
}
