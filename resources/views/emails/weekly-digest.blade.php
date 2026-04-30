<x-mail::message>
# Your Weekly Digest

Hi {{ $user->name }},

Here's what happened in your BrainVault this week:

## This Week's Stats
- **{{ $stats['new_bookmarks'] }}** new bookmarks saved
- **{{ $stats['new_notes'] }}** notes created
- **{{ $stats['bookmarks_read'] }}** articles read
- **{{ $stats['total_words_read'] }}** words consumed

@if(count($topBookmarks) > 0)
## Top Bookmarks This Week
@foreach($topBookmarks as $bookmark)
- [{{ $bookmark['title'] }}]({{ $bookmark['url'] }})
@endforeach
@endif

@if(count($aiInsights) > 0)
## AI Insights
@foreach($aiInsights as $insight)
- {{ $insight }}
@endforeach
@endif

Keep building your knowledge base!

<x-mail::button :url="config('app.url') . '/dashboard'">
Open Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
