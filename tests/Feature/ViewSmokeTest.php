<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_pages_render(): void
    {
        config(['admin.id' => 'admin', 'admin.password' => 'secret']);
        $cat = Category::create(['name' => 'Cat', 'slug' => 'cat']);
        $visual = $this->makeVisual([
            'title' => 'Doc', 'slug' => 'doc', 'category_id' => $cat->id, 'description' => 'desc',
        ]);

        $this->get(route('visuals.index'))->assertOk();
        $this->get(route('visuals.show', $visual->slug))->assertOk()->assertSee('전체화면');
        $this->get(route('admin.login'))->assertOk()->assertSee('Admin Login');

        $admin = $this->withSession(['is_admin' => true]);
        $admin->get(route('admin.visuals.index'))->assertOk();
        $admin->get(route('admin.visuals.create'))->assertOk()->assertSee('.html 파일 업로드');
        $admin->get(route('admin.visuals.edit', $visual))->assertOk()->assertSee('미리보기');
        $admin->get(route('admin.categories.index'))->assertOk()->assertSee('Cat');
    }
}
