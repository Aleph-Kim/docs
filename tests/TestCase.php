<?php

namespace Tests;

use App\Models\Category;
use App\Models\File;
use App\Models\Visual;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 실제 스토리지 오염 방지 (기본 디스크 fake)
        Storage::fake();
    }

    // 테스트용 HTML 첨부 Visual 생성
    protected function makeVisual(array $attributes = []): Visual
    {
        $category = Category::firstOrCreate(['slug' => 'general'], ['name' => 'General']);
        $title = $attributes['title'] ?? 'Visual '.Str::random(5);

        $visual = Visual::create([
            'title' => $title,
            'slug' => $attributes['slug'] ?? Str::slug($title),
            'category_id' => $attributes['category_id'] ?? $category->id,
            'description' => $attributes['description'] ?? null,
        ]);

        $html = $attributes['html'] ?? '<!DOCTYPE html><html><body>'.$title.'</body></html>';
        $env = config('app.env') === 'production' ? 'production' : 'local';
        $path = $env.'/visuals/'.date('Y/m/d').'/'.Str::random(40).'.html';
        Storage::put($path, $html, [
            'visibility' => 'public',
            'ContentType' => 'text/html',
            'mimetype' => 'text/html',
        ]);

        $visual->file()->save(new File([
            'url' => $path,
            'name' => basename($path),
            'origin_name' => 'viz.html',
            'mime_type' => 'text/html',
            'file_size' => strlen($html),
            'field_name' => 'html_file',
        ]));

        return $visual->load('file');
    }
}
