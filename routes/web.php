<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AssociateRequestController;
use App\Http\Controllers\AssociateRequestResponseController;
use App\Http\Controllers\MembershipRequestResponseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/talkboard', function () {
    return Inertia::render('Talkboard');
})->name('home');

Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {

    Route::get('/users/{user}/profile', [UserController:: class, 'show'])
        ->name('myprofile')->middleware(['auth']);

    Route::patch('/users/{user}/update', [UserController:: class, 'update'])
        ->name('updateProfile')->middleware(['auth']);
    
    Route::patch('/users/{user}/updatePassword', [UserController:: class, 'updatePassword'])
        ->name('updatePassword')->middleware(['auth']);

    Route::post('/images/store', [ImagesController::class, 'store'])
        ->name('storeImage')->middleware(['auth']);

    Route::get('myarticles', [ArticleController::class, 'index'])
        ->name('myarticles')->middleware(['auth']);
    
    Route::post('/approvals/store', [ApprovalController::class, 'store'])
        ->name('storeApproval')->middleware(['auth']);
    
    Route::post('/comments/store', [CommentController::class, 'store'])
        ->name('storeComment')->middleware(['auth']);

    Route::get('/users/{user:username}/posts', [PostController::class, 'show'])
        ->name('user-posts')->middleware(['auth']);

    Route::get('talkboard', [PostController::class, 'index'])
        ->name('talkboard')->middleware(['auth']);
    
    Route::post('/posts/store', [PostController::class, 'store'])
        ->name('storePost')->middleware(['auth']);

    Route::post('/associate-request', [AssociateRequestController::class, 'store'])
        ->name('assocReq')->middleware(['auth']);
    
    Route::post('/associate-request-response', [AssociateRequestResponseController::class, 'update'])
        ->name('assocReqRes')->middleware(['auth']);

    Route::get('/mygroups', [GroupController::class, 'index'])
        ->name('mygroups')->middleware(['auth']);
    
    Route::post('/mygroups/store', [GroupController::class, 'store'])
        ->name('storeGroup')->middleware(['auth']);
        
    Route::delete('/mygroups/{group}/destroy', [GroupController::class, 'destroy'])
        ->name('deleteGroup')->middleware(['auth']);

    Route::post('/myteams/store', [TeamController::class, 'store'])
        ->name('storeTeam')->middleware(['auth']);
    
    Route::post('/memberships/accept-reject', [MembershipRequestResponseController::class, 'store'])
        ->name('acceptRejectMembership')->middleware(['auth']);

    Route::get('/myprojects', [ProjectController::class, 'index'])
        ->name('myprojects')->middleware(['auth']);

    Route::post('/myprojects/store', [ProjectController::class, 'store'])
        ->name('storeProject')->middleware(['auth']);
});
