<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Database\Factories\SubmissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int $competition_id
 * @property int $participant_id
 * @property SubmissionStatus $status
 * @property string|null $text_content
 * @property string|null $link_url
 */
class Submission extends Model implements HasMedia
{
    /** @use HasFactory<SubmissionFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'competition_id', 'participant_id', 'prize_id', 'status', 'text_content', 'link_url', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubmissionStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Competition, $this>
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    /**
     * @return BelongsTo<Prize, $this>
     */
    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }

    /**
     * @return HasMany<Evaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function averageScore(): ?float
    {
        $average = $this->evaluations()->whereNotNull('score')->avg('score');

        return $average === null ? null : (float) $average;
    }
}
