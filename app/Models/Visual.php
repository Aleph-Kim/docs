<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visual extends Model
{
    protected $fillable = ['title', 'slug', 'category_id', 'description', 'html'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 제목·설명을 키워드로 필터. LIKE 와일드카드(%, _)와 이스케이프 문자는
     * 리터럴로 취급되도록 백슬래시 이스케이프한다.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $escaped = '%'.addcslashes($term, '%_\\').'%';

        // ESCAPE 절을 명시해야 SQLite·MariaDB 모두에서 백슬래시가 이스케이프 문자로 동작한다
        return $query->where(function (Builder $q) use ($escaped) {
            $q->whereRaw("title LIKE ? ESCAPE '\\'", [$escaped])
                ->orWhereRaw("description LIKE ? ESCAPE '\\'", [$escaped]);
        });
    }

    public function scopeInCategory(Builder $query, mixed $categoryId): Builder
    {
        if (blank($categoryId)) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }
}
