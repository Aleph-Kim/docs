<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Visual;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VisualController extends Controller
{
    public function index(Request $request): View
    {
        $visuals = Visual::with('category')
            ->inCategory($request->query('category'))
            ->search($request->query('q'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('visuals.index', [
            'visuals' => $visuals,
            'categories' => Category::orderBy('name')->get(),
            'activeCategory' => $request->query('category'),
            'keyword' => (string)$request->query('q'),
        ]);
    }

    public function show(Visual $visual): View
    {
        $visual->load('category', 'file');

        return view('visuals.show', compact('visual'));
    }

    public function render(Visual $visual): Response
    {
        $visual->loadMissing('file');

        if (! $visual->file || ! ($path = $visual->file->getRawOriginal('url'))) {
            abort(404);
        }

        $cacheKey = "visual:content:{$visual->id}";

        $content = Cache::remember($cacheKey, now()->addDay(), function () use ($path) {
            if (! Storage::exists($path)) {
                abort(404);
            }

            return Storage::get($path);
        });

        // macOS 오버레이 스크롤바는 스크롤 전엔 숨겨져 있어 스크롤 가능 여부를 알 수 없으므로 항상 표시
        $scrollbarStyle = '<style>'
            .'::-webkit-scrollbar{width:10px;height:10px}'
            .'::-webkit-scrollbar-track{background:#f4f4f5}'
            .'::-webkit-scrollbar-thumb{background:#a1a1aa;border-radius:5px}'
            .'</style>';

        $content = preg_replace('~</head>~i', $scrollbarStyle.'</head>', $content, 1, $count);

        if ($count === 0) {
            $content .= $scrollbarStyle;
        }

        $etag = '"'.md5($content).'"';

        return response($content, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Security-Policy' => 'sandbox allow-scripts allow-popups',
            'X-Content-Type-Options' => 'nosniff',
            'X-Robots-Tag' => 'noindex, follow',
            'ETag' => $etag,
        ]);
    }
}
