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
