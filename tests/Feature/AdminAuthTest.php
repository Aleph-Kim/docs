<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'admin.id' => 'admin',
            'admin.password' => 'secret',
        ]);
    }

    public function test_guest_is_redirected_from_admin_area(): void
    {
        $this->get('/admin/visuals')->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_log_in_with_valid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'id' => 'admin',
            'password' => 'secret',
        ]);

        $response->assertRedirect(route('admin.visuals.index'));
        $this->assertTrue(session('is_admin'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->from(route('admin.login'))->post('/admin/login', [
            'id' => 'admin',
            'password' => 'wrong',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('id');
        $this->assertNull(session('is_admin'));
    }

    public function test_logout_clears_admin_session(): void
    {
        $this->withSession(['is_admin' => true])
            ->post('/admin/logout')
            ->assertRedirect(route('visuals.index'));

        $this->assertNull(session('is_admin'));
    }
}
