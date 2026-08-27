<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Visual;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            'keyword' => (string) $request->query('q'),
        ]);
    }

    public function show(Visual $visual): View
    {
        $visual->load('category');

        return view('visuals.show', compact('visual'));
    }

    /**
     * 저장된 완결형 HTML 문서를 원문 그대로 반환한다. 상세 페이지의 <iframe src> 로 사용된다.
     */
    public function raw(Visual $visual): Response
    {
        return response($visual->html)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }
}
