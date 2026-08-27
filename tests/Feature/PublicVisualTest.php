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
        $response->assertSee($visual->file->url, false);
        $response->assertSee('sandbox="allow-scripts allow-popups"', false);

        Storage::disk('public')->assertExists($path);
        $this->assertStringContainsString('Raw Doc', Storage::disk('public')->get($path));
    }
}
