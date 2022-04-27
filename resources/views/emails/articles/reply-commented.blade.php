<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        color: rgb(110, 108, 108);
        font-size: .9rem;
        padding: 1rem;
    }
    a {
        color: rgb(81, 168, 186);
        text-decoration: none;
    }
    a:hover {
        color: rgb(51, 131, 142);
    }
</style>

<p>Hi {{ $replyToUser->name }}!</p>

<p>{{ $comment->user->username}} replied to a comment you made in a discussion under the article <em>{{ $comment->commentable->title }}</em>.</p>

<p>{{ $comment->user->username}} said:</p>

<span style="color: #939090;">{!! $comment->body !!}</span>

<p>Visit the <a href="{{ route('article-show', $comment->commentable->slug) }}">article’s page</a> if you would like to respond.</p>

<br>

<img style="height: 1.5rem" src="{{ $message->embed(base_path('public/storage/images/we4v_logo.svg')) }}" alt="">