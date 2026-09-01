<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\File;
use App\Models\Visual;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Visual>
 */
class VisualFactory extends Factory
{
    protected $model = Visual::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->randomNumber(4),
            'category_id' => Category::factory(),
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * 실물 HTML 첨부 파일 생성 상태
     */
    public function withFile(?string $content = null): static
    {
        return $this->afterCreating(function (Visual $visual) use ($content) {
            $html = $content ?? '<!DOCTYPE html><html><body>'.$visual->title.'</body></html>';
            $file = File::factory()->content($html)->make();
            $visual->file()->save($file);
        });
    }
}
