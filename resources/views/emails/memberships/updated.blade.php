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

<p>{{ $user->username }} changed the details of your membership of “{{ $membership->membershipable->name }}”:</p>

@if ($roleChanged)
    <p>Your role has changed to {{ $membership->role }}</p>
@endif

@if ($adminChanged)
    @if ($membership->is_admin)
        <p>{{ $user->username }} requests that you become administrator of {{ $membership->membershipable->name }}</p>
    @else
        <p>{{ $user->username }} requests that you relinquish the administrator role for {{ $membership->membershipable->name }}</p>
    @endif
@endif

<p>If these changes come as a surprise to you, you might want to discuss them with {{ $user->username }}</p>

<br>

<img style="height: 1.5rem" src="{{ $message->embed(base_path('public/storage/images/we4v_logo.svg')) }}" alt="">