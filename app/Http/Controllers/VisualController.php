<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Visual;
use Illuminate\Http\Request;
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
}
