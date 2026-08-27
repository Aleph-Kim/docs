<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Visual;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VisualCrudTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): self
    {
        return $this->withSession(['is_admin' => true]);
    }

    private function htmlUpload(string $content = '<!DOCTYPE html><html><body>hi</body></html>'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('viz.html', $content);
    }

    public function test_admin_can_create_visual_and_slug_is_auto_generated(): void
    {
        $category = Category::create(['name' => '대시보드', 'slug' => 'dashboard']);

        $this->actingAsAdmin()->post(route('admin.visuals.store'), [
            'title' => 'Sales Dashboard',
            'category_id' => $category->id,
            'html_file' => $this->htmlUpload(),
        ])->assertRedirect(route('admin.visuals.index'));

        $this->assertDatabaseHas('visuals', [
            'title' => 'Sales Dashboard',
            'slug' => 'sales-dashboard',
            'category_id' => $category->id,
        ]);

        $visual = Visual::firstWhere('slug', 'sales-dashboard');
        $this->assertDatabaseHas('files', [
            'fileable_id' => $visual->id,
            'fileable_type' => Visual::class,
            'field_name' => 'html_file',
            'mime_type' => 'text/html',
        ]);
        $path = $visual->file->getRawOriginal('url');
        Storage::disk('public')->assertExists($path);
        $this->assertMatchesRegularExpression('#^visuals/\d{4}/\d{2}/\d{2}/[A-Za-z0-9]{40}\.html$#', $path);
    }

    public function test_duplicate_title_gets_suffixed_slug(): void
    {
        $category = Category::create(['name' => '실험', 'slug' => 'experiment']);
        $this->makeVisual(['title' => 'Test', 'slug' => 'test', 'category_id' => $category->id]);

        $this->actingAsAdmin()->post(route('admin.visuals.store'), [
            'title' => 'Test',
            'category_id' => $category->id,
            'html_file' => $this->htmlUpload('<html>2</html>'),
        ]);

        $this->assertDatabaseHas('visuals', ['title' => 'Test', 'slug' => 'test-2']);
    }

    public function test_uploaded_html_file_content_is_stored(): void
    {
        $category = Category::create(['name' => '인터랙티브', 'slug' => 'interactive']);
        $content = '<!DOCTYPE html><html><head><title>up</title></head><body>uploaded</body></html>';

        $this->actingAsAdmin()->post(route('admin.visuals.store'), [
            'title' => 'From File',
            'category_id' => $category->id,
            'html_file' => UploadedFile::fake()->createWithContent('viz.html', $content),
        ])->assertRedirect(route('admin.visuals.index'));

        $visual = Visual::firstWhere('title', 'From File');
        $this->assertNotNull($visual->file);

        $path = $visual->file->getRawOriginal('url');
        Storage::disk('public')->assertExists($path);
        $this->assertSame($content, Storage::disk('public')->get($path));
        $this->assertSame('viz.html', $visual->file->origin_name);
        $this->assertSame(strlen($content), $visual->file->file_size);
    }

    public function test_admin_can_update_and_delete_visual(): void
    {
        $category = Category::create(['name' => '다이어그램', 'slug' => 'diagram']);
        $visual = $this->makeVisual(['title' => 'Old', 'slug' => 'old', 'category_id' => $category->id]);
        $oldPath = $visual->file->getRawOriginal('url');

        $this->actingAsAdmin()->put(route('admin.visuals.update', $visual), [
            'title' => 'New Title',
            'slug' => 'old',
            'category_id' => $category->id,
            'html_file' => $this->htmlUpload('<html>new</html>'),
        ])->assertRedirect(route('admin.visuals.index'));

        $this->assertDatabaseHas('visuals', ['id' => $visual->id, 'title' => 'New Title']);

        $visual->refresh()->load('file');
        $newPath = $visual->file->getRawOriginal('url');
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
        $this->assertSame('<html>new</html>', Storage::disk('public')->get($newPath));

        $this->actingAsAdmin()->delete(route('admin.visuals.destroy', $visual))
            ->assertRedirect(route('admin.visuals.index'));

        $this->assertDatabaseMissing('visuals', ['id' => $visual->id]);
        $this->assertDatabaseMissing('files', ['fileable_id' => $visual->id, 'fileable_type' => Visual::class]);
        Storage::disk('public')->assertMissing($newPath);
    }
}
