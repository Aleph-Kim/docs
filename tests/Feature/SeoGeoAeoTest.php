<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoGeoAeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_valid_xml_with_all_urls(): void
    {
        $cat = Category::create(['name' => 'Architecture', 'slug' => 'architecture']);
        $visual = $this->makeVisual([
            'title' => 'System Design',
            'slug' => 'system-design',
            'category_id' => $cat->id,
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
        $response->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'Sitemap response is not valid XML');
        $response->assertSee(route('visuals.index'), false);
        $response->assertSee(route('visuals.index', ['category' => $cat->id]), false);
        $response->assertSee(route('visuals.show', $visual->slug), false);
        $response->assertSee('<changefreq>daily</changefreq>', false);
        $response->assertSee('<changefreq>weekly</changefreq>', false);
        $response->assertSee('<priority>1.0</priority>', false);
    }

    public function test_rss_feed_returns_valid_xml_with_items(): void
    {
        $cat = Category::create(['name' => 'Backend', 'slug' => 'backend']);
        $visual = $this->makeVisual([
            'title' => 'Laravel Pipeline Pattern',
            'slug' => 'laravel-pipeline-pattern',
            'category_id' => $cat->id,
            'description' => 'A deep dive into pipeline pattern.',
        ]);

        $response = $this->get(route('rss'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
        $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
        $response->assertSee('<rss version="2.0"', false);
        $response->assertSee('<title>Docs</title>', false);
        $response->assertSee('<title>Laravel Pipeline Pattern</title>', false);
        $response->assertSee(route('visuals.show', $visual->slug), false);
        $response->assertSee('Backend', false);
        $response->assertSee('A deep dive into pipeline pattern.', false);

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'RSS feed response is not valid XML');
        $this->assertEquals('Docs', (string) $xml->channel->title);
        $this->assertEquals('Laravel Pipeline Pattern', (string) $xml->channel->item[0]->title);
    }

    public function test_llms_txt_and_llms_full_txt_endpoints_return_markdown(): void
    {
        $cat = Category::create(['name' => 'DevOps', 'slug' => 'devops']);
        $visual = $this->makeVisual([
            'title' => 'Kubernetes Guide',
            'slug' => 'kubernetes-guide',
            'category_id' => $cat->id,
            'description' => 'A visual overview of K8s architecture.',
        ]);

        $res1 = $this->get(route('llms.txt'));
        $res1->assertOk();
        $res1->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $res1->assertSee('# Docs');
        $res1->assertSee('Kubernetes Guide');
        $res1->assertSee('DevOps');
        $res1->assertSee('A visual overview of K8s architecture.');

        $res2 = $this->get(route('llms-full.txt'));
        $res2->assertOk();
        $res2->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $res2->assertSee('# Docs — Full Archive Catalog');
        $res2->assertSee('### [Kubernetes Guide]');
        $res2->assertSee(route('visuals.render', $visual->slug));
    }

    public function test_index_page_contains_seo_geo_meta_and_json_ld(): void
    {
        $cat = Category::create(['name' => 'Backend', 'slug' => 'backend']);
        $visual = $this->makeVisual([
            'title' => 'API Design Pattern',
            'slug' => 'api-design-pattern',
            'category_id' => $cat->id,
        ]);

        $response = $this->get(route('visuals.index'));

        $response->assertOk();
        // Robots tag
        $response->assertSee('<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">', false);
        // Canonical tag
        $response->assertSee('<link rel="canonical" href="' . route('visuals.index') . '">', false);
        // Open Graph & Image
        $response->assertSee('<meta property="og:site_name" content="Docs">', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:image" content="http://localhost/og-image.png">', false);
        // Twitter
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('<meta name="twitter:image" content="http://localhost/og-image.png">', false);
        // Favicons & Manifest
        $response->assertSee('<link rel="icon" href="/favicon.svg" type="image/svg+xml">', false);
        $response->assertSee('<link rel="manifest" href="/site.webmanifest">', false);
        $response->assertSee('<link rel="alternate" type="application/rss+xml" title="Docs RSS Feed" href="' . route('rss') . '">', false);
        // JSON-LD Structured Data
        $response->assertSee('"@type": "WebSite"', false);
        $response->assertSee('"@type": "SearchAction"', false);
        $response->assertSee('"@type": "CollectionPage"', false);
        $response->assertSee('"@type": "ItemList"', false);
        $response->assertSee('API Design Pattern', false);
    }

    public function test_search_query_applies_noindex_follow(): void
    {
        $response = $this->get(route('visuals.index', ['q' => 'somequery']));

        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    public function test_show_page_contains_article_metadata_breadcrumbs_and_schema(): void
    {
        $cat = Category::create(['name' => 'AI', 'slug' => 'ai']);
        $visual = $this->makeVisual([
            'title' => 'Transformer Attention Mechanism',
            'slug' => 'transformer-attention',
            'category_id' => $cat->id,
            'description' => 'Step-by-step breakdown of multi-head self-attention.',
        ]);

        $response = $this->get(route('visuals.show', $visual->slug));

        $response->assertOk();
        // Page title & description
        $response->assertSee('<title>Transformer Attention Mechanism - Docs</title>', false);
        $response->assertSee('Step-by-step breakdown of multi-head self-attention.');
        // OG tags
        $response->assertSee('<meta property="og:type" content="article">', false);
        $response->assertSee('<meta property="article:section" content="AI">', false);
        $response->assertSee('<meta property="article:published_time"', false);
        // Semantic UI
        $response->assertSee('class="breadcrumbs"', false);
        $response->assertSee('itemprop="headline"', false);
        $response->assertSee('itemprop="datePublished"', false);
        // JSON-LD schema
        $response->assertSee('"@type": "TechArticle"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
        $response->assertSee('Transformer Attention Mechanism', false);
    }

    public function test_render_endpoint_returns_x_robots_tag_noindex(): void
    {
        $visual = $this->makeVisual(['title' => 'Render Test']);

        $response = $this->get(route('visuals.render', $visual->slug));

        $response->assertOk();
        $response->assertHeader('X-Robots-Tag', 'noindex, follow');
    }

    public function test_admin_pages_contain_noindex_nofollow_meta(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }
}
