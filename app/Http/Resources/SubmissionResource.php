<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resultsPublished = $this->competition?->results_published_at !== null;

        return [
            'id' => $this->id,
            'competition_id' => $this->competition_id,
            'status' => $this->status->value,
            'text_content' => $this->text_content,
            'link_url' => $this->link_url,
            'rejection_reason' => $this->rejection_reason,
            'media_url' => $this->getFirstMediaUrl('submission_files') ?: null,
            'average_score' => $this->when(
                $this->status->value === 'evaluated' || $this->status->value === 'accepted',
                fn () => $this->averageScore(),
            ),
            'is_winner' => $resultsPublished && $this->prize_id !== null,
            'rank' => $resultsPublished ? $this->rank() : null,
        ];
    }

    private function rank(): ?int
    {
        $scores = $this->competition->submissions()
            ->get()
            ->map(fn ($submission) => $submission->averageScore())
            ->filter(fn ($score) => $score !== null)
            ->sortByDesc(fn ($score) => $score)
            ->values();

        $ownScore = $this->averageScore();

        if ($ownScore === null) {
            return null;
        }

        $index = $scores->search($ownScore);

        return $index === false ? null : (int) $index + 1;
    }
}
