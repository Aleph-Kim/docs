<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * 요청의 파일 필드를 fileable 모델에 저장한다. 기존 파일이 있으면 교체.
     */
    public static function saveOrUpdate(Model $fileable, FormRequest $request, array|string $fileFieldNames): void
    {
        $env = config('app.env') === 'production' ? 'production' : 'local';
        $path = $env.'/'.$fileable->getTable().'/'.date('Y/m/d');

        foreach (Arr::wrap($fileFieldNames) as $fieldName) {
            $files = $request->validated($fieldName);

            if (empty($files)) {
                continue;
            }

            foreach (Arr::wrap($files) as $file) {
                $target = $fileable->file instanceof Collection
                    ? $fileable->file->where('field_name', $fieldName)->first()
                    : $fileable->file;

                if ($target) { // 기존 파일 교체
                    self::handleUploadFile($target, $file, $path, $fieldName);
                    $target->update(); // File::updating 훅이 예전 실물을 삭제

                    continue;
                }

                $target = new File;
                self::handleUploadFile($target, $file, $path, $fieldName);
                $fileable->file()->save($target);
            }
        }
    }

    /**
     * 파일을 저장하고 모델 값을 채운다.
     */
    public static function handleUploadFile(File $fileModel, UploadedFile $file, string $path, string $fieldName): void
    {
        // Apache 가 확장자로 Content-Type 을 판단하므로 확장자를 보존한다(기본 html)
        $ext = strtolower($file->getClientOriginalExtension() ?: 'html');
        $filePath = Storage::putFileAs($path, $file, Str::random(40).'.'.$ext);

        $fileModel->name = basename($filePath);
        $fileModel->origin_name = $file->getClientOriginalName();
        $fileModel->mime_type = $file->getClientMimeType() ?: 'text/html';
        $fileModel->url = $filePath;
        $fileModel->file_size = $file->getSize();
        $fileModel->field_name = $fieldName;
    }

    /**
     * 파일 ID 기반 실물과 DB 레코드 일괄 삭제.
     */
    public static function delete(array|string|int $fileIds): void
    {
        $ids = Arr::wrap($fileIds);

        if (empty($ids)) {
            return;
        }

        $files = File::whereIn('id', $ids)->get(['id', 'url']);

        if ($files->isEmpty()) {
            return;
        }

        // getRawOriginal 사용 — pluck('url') 은 접근자를 태워 전체 URL 을 넘긴다
        Storage::delete(
            $files->map(fn (File $f) => $f->getRawOriginal('url'))->filter()->all()
        );

        File::whereIn('id', $files->pluck('id')->all())->forceDelete();
    }

    /**
     * 부모 모델 기반 실물과 DB 레코드 일괄 삭제.
     */
    public static function deleteByModel(Collection|Model|array $models): void
    {
        $models = Collection::wrap($models);

        if ($models->isEmpty()) {
            return;
        }

        $models->loadMissing('file');
        $files = $models->pluck('file')->flatten()->filter();

        if ($files->isEmpty()) {
            return;
        }

        Storage::delete(
            $files->map(fn (File $file) => $file->getRawOriginal('url'))->filter()->all()
        );

        File::whereIn('id', $files->pluck('id')->all())->forceDelete();
    }
}
