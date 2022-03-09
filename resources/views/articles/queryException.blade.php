@extends('layouts.frame')

@section('content')
<div class="mx-auto mt-20 text-center">
    <h1 class="text-red-600 text-6xl font-bold">Oops! Error 500</h1>
    <div class="mx-auto w-40">
        <img src="{{ asset('images/confusedRobot.jpeg') }}" alt="" width="150" class="text-center">
    </div>
    <h2 class="text-we4vGrey-500 text-3xl font-bold">Article not found</h2>
    <h3 class="text-we4vGrey-500 text-lg">Apologies. There was an internal server error resulting from a query to retrieve your articles from the database. Please try again later. If the problem persists, contact one of our admins.</h3>
    <h3 class="font-bold text-2xl text-we4vGrey-500 text-center mt-10">
        Return to <a href="{{route('myarticles')}}" class="hover:text-we4vGrey-700">Articles</a>
    </h3>
</div>
@endsection