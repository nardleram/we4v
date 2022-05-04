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

<p>Hi {{ $requestee->name }}!</p>

<p>{{ $requester->username}} has requested to associate with you.</p>

<p>Simply visit {{ $requester->username }}’s <a href="{{ route('user-show', $requester->slug) }}">Self page</a> or click the orange bell to the right of the “Association requests” panel at the upper right of the we4v site to respond.</p>

<p>(If you are already logged on, you may need to refresh the page to turn the grey bell orange!)</p>

<br>

<img style="height: 1.5rem" src="{{ $message->embed(base_path('public/storage/images/we4v_logo.svg')) }}" alt="">