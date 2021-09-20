@extends('layouts.frame')

@section('content')
<div class="mx-auto mt-20 text-center">
    <h1 class="text-red-600 text-6xl font-bold">Oops! Error 422</h1>
    <div class="mx-auto w-40">
        <img src="{{ asset('images/confusedRobot.jpeg') }}" alt="" width="150" class="text-center">
    </div>
    <h2 class="text-we4vGrey-500 text-3xl font-bold">Object not found</h2>
    <h3 class="text-we4vGrey-500 text-lg">We couldn’t locate the group you requested.</h3>
    <h3 class="font-bold text-2xl text-we4vGrey-500 text-center mt-10">
        Return to <a href="{{route('talkboard')}}" class="hover:text-we4vGrey-700">Talkboard</a>
    </h3>
</div>
@endsection