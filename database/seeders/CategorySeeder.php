<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => '데이터 시각화', 'slug' => 'data-viz'],
            ['name' => '대시보드', 'slug' => 'dashboard'],
            ['name' => '인터랙티브', 'slug' => 'interactive'],
            ['name' => '다이어그램', 'slug' => 'diagram'],
            ['name' => '실험', 'slug' => 'experiment'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
