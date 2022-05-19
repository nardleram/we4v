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

<p>Hi {{ $membership->updatedBy->name }}!</p>

<p>{{ $membership->member->username }} accepts your invitation to join the {{ $type }}, “{{ $membership->membershipable->name }}”.</p>

<p>Role: {{ $membership->role }}</p>

@if ($membership->is_admin)
    <p>{{ $membership->member->username }} is now an administrator of this {{ $type }}</p>
@endif

<br>

<img style="height: 1.5rem" src="{{ $message->embed(base_path('public/storage/images/we4v_logo.svg')) }}" alt="">