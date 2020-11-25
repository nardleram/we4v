<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {

    Route::get('auth-user', 'AuthUserController@show');

    Route::apiResources([
        '/posts' => 'PostController',
        '/posts/{post}/like' => 'PostLikeController',
        '/posts/{post}/comment' => 'PostCommentController',
        '/users' => 'UserController',
        '/user-images' => 'UserImageController',
        '/users/{user}/posts' => 'UserPostController',
        '/associate-request' => 'AssociateRequestController',
        '/associate-request-response' => 'AssociateRequestResponseController',
    ]);
});

