<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $categories = Category::withCount('visuals')->orderBy('name')->get();

        if ($request->expectsJson()) {
            return $this->success(CategoryResource::collection($categories));
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);

        $category = Category::create($data);
        $message = "'{$category->name}' 카테고리를 추가했습니다.";

        if ($request->expectsJson()) {
            return $this->created(new CategoryResource($category), $message);
        }

        return redirect()->route('admin.categories.index')->with('status', $message);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name'], $category->id);

        $category->update($data);
        $message = "'{$category->name}' 카테고리를 수정했습니다.";

        if ($request->expectsJson()) {
            return $this->success(new CategoryResource($category), $message);
        }

        return redirect()->route('admin.categories.index')->with('status', $message);
    }

    public function destroy(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        if ($category->visuals()->exists()) {
            if ($request->expectsJson()) {
                return $this->error('이 카테고리에 연결된 문서가 있어 삭제할 수 없습니다.', 422);
            }

            return redirect()->route('admin.categories.index')
                ->withErrors(['category' => '이 카테고리에 연결된 문서가 있어 삭제할 수 없습니다.']);
        }

        $name = $category->name;
        $category->delete();
        $message = "'{$name}' 카테고리를 삭제했습니다.";

        if ($request->expectsJson()) {
            return $this->success(null, $message);
        }

        return redirect()->route('admin.categories.index')->with('status', $message);
    }

    private function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name) ?: Str::slug(Str::random(8));
        $candidate = $base;
        $suffix = 2;

        while (Category::where('slug', $candidate)
            ->when($ignoreId, fn($q) => $q->whereKeyNot($ignoreId))
            ->exists()
        ) {
            $candidate = $base . '-' . $suffix++;
        }

        return $candidate;
    }
}
