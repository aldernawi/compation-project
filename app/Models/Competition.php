<?php

namespace App\Models;

use App\Enums\CompetitionStatus;
use Database\Factories\CompetitionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    /** @use HasFactory<CompetitionFactory> */
    use HasFactory;

    protected $fillable = [
        'organizer_id', 'competition_type_id', 'title', 'description', 'terms',
        'starts_at', 'ends_at', 'status', 'requires_approval', 'evaluation_method',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => CompetitionStatus::class,
            'requires_approval' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * @return BelongsTo<CompetitionType, $this>
     */
    public function competitionType(): BelongsTo
    {
        return $this->belongsTo(CompetitionType::class);
    }

    /**
     * @return HasMany<Prize, $this>
     */
    public function prizes(): HasMany
    {
        return $this->hasMany(Prize::class);
    }

    /**
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'competition_judge', 'competition_id', 'judge_id');
    }
}
