<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): self
    {
        return $this->withSession(['is_admin' => true]);
    }

    public function test_category_index_shows_delete_button_only_when_visuals_count_is_zero(): void
    {
        $categoryWithVisual = Category::factory()->create(['name' => '문서있음', 'slug' => 'has-visual']);
        $this->makeVisual(['category_id' => $categoryWithVisual->id]);

        $categoryEmpty = Category::factory()->create(['name' => '문서없음', 'slug' => 'empty-cat']);

        $response = $this->actingAsAdmin()->get(route('admin.categories.index'));

        $response->assertOk();

        $html = $response->getContent();

        // 문서가 있는 카테고리 행에는 삭제 버튼/폼이 노출되지 않음
        preg_match('/<tr id="cat-row-' . $categoryWithVisual->id . '">(.*?)<\/tr>/s', $html, $withVisualRow);
        $this->assertNotEmpty($withVisualRow);
        $this->assertStringNotContainsString('category-delete-form', $withVisualRow[1]);
        $this->assertStringNotContainsString('삭제', $withVisualRow[1]);

        // 문서가 없는 카테고리 행에는 삭제 버튼/폼이 정상 노출됨
        preg_match('/<tr id="cat-row-' . $categoryEmpty->id . '">(.*?)<\/tr>/s', $html, $emptyRow);
        $this->assertNotEmpty($emptyRow);
        $this->assertStringContainsString('category-delete-form', $emptyRow[1]);
        $this->assertStringContainsString('삭제', $emptyRow[1]);
    }

    public function test_admin_can_delete_category_without_visuals(): void
    {
        $category = Category::factory()->create(['name' => '삭제대상', 'slug' => 'to-delete']);

        $response = $this->actingAsAdmin()
            ->deleteJson(route('admin.categories.destroy', $category));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_visuals(): void
    {
        $category = Category::factory()->create(['name' => '문서있음', 'slug' => 'has-doc']);
        $this->makeVisual(['category_id' => $category->id]);

        $response = $this->actingAsAdmin()
            ->deleteJson(route('admin.categories.destroy', $category));

        $response->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
