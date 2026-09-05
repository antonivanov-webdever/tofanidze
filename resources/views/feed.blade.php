<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $site->name() }} — Writing</title>
        <link>{{ route('blog.index') }}</link>
        <description>{{ $site->get('meta_description') }}</description>
        <language>en</language>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml"/>
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('blog.show', $post) }}</link>
                <guid isPermaLink="true">{{ route('blog.show', $post) }}</guid>
                <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
                <description>{{ $post->excerpt }}</description>
                @foreach ($post->tags ?? [] as $tag)
                    <category>{{ $tag }}</category>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>
