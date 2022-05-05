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

<p>Hi {{ $membership->user->name }}!</p>

<p>{{ $membership->membershipable->user->username }} has invited you to join the team, “{{ $membership->membershipable->name }}”.</p>

<p>To respond, go to <a href="{{ route('talkboard') }}">we4v</a> and click the orange bell to the right of the “Group/team invitations” panel on the right of each we4v page.</p>

<p>(If you are already logged on, you may need to refresh the page to turn the grey bell orange!)</p>

<br>

<img style="height: 1.5rem" src="{{ $message->embed(base_path('public/storage/images/we4v_logo.svg')) }}" alt="">