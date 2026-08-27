<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<File>
 *
 * Storage::fake('public') 가 활성화된 테스트에서만 사용한다.
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        $content = '<!DOCTYPE html><html><body>'.fake()->sentence().'</body></html>';
        $upload = UploadedFile::fake()->createWithContent('viz.html', $content);
        $stored = Storage::disk('public')->putFileAs('visuals/'.date('Y/m/d'), $upload, Str::random(40).'.html');

        return [
            'url' => $stored,
            'name' => basename($stored),
            'origin_name' => 'viz.html',
            'mime_type' => 'text/html',
            'file_size' => Storage::disk('public')->size($stored),
            'field_name' => 'html_file',
        ];
    }
}
