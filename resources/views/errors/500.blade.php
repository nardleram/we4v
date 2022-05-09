@extends('layouts.frame')

@section('content')

<div class="mt-20 mx-auto">
    <h1 class="text-red-600 text-6xl text-center font-bold">Oops! Error 500</h1>
    <h2 class="text-we4vGrey-500 text-xl text-center mt-6">So very sorry! Our servers just dropped the ball rather spectacularly. A simply page refresh might do the trick. If a refresh doesn't solve the problem, admin would be happy to receive details of this sad occurrence. Email us at admin@we4v.com.</h2>
    <h3 class="font-bold text-2xl text-we4vGrey-500 text-center mt-10 mx-auto">Return to <a href="{{route('talkboard')}}" class="hover:text-we4vGrey-700">Talkboard</a></h3>
</div>
        
@endsection