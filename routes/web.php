<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CastVoteController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\ArticleImageController;
use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\BackgroundImageController;
use App\Http\Controllers\AssociateRequestController;
use App\Http\Controllers\AssociateRequestResponseController;
use App\Http\Controllers\MembershipRequestResponseController;
use App\Http\Controllers\PidgenMailController;

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

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    Route::get('/about', [HomeController::class, 'index'])
        ->name('about');

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

    Route::post('/images/profile/store', [ProfileImageController::class, 'store'])
        ->name('storeProfileImage');

    Route::post('/images/background/store', [BackgroundImageController::class, 'store'])
        ->name('storeBackgroundImage');

    Route::post('/images/article/store', [ArticleImageController::class, 'store'])
        ->name('storeArticleImage');

    Route::get('myarticles', [ArticleController::class, 'index'])
        ->name('myarticles');

    Route::post('/articles/store', [ArticleController::class, 'store'])
        ->name('storeArticle');

    Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])
        ->name('article-show');

    Route::post('/articles/search', [ArticleController:: class, 'search'])
        ->name('searchArticles');
    
    Route::post('/approvals/store', [ApprovalController::class, 'store'])
        ->name('storeApproval');

    Route::get('talkboard', [PostController::class, 'index'])
        ->name('talkboard');
    
    Route::post('/posts/store', [PostController::class, 'store'])
        ->name('storePost');
    
    Route::post('/post-comments/store', [PostCommentController::class, 'store'])
    ->name('storePostComment');

    Route::post('/article-comments/store', [ArticleCommentController::class, 'store'])
        ->name('storeArticleComment');

    Route::post('/associate-request', [AssociateRequestController::class, 'store'])
        ->name('assocReq');
    
    Route::post('/associate-request-response', [AssociateRequestResponseController::class, 'update'])
        ->name('assocReqRes');

    Route::get('/mygroups', [GroupController::class, 'index'])
        ->name('mygroups');

    Route::get('/mygroups/show/{id}', [GroupController::class, 'show'])
        ->name('showGroup');
    
    Route::post('/mygroups/store', [GroupController::class, 'store'])
        ->name('storeGroup');
    
    Route::patch('/mygroups/update', [GroupController::class, 'update'])
        ->name('updateGroup');

    Route::patch('/mygroups/transfer', [GroupController::class, 'transferOwnership'])
        ->name('transferGroup');
    
    Route::post('/groups/search', [GroupController:: class, 'search'])
        ->name('searchGroups');

    Route::post('/myteams/store', [TeamController::class, 'store'])
        ->name('storeTeam');
        
    Route::patch('/myteams/update', [TeamController::class, 'update'])
        ->name('updateTeam');

    Route::post('/mynetworks/store', [NetworkController::class, 'store'])
        ->name('storeNetwork');

    Route::patch('/mynetworks/update', [NetworkController::class, 'update'])
        ->name('updateNetwork');

    Route::patch('/mynetworks/transfer', [NetworkController::class, 'transferOwnership'])
        ->name('transferNetwork');

    Route::post('/memberships/store', [MembershipController::class, 'store'])
        ->name('storeMembership');

    Route::delete('/memberships/{membership}/destroy', [MembershipController::class, 'destroy'])
        ->name('deleteMembership');
    
    Route::patch('/memberships/accept-reject', [MembershipRequestResponseController::class, 'update'])
        ->name('acceptRejectMembership');

    Route::get('/myprojects', [ProjectController::class, 'index'])
        ->name('myprojects');

    Route::post('/myprojects/store', [ProjectController::class, 'store'])
        ->name('storeProject');
        
    Route::patch('/myprojects/update', [ProjectController::class, 'update'])
        ->name('updateProject');

    Route::delete('/myprojects/{project}/destroy', [ProjectController::class, 'destroy'])
        ->name('deleteProject');

    Route::post('/mytasks/store', [TaskController::class, 'store'])
        ->name('storeTask');
    
    Route::patch('/mytasks/update', [TaskController::class, 'update'])
        ->name('updateTask');

    Route::delete('/mytasks/{task}/destroy', [TaskController::class, 'destroy'])
        ->name('deleteTask');

    Route::get('/myvotes', [VoteController::class, 'index'])
        ->name('myvotes');

    Route::post('/myvotes/store', [VoteController::class, 'store'])
        ->name('storeVote');

    Route::patch('/myvotes/update', [VoteController::class, 'update'])
        ->name('updateVote');

    Route::post('/cast-vote/store', [CastVoteController::class, 'store'])
        ->name('castVote');

    Route::get('/pidgenmail', [PidgenMailController::class, 'index'])
    ->name('pidgenmail');
});
