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

<p>{{ $user->username}} removed you from “{{ $membership->membershipable->name }}”.</p>

<p>If this was unexpected, you might want to discuss this matter with {{ $user->username}}.</p>

@if (!$membership->confirmed)
    <p>(However, seeing as you have not yet responded to the invitation, perhaps it was sent in error.)</p>
@endif

<br>

<img style="height: 1.5rem" src="{{ $message->embed(base_path('public/storage/images/we4v_logo.svg')) }}" alt="">