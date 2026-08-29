<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVisualRequest;
use App\Http\Requests\Admin\UpdateVisualRequest;
use App\Http\Resources\VisualResource;
use App\Models\Category;
use App\Models\Visual;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VisualController extends Controller
{
    public function index(): View
    {
        $visuals = Visual::with('category')->latest()->paginate(20);

        return view('admin.visuals.index', compact('visuals'));
    }

    public function create(): View
    {
        return view('admin.visuals.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(StoreVisualRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->safe()->except('html_file');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title']);

        $visual = DB::transaction(function () use ($request, $data) {
            $visual = Visual::create($data);
            FileService::saveOrUpdate($visual, $request, 'html_file');
            return $visual;
        });

        if ($request->expectsJson()) {
            $visual->load('category', 'file');
            return $this->created(new VisualResource($visual), '문서를 등록했습니다.');
        }

        return redirect()->route('admin.visuals.index')->with('status', '문서를 등록했습니다.');
    }

    public function edit(Visual $visual): View
    {
        return view('admin.visuals.edit', [
            'visual' => $visual->load('file'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateVisualRequest $request, Visual $visual): RedirectResponse|JsonResponse
    {
        $data = $request->safe()->except('html_file');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $visual->id);

        DB::transaction(function () use ($request, $visual, $data) {
            $visual->update($data);
            FileService::saveOrUpdate($visual, $request, 'html_file');
            Cache::forget("visual:content:{$visual->id}");
        });

        if ($request->expectsJson()) {
            $visual->load('category', 'file');
            return $this->success(new VisualResource($visual), '문서를 수정했습니다.');
        }

        return redirect()->route('admin.visuals.index')->with('status', '문서를 수정했습니다.');
    }

    public function destroy(Request $request, Visual $visual): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($visual) {
            FileService::deleteByModel($visual);
            $visual->delete();
            Cache::forget("visual:content:{$visual->id}");
        });

        if ($request->expectsJson()) {
            return $this->success(null, '문서를 삭제했습니다.');
        }

        return redirect()->route('admin.visuals.index')->with('status', '문서를 삭제했습니다.');
    }

    /**
     * slug 미입력 시 제목에서 생성하고, 중복이면 -2, -3 … 을 붙여 유니크하게 만든다.
     */
    private function uniqueSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $title) ?: Str::slug(Str::random(8));
        $candidate = $base;
        $suffix = 2;

        while (Visual::where('slug', $candidate)
            ->when($ignoreId, fn($q) => $q->whereKeyNot($ignoreId))
            ->exists()
        ) {
            $candidate = $base . '-' . $suffix++;
        }

        return $candidate;
    }
}
