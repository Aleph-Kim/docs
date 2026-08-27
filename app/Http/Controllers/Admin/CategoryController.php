<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('visuals')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', '카테고리를 추가했습니다.');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name'], $category->id);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', '카테고리를 수정했습니다.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->visuals()->exists()) {
            return redirect()->route('admin.categories.index')
                ->withErrors(['category' => '이 카테고리에 연결된 시각화가 있어 삭제할 수 없습니다.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', '카테고리를 삭제했습니다.');
    }

    private function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name) ?: Str::slug(Str::random(8));
        $candidate = $base;
        $suffix = 2;

        while (Category::where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$suffix++;
        }

        return $candidate;
    }
}
