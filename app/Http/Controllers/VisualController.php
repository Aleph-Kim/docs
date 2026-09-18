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

        // 샌드박스 iframe 에 포커스가 있으면 키 입력이 부모로 전달되지 않으므로 단축키를 부모에 대신 전달
        $shortcutScript = '<script>'
            .'document.addEventListener("keydown",function(e){'
            .'if(parent===window||e.repeat||e.metaKey||e.ctrlKey||e.altKey)return;'
            .'if(e.target.closest&&e.target.closest("input,textarea,select,[contenteditable]"))return;'
            .'if(e.key==="Escape"||e.code==="KeyF")parent.postMessage({type:"visual-shortcut",key:e.key,code:e.code},"*");'
            .'});'
            // 부모에 포커스가 있을 때 부모가 넘겨준 탐색 키를 문서 자체 키 핸들러(단계 재생 등)에 전달
            .'window.addEventListener("message",function(e){'
            .'if(e.source!==parent||parent===window||!e.data||e.data.type!=="visual-key")return;'
            .'document.dispatchEvent(new KeyboardEvent("keydown",{key:e.data.key,code:e.data.code,bubbles:true,cancelable:true}));'
            .'});'
            .'</script>';

        $injected = $scrollbarStyle.$shortcutScript;

        $content = preg_replace('~</head>~i', $injected.'</head>', $content, 1, $count);

        if ($count === 0) {
            $content .= $injected;
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
