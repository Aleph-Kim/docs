<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Visual;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VisualCrudTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): self
    {
        return $this->withSession(['is_admin' => true]);
    }

    public function test_admin_can_create_visual_and_slug_is_auto_generated(): void
    {
        $category = Category::create(['name' => '대시보드', 'slug' => 'dashboard']);

        $this->actingAsAdmin()->post(route('admin.visuals.store'), [
            'title' => 'Sales Dashboard',
            'category_id' => $category->id,
            'html' => '<!DOCTYPE html><html><body>hi</body></html>',
        ])->assertRedirect(route('admin.visuals.index'));

        $this->assertDatabaseHas('visuals', [
            'title' => 'Sales Dashboard',
            'slug' => 'sales-dashboard',
            'category_id' => $category->id,
        ]);
    }

    public function test_duplicate_title_gets_suffixed_slug(): void
    {
        $category = Category::create(['name' => '실험', 'slug' => 'experiment']);
        Visual::create([
            'title' => 'Test', 'slug' => 'test', 'category_id' => $category->id, 'html' => '<html></html>',
        ]);

        $this->actingAsAdmin()->post(route('admin.visuals.store'), [
            'title' => 'Test',
            'category_id' => $category->id,
            'html' => '<html>2</html>',
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

        $this->assertSame($content, Visual::firstWhere('title', 'From File')->html);
    }

    public function test_admin_can_update_and_delete_visual(): void
    {
        $category = Category::create(['name' => '다이어그램', 'slug' => 'diagram']);
        $visual = Visual::create([
            'title' => 'Old', 'slug' => 'old', 'category_id' => $category->id, 'html' => '<html></html>',
        ]);

        $this->actingAsAdmin()->put(route('admin.visuals.update', $visual), [
            'title' => 'New Title',
            'slug' => 'old',
            'category_id' => $category->id,
            'html' => '<html>new</html>',
        ])->assertRedirect(route('admin.visuals.index'));

        $this->assertDatabaseHas('visuals', ['id' => $visual->id, 'title' => 'New Title']);

        $this->actingAsAdmin()->delete(route('admin.visuals.destroy', $visual))
            ->assertRedirect(route('admin.visuals.index'));

        $this->assertDatabaseMissing('visuals', ['id' => $visual->id]);
    }
}
