<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'terms' => $this->terms,
            'status' => $this->status->value,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'requires_approval' => $this->requires_approval,
            'evaluation_method' => $this->evaluation_method,
            'organizer' => $this->whenLoaded('organizer', fn () => [
                'id' => $this->organizer->id,
                'name' => $this->organizer->name,
            ]),
            'competition_type' => $this->whenLoaded('competitionType', fn () => [
                'id' => $this->competitionType->id,
                'name' => $this->competitionType->name,
                'submission_kind' => $this->competitionType->submission_kind->value,
            ]),
            'prizes' => PrizeResource::collection($this->whenLoaded('prizes')),
        ];
    }
}
