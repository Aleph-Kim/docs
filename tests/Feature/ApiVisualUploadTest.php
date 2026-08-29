<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Visual;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiVisualUploadTest extends TestCase
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

    private function sampleEli5Html(string $topic = '헥사고날 아키텍처', string $def = '비즈니스 로직을 가운데 두고 포트와 어댑터로 분리한 아키텍처'): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>{$topic}</title>
</head>
<body>
<div class="wrap">
  <div class="hd"><h1>{$topic}</h1> <span class="mode">개념 모드</span></div>
  <div class="stage">
    <div class="def">
      <p class="one">{$def}</p>
    </div>
  </div>
</div>
</body>
</html>
HTML;
    }

    public function test_unauthenticated_json_request_is_rejected_with_401(): void
    {
        $response = $this->getJson(route('admin.categories.index'));

        $response->assertStatus(401)
            ->assertJson(['message' => '인증에 실패했습니다. 유효한 API 키 또는 비밀번호를 입력해주세요.']);
    }

    public function test_invalid_key_is_rejected_with_401(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer wrong-key')
            ->getJson(route('admin.categories.index'));

        $response->assertStatus(401);
    }

    public function test_auth_via_bearer_token_can_list_categories(): void
    {
        Category::create(['name' => '인터랙티브', 'slug' => 'interactive']);
        Category::create(['name' => '다이어그램', 'slug' => 'diagram']);

        $response = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->getJson(route('admin.categories.index'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_auth_via_bearer_token_can_create_category(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->postJson(route('admin.categories.store'), [
                'name' => '네트워크 시각화',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => '네트워크 시각화',
                ],
            ]);

        $this->assertDatabaseHas('categories', ['name' => '네트워크 시각화']);
    }

    public function test_auth_via_bearer_token_can_upload_visual(): void
    {
        $category = Category::create(['name' => '인터랙티브', 'slug' => 'interactive']);
        $html = $this->sampleEli5Html();

        $response = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->post(route('admin.visuals.store'), [
                'title' => '헥사고날 아키텍처',
                'description' => '비즈니스 로직을 가운데 두고 포트와 어댑터로 분리한 아키텍처',
                'category_id' => $category->id,
                'html_file' => UploadedFile::fake()->createWithContent('eli5.html', $html),
            ], ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => '헥사고날 아키텍처',
                    'description' => '비즈니스 로직을 가운데 두고 포트와 어댑터로 분리한 아키텍처',
                    'category' => [
                        'slug' => 'interactive',
                    ],
                ],
            ]);

        $visual = Visual::firstWhere('title', '헥사고날 아키텍처');
        $this->assertNotNull($visual);
        $this->assertSame($category->id, $visual->category_id);
        $this->assertNotNull($visual->file);
        Storage::assertExists($visual->file->getRawOriginal('url'));
    }

    public function test_auth_via_x_api_key_header_succeeds(): void
    {
        $category = Category::create(['name' => '인터랙티브', 'slug' => 'interactive']);
        $html = $this->sampleEli5Html('클린 아키텍처');

        $response = $this->withHeader('X-API-KEY', 'test-api-key')
            ->post(route('admin.visuals.store'), [
                'title' => '클린 아키텍처',
                'category_id' => $category->id,
                'html_file' => UploadedFile::fake()->createWithContent('clean.html', $html),
            ], ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', '클린 아키텍처');
    }

    public function test_auth_via_body_password_param_succeeds(): void
    {
        config([
            'admin.api_key' => null,
            'admin.password' => 'my-super-password',
        ]);
        $category = Category::create(['name' => '인터랙티브', 'slug' => 'interactive']);
        $html = $this->sampleEli5Html('도메인 주도 설계');

        $response = $this->post(route('admin.visuals.store'), [
            'password' => 'my-super-password',
            'title' => '도메인 주도 설계',
            'category_id' => $category->id,
            'html_file' => UploadedFile::fake()->createWithContent('ddd.html', $html),
        ], ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', '도메인 주도 설계');
    }

    public function test_auth_via_admin_session_succeeds(): void
    {
        $category = Category::create(['name' => '인터랙티브', 'slug' => 'interactive']);
        $html = $this->sampleEli5Html('세션 인증 테스트');

        $response = $this->withSession(['is_admin' => true])
            ->post(route('admin.visuals.store'), [
                'title' => '세션 인증 테스트',
                'category_id' => $category->id,
                'html_file' => UploadedFile::fake()->createWithContent('session.html', $html),
            ], ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', '세션 인증 테스트');
    }

    public function test_explicit_params_override_extracted_metadata(): void
    {
        $customCategory = Category::create(['name' => '다이어그램', 'slug' => 'diagram']);
        $html = $this->sampleEli5Html('HTML 제목', 'HTML 설명');

        $response = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->post(route('admin.visuals.store'), [
                'html_file' => UploadedFile::fake()->createWithContent('custom.html', $html),
                'title' => '재정의된 제목',
                'description' => '재정의된 설명',
                'category_id' => $customCategory->id,
                'slug' => 'custom-slug',
            ], ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => '재정의된 제목',
                    'slug' => 'custom-slug',
                    'description' => '재정의된 설명',
                    'category' => [
                        'id' => $customCategory->id,
                        'slug' => 'diagram',
                    ],
                ],
            ]);
    }

    public function test_duplicate_slug_gets_numeric_suffix(): void
    {
        $category = Category::create(['name' => '인터랙티브', 'slug' => 'interactive']);
        $html = '<html><head><title>Duplicate Title</title></head><body>1</body></html>';

        $this->withHeader('Authorization', 'Bearer test-api-key')
            ->post(route('admin.visuals.store'), [
                'title' => 'Duplicate Title',
                'category_id' => $category->id,
                'html_file' => UploadedFile::fake()->createWithContent('dup1.html', $html),
            ], ['Accept' => 'application/json'])->assertStatus(201);

        $res2 = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->post(route('admin.visuals.store'), [
                'title' => 'Duplicate Title',
                'category_id' => $category->id,
                'html_file' => UploadedFile::fake()->createWithContent('dup2.html', $html),
            ], ['Accept' => 'application/json'])->assertStatus(201);

        $this->assertSame('duplicate-title-2', $res2->json('data.slug'));
    }

    public function test_missing_html_file_returns_422(): void
    {
        $category = Category::create(['name' => '인터랙티브', 'slug' => 'interactive']);

        $response = $this->withHeader('Authorization', 'Bearer test-api-key')
            ->post(route('admin.visuals.store'), [
                'title' => 'No File',
                'category_id' => $category->id,
            ], ['Accept' => 'application/json']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['html_file']);
    }
}
