<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompetitionResource;
use App\Models\Competition;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompetitionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CompetitionResource::collection(
            Competition::query()->with('competitionType')->latest()->paginate(15)
        );
    }

    public function show(Competition $competition): CompetitionResource
    {
        $competition->load(['competitionType', 'prizes' => fn ($query) => $query->orderBy('rank')]);

        return new CompetitionResource($competition);
    }
}
