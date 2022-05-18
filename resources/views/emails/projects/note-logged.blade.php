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

<p>Hi {{ $membership->member->name }}!</p>

<p>{{ $user->username }} logged a note in “<em>{{ $note->noteable->name }}</em>”, a project assigned to “<em>{{ $note->noteable->projectable->name }}</em>”, a {{ $type }} you are in.</p>

<p>Project description: <span style="color: #939090;">{{ $note->noteable->description }}</span></p>
<p>Project deadline: <span style="color: #939090;">{{ Carbon\Carbon::parse($note->noteable->end_date)->format('d M y') }}</span></p>

<p>{{ $user->username }} recorded the following log entry:</p>

<span style="color: #939090;">{!! $note->body !!}</span>

<br>
<br>

<img style="height: 1.5rem" src="{{ $message->embed(base_path('public/storage/images/we4v_logo.svg')) }}" alt="">