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

<p>Hi {{ $user->name }}!</p>

<p>{{ $comment->user->username }} just commented on a Talkboard post on which you also commented.</p>

<p>{{ $comment->user->username }} said:</p>

<em style="color: #848181;">{!! $comment->body !!}</em>

<p>Visit your <a href="{{ route('talkboard') }}">Talkboard</a> if you would like to respond.</p>

<br>

<img style="height: 1.5rem" src="{{ $message->embed(base_path('public/storage/images/we4v_logo.svg')) }}" alt="">