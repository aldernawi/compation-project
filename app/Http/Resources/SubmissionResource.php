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
        ];
    }
}
