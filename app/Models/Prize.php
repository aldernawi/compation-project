<?php

namespace App\Models;

use Database\Factories\PrizeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prize extends Model
{
    /** @use HasFactory<PrizeFactory> */
    use HasFactory;

    protected $fillable = ['competition_id', 'title', 'description', 'winners_count', 'rank'];

    /**
     * @return BelongsTo<Competition, $this>
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
