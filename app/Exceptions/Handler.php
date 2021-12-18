<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register() : void
    {
        $this->reportable(function (Throwable $e) {
            // Log something
        });

        $this->renderable(function (NotFoundHttpException $e, $request) { 
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Object not found', 422]);
            }

            if ($request->path() === 'users/{user:username}/self') {
                return response()->view('users.notFound', [], 422);
            }

            if ($request->path() === 'mygroups/{group}') {
                return response()->view('groups.notFound', [], 422);
            }

            if ($request->path() === 'mygroups/store') {
                return response()->view('groups.notFound', [], 422);
            }

            if ($request->path() === 'myteams/store') {
                return response()->view('teams.notFound', [], 422);
            }

            if ($request->path() === 'articles/{article}') {
                return response()->view('articles.notFound', [], 422);
            }
        });
    }
}
