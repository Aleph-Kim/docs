<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Visual;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ViewSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_pages_render(): void
    {
        config(['admin.id' => 'admin', 'admin.password_hash' => Hash::make('secret')]);
        $cat = Category::create(['name' => 'Cat', 'slug' => 'cat']);
        $visual = Visual::create([
            'title' => 'Doc', 'slug' => 'doc', 'category_id' => $cat->id,
            'description' => 'desc', 'html' => '<!DOCTYPE html><html><body>x</body></html>',
        ]);

        $this->get(route('visuals.index'))->assertOk();
        $this->get(route('visuals.show', $visual->slug))->assertOk()->assertSee('전체화면');
        $this->get(route('visuals.raw', $visual->slug))->assertOk();
        $this->get(route('admin.login'))->assertOk()->assertSee('Admin Login');

        $admin = $this->withSession(['is_admin' => true]);
        $admin->get(route('admin.visuals.index'))->assertOk();
        $admin->get(route('admin.visuals.create'))->assertOk()->assertSee('HTML 원문');
        $admin->get(route('admin.visuals.edit', $visual))->assertOk()->assertSee('미리보기');
        $admin->get(route('admin.categories.index'))->assertOk()->assertSee('Cat');
    }
}
