{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- 메인 페이지 --}}
    <url>
        <loc>{{ route('visuals.index') }}</loc>
        @if ($visuals->isNotEmpty())
            <lastmod>{{ $visuals->first()->updated_at->toAtomString() }}</lastmod>
        @endif
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- 카테고리별 목록 페이지 --}}
    @foreach ($categories as $category)
        <url>
            <loc>{{ route('visuals.index', ['category' => $category->slug]) }}</loc>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    {{-- 개별 시각화 문서 상세 페이지 --}}
    @foreach ($visuals as $visual)
        <url>
            <loc>{{ route('visuals.show', $visual->slug) }}</loc>
            <lastmod>{{ $visual->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>

