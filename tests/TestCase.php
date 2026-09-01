<?php

namespace Tests;

use App\Models\Visual;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 실제 스토리지 오염 방지 (기본 디스크 fake)
        Storage::fake();
    }

    // 테스트용 HTML 첨부 Visual 생성
    protected function makeVisual(array $attributes = []): Visual
    {
        $html = $attributes['html'] ?? null;
        unset($attributes['html']);

        return Visual::factory()
            ->withFile($html)
            ->create($attributes);
    }
}
