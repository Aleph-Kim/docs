{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Docs</title>
        <link>{{ route('visuals.index') }}</link>
        <description>인터랙티브 시각화 &amp; 기술 문서 아카이브</description>
        <language>ko</language>
        <lastBuildDate>{{ $visuals->isNotEmpty() ? $visuals->first()->updated_at->toRfc2822String() : now()->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ route('rss') }}" rel="self" type="application/rss+xml" />

        @foreach ($visuals as $visual)
            <item>
                <title>{{ $visual->title }}</title>
                <link>{{ route('visuals.show', $visual->slug) }}</link>
                <guid isPermaLink="true">{{ route('visuals.show', $visual->slug) }}</guid>
                <description><![CDATA[{{ $visual->description ?: $visual->title }}]]></description>
                @if ($visual->category)
                    <category>{{ $visual->category->name }}</category>
                @endif
                <pubDate>{{ $visual->created_at->toRfc2822String() }}</pubDate>
            </item>
        @endforeach
    </channel>
</rss>

