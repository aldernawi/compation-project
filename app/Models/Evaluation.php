<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use Database\Factories\EvaluationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $submission_id
 * @property int $judge_id
 * @property int|null $score
 * @property string|null $notes
 * @property EvaluationStatus $status
 */
class Evaluation extends Model
{
    /** @use HasFactory<EvaluationFactory> */
    use HasFactory;

    protected $fillable = ['submission_id', 'judge_id', 'score', 'notes', 'status'];

    protected function casts(): array
    {
        return [
            'status' => EvaluationStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Submission, $this>
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function judge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'judge_id');
    }
}
