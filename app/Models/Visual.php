<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[Fillable([
    'title',
    'slug',
    'category_id',
    'description',
])]
class Visual extends Model
{
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 저장된 HTML 문서 파일. 업로드 시 생성되고 웹서버가 정적으로 서빙한다.
     */
    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    /**
     * 제목·설명을 키워드로 필터. LIKE 와일드카드(%, _)와 이스케이프 문자는
     * 리터럴로 취급되도록 느낌표(!)로 이스케이프한다.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $escaped = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term) . '%';

        // ANSI SQL 표준 ESCAPE '!'를 사용하여 SQLite·MariaDB 모두에서 안전하게 동작
        return $query->where(function (Builder $q) use ($escaped) {
            $q->whereRaw("title LIKE ? ESCAPE '!'", [$escaped])
                ->orWhereRaw("description LIKE ? ESCAPE '!'", [$escaped]);
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
