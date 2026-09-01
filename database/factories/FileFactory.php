<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        $content = '<!DOCTYPE html><html><body>'.fake()->sentence().'</body></html>';
        $upload = UploadedFile::fake()->createWithContent('viz.html', $content);
        $env = config('app.env') === 'production' ? 'production' : 'local';
        $targetDirectory = $env.'/visuals/'.date('Y/m/d');
        $stored = Storage::putFileAs($targetDirectory, $upload, Str::random(40).'.html', [
            'visibility' => 'public',
            'ContentType' => 'text/html',
            'mimetype' => 'text/html',
        ]);

        return [
            'url' => $stored,
            'name' => basename($stored),
            'origin_name' => 'viz.html',
            'mime_type' => 'text/html',
            'file_size' => Storage::size($stored),
            'field_name' => 'html_file',
        ];
    }

    public function content(string $content, string $originName = 'viz.html'): static
    {
        return $this->state(function () use ($content, $originName) {
            $upload = UploadedFile::fake()->createWithContent($originName, $content);
            $env = config('app.env') === 'production' ? 'production' : 'local';
            $targetDirectory = $env.'/visuals/'.date('Y/m/d');
            $stored = Storage::putFileAs($targetDirectory, $upload, Str::random(40).'.html', [
                'visibility' => 'public',
                'ContentType' => 'text/html',
                'mimetype' => 'text/html',
            ]);

            return [
                'url' => $stored,
                'name' => basename($stored),
                'origin_name' => $originName,
                'mime_type' => 'text/html',
                'file_size' => strlen($content),
                'field_name' => 'html_file',
            ];
        });
    }
}
