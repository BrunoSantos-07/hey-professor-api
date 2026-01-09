<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Builder, Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $table = 'questions';

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', '=', 'published');
    }

    public function scopeSearch(Builder $query, ?string $search = null): Builder
    {
        return $query->when(
            $search,
            fn (Builder $q) => $q->where('question', 'like', "%{$search}%")
        );
    }

}
