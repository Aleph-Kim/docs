<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicVisualTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_visuals(): void
    {
        $this->makeVisual(['title' => 'Alpha Chart']);

        $this->get(route('visuals.index'))
            ->assertOk()
            ->assertSee('Alpha Chart');
    }

    public function test_keyword_search_filters_by_title(): void
    {
        $this->makeVisual(['title' => 'Revenue Map']);
        $this->makeVisual(['title' => 'User Funnel']);

        $this->get(route('visuals.index', ['q' => 'Revenue']))
            ->assertOk()
            ->assertSee('Revenue Map')
            ->assertDontSee('User Funnel');
    }

    public function test_wildcard_characters_in_search_are_escaped(): void
    {
        $this->makeVisual(['title' => '100% Coverage']);
        $this->makeVisual(['title' => 'Plain Title']);

        // '%' 등 LIKE 와일드카드의 리터럴 검색 매칭
        $this->get(route('visuals.index', ['q' => '100%']))
            ->assertOk()
            ->assertSee('100% Coverage')
            ->assertDontSee('Plain Title');
    }

    public function test_category_filter(): void
    {
        $a = Category::create(['name' => 'Cat A', 'slug' => 'cat-a']);
        $b = Category::create(['name' => 'Cat B', 'slug' => 'cat-b']);
        $this->makeVisual(['title' => 'In A', 'slug' => 'in-a', 'category_id' => $a->id]);
        $this->makeVisual(['title' => 'In B', 'slug' => 'in-b', 'category_id' => $b->id]);

        $this->get(route('visuals.index', ['category' => $a->id]))
            ->assertOk()
            ->assertSee('In A')
            ->assertDontSee('In B');
    }

    public function test_show_page_points_iframe_at_stored_html_file(): void
    {
        $visual = $this->makeVisual(['title' => 'Raw Doc']);
        $path = $visual->file->getRawOriginal('url');

        $response = $this->get(route('visuals.show', $visual->slug));

        $response->assertOk();
        $response->assertSee(route('visuals.render', $visual), false);
        $response->assertSee('sandbox="allow-scripts allow-popups"', false);

        Storage::assertExists($path);
        $this->assertStringContainsString('Raw Doc', Storage::get($path));
    }

    public function test_render_serves_html_file_with_html_content_type(): void
    {
        $visual = $this->makeVisual(['title' => 'Render Doc']);

        $response = $this->get(route('visuals.render', $visual->slug));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
        $response->assertHeader('Content-Security-Policy', 'sandbox allow-scripts allow-popups');
        $response->assertHeader('ETag');
        $response->assertSee('Render Doc', false);
    }

    public function test_render_caches_file_content(): void
    {
        $visual = $this->makeVisual(['title' => 'Cached Doc']);
        $cacheKey = "visual:content:{$visual->id}";

        $this->get(route('visuals.render', $visual->slug))->assertOk();

        $this->assertTrue(\Illuminate\Support\Facades\Cache::has($cacheKey));
        $this->assertStringContainsString('Cached Doc', \Illuminate\Support\Facades\Cache::get($cacheKey));
    }
}
