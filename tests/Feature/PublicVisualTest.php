<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Visual;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicVisualTest extends TestCase
{
    use RefreshDatabase;

    private function makeVisual(string $title, ?string $description = null): Visual
    {
        $category = Category::firstOrCreate(['slug' => 'general'], ['name' => 'General']);

        return Visual::create([
            'title' => $title,
            'slug' => Str::slug($title),
            'category_id' => $category->id,
            'description' => $description,
            'html' => '<!DOCTYPE html><html><body>'.$title.'</body></html>',
        ]);
    }

    public function test_index_lists_visuals(): void
    {
        $this->makeVisual('Alpha Chart');

        $this->get(route('visuals.index'))
            ->assertOk()
            ->assertSee('Alpha Chart');
    }

    public function test_keyword_search_filters_by_title(): void
    {
        $this->makeVisual('Revenue Map');
        $this->makeVisual('User Funnel');

        $this->get(route('visuals.index', ['q' => 'Revenue']))
            ->assertOk()
            ->assertSee('Revenue Map')
            ->assertDontSee('User Funnel');
    }

    public function test_wildcard_characters_in_search_are_escaped(): void
    {
        $this->makeVisual('100% Coverage');
        $this->makeVisual('Plain Title');

        // '%' 가 LIKE 와일드카드로 새지 않고 리터럴로 매칭되어야 한다
        $this->get(route('visuals.index', ['q' => '100%']))
            ->assertOk()
            ->assertSee('100% Coverage')
            ->assertDontSee('Plain Title');
    }

    public function test_category_filter(): void
    {
        $a = Category::create(['name' => 'Cat A', 'slug' => 'cat-a']);
        $b = Category::create(['name' => 'Cat B', 'slug' => 'cat-b']);
        Visual::create(['title' => 'In A', 'slug' => 'in-a', 'category_id' => $a->id, 'html' => '<html></html>']);
        Visual::create(['title' => 'In B', 'slug' => 'in-b', 'category_id' => $b->id, 'html' => '<html></html>']);

        $this->get(route('visuals.index', ['category' => $a->id]))
            ->assertOk()
            ->assertSee('In A')
            ->assertDontSee('In B');
    }

    public function test_raw_endpoint_returns_html_with_frame_headers(): void
    {
        $visual = $this->makeVisual('Raw Doc');

        $response = $this->get(route('visuals.raw', $visual->slug));

        $response->assertOk();
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $response->assertSee('Raw Doc', false);
    }
}
