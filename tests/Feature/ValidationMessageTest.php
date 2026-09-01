<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ValidationMessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'admin.password' => 'secret-admin-pass',
            'admin.api_key' => 'test-api-key',
        ]);
    }

    public function test_visual_store_validation_returns_korean_messages(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->post(route('admin.visuals.store'), [
                // 빈 요청
            ], ['Accept' => 'application/json']);

        $response->assertStatus(422);

        $errors = $response->json('errors');
        $this->assertArrayHasKey('title', $errors);
        $this->assertArrayHasKey('category_id', $errors);
        $this->assertArrayHasKey('html_file', $errors);

        $this->assertSame('제목을 입력해주세요.', $errors['title'][0]);
        $this->assertSame('카테고리를 선택해주세요.', $errors['category_id'][0]);
        $this->assertSame('HTML 파일을 업로드해주세요.', $errors['html_file'][0]);
    }

    public function test_visual_store_invalid_file_and_exists_validation_in_korean(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->post(route('admin.visuals.store'), [
                'title' => 'Test Title',
                'category_id' => 99999, // 존재하지 않는 카테고리
                'html_file' => UploadedFile::fake()->create('test.png', 100, 'image/png'),
            ], ['Accept' => 'application/json']);

        $response->assertStatus(422);

        $errors = $response->json('errors');
        $this->assertArrayHasKey('category_id', $errors);
        $this->assertArrayHasKey('html_file', $errors);

        $this->assertSame('선택한 카테고리가 유효하지 않습니다.', $errors['category_id'][0]);
        $this->assertSame('HTML 파일은 html 또는 txt 형식만 지원합니다.', $errors['html_file'][0]);
    }

    public function test_category_store_validation_returns_korean_messages(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->postJson(route('admin.categories.store'), [
                // 빈 요청
            ]);

        $response->assertStatus(422);

        $errors = $response->json('errors');
        $this->assertArrayHasKey('name', $errors);
        $this->assertSame('카테고리 이름을 입력해주세요.', $errors['name'][0]);
    }

    public function test_category_duplicate_slug_validation_in_korean(): void
    {
        Category::factory()->create(['name' => '카테고리1', 'slug' => 'cat-1']);

        $response = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->postJson(route('admin.categories.store'), [
                'name' => '카테고리2',
                'slug' => 'cat-1',
            ]);

        $response->assertStatus(422);

        $errors = $response->json('errors');
        $this->assertArrayHasKey('slug', $errors);
        $this->assertSame('이미 사용 중인 슬러그입니다.', $errors['slug'][0]);
    }

    public function test_admin_login_validation_in_korean(): void
    {
        $response = $this->post(route('admin.login'), [
            // 빈 요청
        ]);

        $response->assertSessionHasErrors([
            'id' => '아이디를 입력해주세요.',
            'password' => '비밀번호를 입력해주세요.',
        ]);
    }
}
