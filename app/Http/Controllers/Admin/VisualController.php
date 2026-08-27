<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVisualRequest;
use App\Http\Requests\Admin\UpdateVisualRequest;
use App\Models\Category;
use App\Models\Visual;
use App\Services\FileService;
use Illuminate\Http\RedirectResponse;
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

    public function store(StoreVisualRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('html_file');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title']);

        DB::transaction(function () use ($request, $data) {
            $visual = Visual::create($data);
            FileService::saveOrUpdate($visual, $request, 'html_file');
        });

        return redirect()->route('admin.visuals.index')->with('status', '문서를 등록했습니다.');
    }

    public function edit(Visual $visual): View
    {
        return view('admin.visuals.edit', [
            'visual' => $visual->load('file'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateVisualRequest $request, Visual $visual): RedirectResponse
    {
        $data = $request->safe()->except('html_file');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $visual->id);

        DB::transaction(function () use ($request, $visual, $data) {
            $visual->update($data);
            FileService::saveOrUpdate($visual, $request, 'html_file');
        });

        return redirect()->route('admin.visuals.index')->with('status', '문서를 수정했습니다.');
    }

    public function destroy(Visual $visual): RedirectResponse
    {
        DB::transaction(function () use ($visual) {
            FileService::deleteByModel($visual);
            $visual->delete();
        });

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
            ->exists()) {
            $candidate = $base . '-' . $suffix++;
        }

        return $candidate;
    }
}
