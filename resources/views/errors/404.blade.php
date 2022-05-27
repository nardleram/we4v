@extends('layouts.frame')

@section('content')

<div class="mt-20 mx-auto">
    <h1 class="text-red-600 text-6xl text-center font-bold">Oops! Error 404</h1>
    <h2 class="text-we4vGrey-500 text-xl text-center mt-6">Sorry, we can’t locate the page you’re looking for.</h2>
    <h3 class="font-bold text-2xl text-we4vGrey-500 text-center mt-10 mx-auto">Return to <a href="{{route('talkboard')}}" class="hover:text-we4vGrey-800">Talkboard</a></h3>
</div>
        
@endsection