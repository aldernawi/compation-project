<?php

namespace App\Models;

use App\Enums\SubmissionKind;
use Database\Factories\CompetitionTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property SubmissionKind $submission_kind
 */
class CompetitionType extends Model
{
    /** @use HasFactory<CompetitionTypeFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'submission_kind'];

    protected function casts(): array
    {
        return [
            'submission_kind' => SubmissionKind::class,
        ];
    }

    /**
     * @return HasMany<Competition, $this>
     */
    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }
}
