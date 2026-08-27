<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'origin_name', 'url', 'file_size', 'mime_type',
        'fileable_type', 'fileable_id', 'field_name',
    ];

    protected static function booted(): void
    {
        // 파일 교체 시 예전 실물 삭제. url 접근자는 전체 URL 을 반환하므로 raw 값을 쓴다
        static::updating(function (File $file): void {
            if ($file->isDirty('url') && ($old = $file->getRawOriginal('url'))) {
                Storage::disk('public')->delete($old);
            }
        });

        // hard delete 시에만 실물 삭제(soft delete 는 파일을 남긴다)
        static::deleting(function (File $file): void {
            if ($file->isForceDeleting() && ($path = $file->getRawOriginal('url'))) {
                Storage::disk('public')->delete($path);
            }
        });
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn($value) => $value ? Storage::disk('public')->url($value) : null);
    }
}
