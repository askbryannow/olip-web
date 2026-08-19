<?php

namespace App\Livewire\Blip;

use App\Models\BlipEnrollment;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** The leadership pipeline register — cohort-wide funnel and the Promotion-Ready shortlist. */
#[Layout('layouts.app')]
class Pipeline extends Component
{
    public function markPromotionReady(int $id): void
    {
        BlipEnrollment::findOrFail($id)->update(['promotion_ready_at' => now()]);
    }

    public function render()
    {
        $enrollments = BlipEnrollment::with(['employee.outlet', 'stamps'])->get();

        $stages = [
            'Registering' => 0,
            'Applicant' => 0,
            'Traveller' => 0,
            'Voyager' => 0,
            'Endorsed' => 0,
            'Promotion-Ready' => 0,
        ];

        foreach ($enrollments as $enrollment) {
            $stage = $enrollment->passportStage();
            $stages[$stage] = ($stages[$stage] ?? 0) + 1;
        }

        // The funnel component reads best as a strict progression; fold the side-branch
        // statuses (On Hold / Declined / Awaiting Cohort Placement) into "Registering".
        $registeringOnly = collect($stages)->except(['Applicant', 'Traveller', 'Voyager', 'Endorsed', 'Promotion-Ready'])->sum();
        $funnel = [
            ['label' => 'Registering', 'value' => $registeringOnly],
            ['label' => 'Applicant', 'value' => $stages['Applicant']],
            ['label' => 'Traveller', 'value' => $stages['Traveller']],
            ['label' => 'Voyager', 'value' => $stages['Voyager']],
            ['label' => 'Endorsed', 'value' => $stages['Endorsed']],
            ['label' => 'Promotion-Ready', 'value' => $stages['Promotion-Ready']],
        ];

        $promotionReady = $enrollments->filter(fn ($e) => $e->passportStage() === 'Promotion-Ready');
        $endorsedAwaitingReview = $enrollments->filter(fn ($e) => $e->passportStage() === 'Endorsed');

        return view('livewire.blip.pipeline', [
            'funnel' => $funnel,
            'totalRegistered' => $enrollments->count(),
            'activeTravellers' => $enrollments->filter(fn ($e) => in_array($e->passportStage(), ['Applicant', 'Traveller', 'Voyager']))->count(),
            'promotionReady' => $promotionReady,
            'endorsedAwaitingReview' => $endorsedAwaitingReview,
        ]);
    }
}
