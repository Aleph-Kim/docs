<?php

namespace Tests\Feature\Storage;

use App\Models\Category;
use App\Models\File;
use App\Models\Visual;
use App\Services\FileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupabaseStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_filesystems_config_has_s3_disk(): void
    {
        $config = config('filesystems.disks.s3');

        $this->assertNotNull($config);
        $this->assertSame('s3', $config['driver']);
        $this->assertFalse($config['throw']);
    }

    public function test_html_upload_stores_file_in_storage(): void
    {
        Config::set('app.env', 'production');

        $category = Category::create(['name' => '인터랙티브', 'slug' => 'interactive']);
        $visual = Visual::create([
            'title' => 'Production Chart',
            'slug' => 'production-chart',
            'category_id' => $category->id,
        ]);

        $file = UploadedFile::fake()->createWithContent('chart.html', '<html><body>Chart</body></html>');
        $fileModel = new File;

        FileService::handleUploadFile($fileModel, $file, 'production/'.$visual->getTable().'/2026/08/29', 'html_file');
        $visual->file()->save($fileModel);

        $this->assertStringStartsWith('production/visuals/2026/08/29/', $fileModel->getRawOriginal('url'));
        $this->assertStringContainsString('production/visuals/2026/08/29/', $fileModel->url);
        $this->assertSame('text/html', $fileModel->mime_type);
        $this->assertSame('chart.html', $fileModel->origin_name);

        Storage::assertExists($fileModel->getRawOriginal('url'));
    }

    public function test_file_model_url_accessor_generates_correct_storage_url(): void
    {
        Config::set('app.env', 'local');

        $path = 'local/visuals/2026/08/29/sample.html';
        Storage::put($path, '<html>sample</html>');

        $file = new File([
            'url' => $path,
            'name' => 'sample.html',
            'origin_name' => 'sample.html',
            'mime_type' => 'text/html',
            'file_size' => 18,
            'field_name' => 'html_file',
        ]);

        $expectedUrl = Storage::url($path);
        $this->assertNotEmpty($file->url);
        $this->assertSame($expectedUrl, $file->url);
    }

    public function test_file_service_delete_removes_storage_objects_and_records(): void
    {
        $visual = $this->makeVisual(['title' => 'Delete Item']);
        $file = $visual->file;
        $path = $file->getRawOriginal('url');

        Storage::assertExists($path);
        $this->assertDatabaseHas('files', ['id' => $file->id]);

        FileService::delete($file->id);

        Storage::assertMissing($path);
        $this->assertDatabaseMissing('files', ['id' => $file->id]);
    }

    public function test_file_service_delete_by_model_removes_associated_files(): void
    {
        $visual = $this->makeVisual(['title' => 'Delete By Model']);
        $path = $visual->file->getRawOriginal('url');

        Storage::assertExists($path);
        $this->assertDatabaseHas('files', ['id' => $visual->file->id]);

        FileService::deleteByModel($visual);

        Storage::assertMissing($path);
        $this->assertDatabaseMissing('files', ['id' => $visual->file->id]);
    }

    public function test_file_updating_replaces_old_physical_file(): void
    {
        $visual = $this->makeVisual(['title' => 'Replace File']);
        $file = $visual->file;
        $oldPath = $file->getRawOriginal('url');
        $newPath = 'local/visuals/new.html';

        Storage::put($newPath, 'new');

        $file->url = $newPath;
        $file->save();

        Storage::assertMissing($oldPath);
        Storage::assertExists($newPath);
    }
}
