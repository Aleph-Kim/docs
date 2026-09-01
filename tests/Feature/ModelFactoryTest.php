<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\File;
use App\Models\Visual;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModelFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_factory_creates_valid_category(): void
    {
        $category = Category::factory()->create();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
        ]);
        $this->assertNotEmpty($category->name);
        $this->assertNotEmpty($category->slug);
    }

    public function test_visual_factory_creates_visual_with_category(): void
    {
        $visual = Visual::factory()->create();

        $this->assertDatabaseHas('visuals', [
            'id' => $visual->id,
            'title' => $visual->title,
            'slug' => $visual->slug,
            'category_id' => $visual->category_id,
        ]);
        $this->assertNotNull($visual->category);
    }

    public function test_visual_factory_with_file_creates_file_and_storage_content(): void
    {
        $customHtml = '<!DOCTYPE html><html><body><h1>Custom Test</h1></body></html>';
        $visual = Visual::factory()->withFile($customHtml)->create();

        $this->assertNotNull($visual->file);
        $this->assertDatabaseHas('files', [
            'id' => $visual->file->id,
            'fileable_id' => $visual->id,
            'fileable_type' => Visual::class,
            'field_name' => 'html_file',
        ]);

        $path = $visual->file->getRawOriginal('url');
        Storage::assertExists($path);
        $this->assertSame($customHtml, Storage::get($path));
    }

    public function test_file_factory_creates_file_with_custom_content(): void
    {
        $content = '<div>Hello World</div>';
        $file = File::factory()->content($content, 'custom.html')->create([
            'fileable_type' => Visual::class,
            'fileable_id' => 1,
        ]);

        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'origin_name' => 'custom.html',
            'file_size' => strlen($content),
        ]);
        $path = $file->getRawOriginal('url');
        Storage::assertExists($path);
        $this->assertSame($content, Storage::get($path));
    }
}
