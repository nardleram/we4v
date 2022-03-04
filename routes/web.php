<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CastVoteController;
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

// Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    Route::get('/users/{user}/profile', [UserController:: class, 'showProfile'])
        ->name('myprofile');

    Route::get('/users/{user:slug}/self', [UserController::class, 'show'])
        ->name('user-show');

    Route::patch('/users/{user}/update', [UserController:: class, 'update'])
        ->name('updateProfile');
    
    Route::patch('/users/{user}/updatePassword', [UserController:: class, 'updatePassword'])
        ->name('updatePassword');
    
    Route::post('/users/search', [UserController:: class, 'search'])
        ->name('searchUsers');

    Route::post('/images/store', [ImagesController::class, 'store'])
        ->name('storeImage');

    Route::get('myarticles', [ArticleController::class, 'index'])
        ->name('myarticles');

    Route::post('/articles/store', [ArticleController::class, 'store'])
        ->name('storeArticle');

    Route::get('/articles/{article:slug}/show', [ArticleController::class, 'show'])
        ->name('article-show');
    
    Route::post('/approvals/store', [ApprovalController::class, 'store'])
        ->name('storeApproval');
    
    Route::post('/comments/store', [CommentController::class, 'store'])
        ->name('storeComment');

    Route::get('talkboard', [PostController::class, 'index'])
        ->name('talkboard');
    
    Route::post('/posts/store', [PostController::class, 'store'])
        ->name('storePost');

    Route::post('/associate-request', [AssociateRequestController::class, 'store'])
        ->name('assocReq');
    
    Route::post('/associate-request-response', [AssociateRequestResponseController::class, 'update'])
        ->name('assocReqRes');

    Route::get('/mygroups', [GroupController::class, 'index'])
        ->name('mygroups');
    
    Route::post('/mygroups/store', [GroupController::class, 'store'])
        ->name('storeGroup');
    
    Route::patch('/mygroups/update', [GroupController::class, 'update'])
        ->name('updateGroup');
        
    Route::delete('/mygroups/{group}/destroy', [GroupController::class, 'destroy'])
        ->name('deleteGroup');
    
    Route::post('/groups/search', [GroupController:: class, 'search'])
        ->name('searchGroups');

    Route::post('/myteams/store', [TeamController::class, 'store'])
        ->name('storeTeam');
        
    Route::patch('/myteams/update', [TeamController::class, 'update'])
        ->name('updateTeam');
    
    Route::patch('/memberships/accept-reject', [MembershipRequestResponseController::class, 'update'])
        ->name('acceptRejectMembership');

    Route::get('/myprojects', [ProjectController::class, 'index'])
        ->name('myprojects');

    Route::post('/myprojects/store', [ProjectController::class, 'store'])
        ->name('storeProject');
        
    Route::patch('/myprojects/update', [ProjectController::class, 'update'])
        ->name('updateProject');

    Route::post('/mytasks/store', [TaskController::class, 'store'])
        ->name('storeTask');
    
    Route::patch('/mytasks/update', [TaskController::class, 'update'])
        ->name('updateTask');

    Route::get('/myvotes', [VoteController::class, 'index'])
        ->name('myvotes');

    Route::post('/myvotes/store', [VoteController::class, 'store'])
        ->name('storeVote');

    Route::post('/cast-vote/store', [CastVoteController::class, 'store'])
        ->name('castVote');

    Route::get('myarticlez', [ArticleController::class, 'indexx'])
        ->name('myarticlez');
});
