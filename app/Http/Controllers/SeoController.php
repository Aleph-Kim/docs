<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Visual;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * 검색엔진 크롤러를 위한 XML 사이트맵 제공
     */
    public function sitemap(): Response
    {
        $visuals = Visual::with('category')->latest('updated_at')->get();
        $categories = Category::orderBy('name')->get();

        $content = view('seo.sitemap', [
            'visuals' => $visuals,
            'categories' => $categories,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * RSS 2.0 피드 제공
     */
    public function rss(): Response
    {
        $visuals = Visual::with('category')->latest('created_at')->take(50)->get();

        $content = view('seo.rss', [
            'visuals' => $visuals,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }

    /**
     * AI 에이전트 및 LLM 검색엔진을 위한 llms.txt 표준 요약 제공
     */
    public function llms(): Response
    {
        $visuals = Visual::with('category')->latest()->take(30)->get();
        $categories = Category::withCount('visuals')->orderBy('name')->get();

        $lines = [];
        $lines[] = '# Docs';
        $lines[] = '> 기술 개념 설명, 인터랙티브 다이어그램, 아키텍처 가이드 등의 문서를 저장하고 열람하는 아카이브';
        $lines[] = '';
        $lines[] = '## About';
        $lines[] = 'Docs는 기술 개념 설명(eli5), 인터랙티브 다이어그램, 아키텍처 가이드 등의 시각화 문서를 제공합니다.';
        $lines[] = '모든 문서는 웹 인터페이스에서 즉시 열람하거나 새 탭에서 전체 화면으로 실행할 수 있습니다.';
        $lines[] = '';
        $lines[] = '## Categories';
        foreach ($categories as $cat) {
            $catUrl = route('visuals.index', ['category' => $cat->slug]);
            $lines[] = "- [{$cat->name}]({$catUrl}): {$cat->visuals_count}개의 시각화 문서";
        }
        $lines[] = '';
        $lines[] = '## Recent & Featured Documents';
        foreach ($visuals as $visual) {
            $url = route('visuals.show', $visual->slug);
            $desc = $visual->description ? ": {$visual->description}" : '';
            $lines[] = "- [{$visual->title}]({$url}) ({$visual->category->name}){$desc}";
        }
        $lines[] = '';
        $lines[] = '## Full Index';
        $lines[] = '- [전체 문서 목록(llms-full.txt)](' . route('llms-full.txt') . '): 아카이브의 모든 문서 요약 목록';

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * LLM을 위한 전체 문서 인덱스(llms-full.txt)
     */
    public function llmsFull(): Response
    {
        $visuals = Visual::with('category')->latest()->get();

        $lines = [];
        $lines[] = '# Docs — Full Archive Catalog';
        $lines[] = '';
        $lines[] = '> 아카이브 내에 등록된 전체 인터랙티브 시각화 문서 목록';
        $lines[] = '';

        foreach ($visuals as $visual) {
            $url = route('visuals.show', $visual->slug);
            $renderUrl = route('visuals.render', $visual->slug);
            $date = $visual->created_at->format('Y-m-d');
            $lines[] = "### [{$visual->title}]({$url})";
            $lines[] = "- **카테고리**: {$visual->category->name}";
            $lines[] = "- **작성일**: {$date}";
            if ($visual->description) {
                $lines[] = "- **설명**: {$visual->description}";
            }
            $lines[] = "- **문서 URL**: {$url}";
            $lines[] = "- **직접 렌더링 URL**: {$renderUrl}";
            $lines[] = '';
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}

