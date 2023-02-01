<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidToken;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name("login");
Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [App\Http\Controllers\AuthController::class, 'resetPasswordByToken']);

Route::middleware([ValidToken::class])->group(function () {

    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/change-password', [App\Http\Controllers\AuthController::class, 'changePassword']);
    Route::post('/me', [App\Http\Controllers\AuthController::class, 'me']);
    Route::post('/send-mail', [App\Http\Controllers\EmailController::class, 'sendEmailTo']);
    Route::post('/my-avatar', [App\Http\Controllers\HomeController::class, 'myAvatar']);
    Route::post('/my-florals', [App\Http\Controllers\HomeController::class, 'myFlorals']);
    Route::post('/my-campaigns', [App\Http\Controllers\HomeController::class, 'myCampaigns']);
    Route::post('/my-nfts', [App\Http\Controllers\HomeController::class, 'myNFTs']);

    Route::prefix('/users')->group(function () {
        Route::get('/', [App\Http\Controllers\UserController::class, 'index']);
        Route::post('/', [App\Http\Controllers\UserController::class, 'store']);
        Route::post('/{id}', [App\Http\Controllers\UserController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\UserController::class, 'destroy']);
        Route::post('/{id}/avatar', [App\Http\Controllers\UserController::class, 'avatar']);
        Route::post('/{id}/attachments', [App\Http\Controllers\UserController::class, 'attachments']);
    });

    Route::prefix('/groups')->group(function () {
        Route::get('/', [App\Http\Controllers\GroupController::class, 'index']);
        Route::post('/', [App\Http\Controllers\GroupController::class, 'store']);
        Route::post('/{id}', [App\Http\Controllers\GroupController::class, 'update']);
        Route::put('/{id}/permissions', [App\Http\Controllers\GroupController::class, 'syncRules']);
        Route::delete('/{id}', [App\Http\Controllers\GroupController::class, 'destroy']);
    });

    Route::prefix('/categories')->group(function () {
        Route::get('/', [App\Http\Controllers\CategorieController::class, 'index']);
        Route::post('/', [App\Http\Controllers\CategorieController::class, 'store']);
        Route::post('/{id}', [App\Http\Controllers\CategorieController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\CategorieController::class, 'destroy']);
    });

    Route::prefix('/permissions')->group(function () {
        Route::get('/', [App\Http\Controllers\PermissionController::class, 'index']);
        Route::post('/', [App\Http\Controllers\PermissionController::class, 'store']);
        Route::post('/{id}', [App\Http\Controllers\PermissionController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\PermissionController::class, 'destroy']);
    });

    Route::prefix('/terms')->group(function () {
        Route::get('/', [App\Http\Controllers\TermController::class, 'index']);
        Route::post('/', [App\Http\Controllers\TermController::class, 'store']);
        Route::post('/{id}', [App\Http\Controllers\TermController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\TermController::class, 'destroy']);
    });

    Route::prefix('/nft')->group(function () {
        Route::get('/audit', [App\Http\Controllers\NFTController::class, 'audit']);
        Route::get('/', [App\Http\Controllers\NFTController::class, 'index']);
        Route::post('/', [App\Http\Controllers\NFTController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\NFTController::class, 'update']);
        Route::put('/{id}/transfer', [App\Http\Controllers\NFTController::class, 'transferNft']);
        Route::put('/{id}/status', [App\Http\Controllers\NFTController::class, 'status']);
        Route::delete('/{id}', [App\Http\Controllers\NFTController::class, 'destroy']);
    });

    Route::prefix('/nft-categorie')->group(function () {
        Route::get('/', [App\Http\Controllers\NFTController::class, 'getCategorie']);
        Route::post('/', [App\Http\Controllers\NFTController::class, 'addCategorie']);
        Route::post('/{id}', [App\Http\Controllers\NFTController::class, 'updateCategorie']);
        Route::delete('/{id}', [App\Http\Controllers\NFTController::class, 'removeCategorie']);
    });

    Route::prefix('/nft-classification')->group(function () {
        Route::get('/', [App\Http\Controllers\NFTController::class, 'getClassifications']);
        Route::post('/', [App\Http\Controllers\NFTController::class, 'addClassification']);
        Route::post('/{id}', [App\Http\Controllers\NFTController::class, 'updateClassification']);
        Route::delete('/{id}', [App\Http\Controllers\NFTController::class, 'removeClassification']);
    });

    Route::prefix('/campaigns')->group(function () {
        Route::get('/', [App\Http\Controllers\CampaignController::class, 'index']);
        Route::post('/', [App\Http\Controllers\CampaignController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\CampaignController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\CampaignController::class, 'destroy']);
    });

    Route::prefix('/partners')->group(function () {
        Route::get('/', [App\Http\Controllers\PartnerController::class, 'index']);
        Route::post('/', [App\Http\Controllers\PartnerController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\PartnerController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\PartnerController::class, 'destroy']);
    });

    Route::prefix('/tickets')->group(function () {
        Route::get('/', [App\Http\Controllers\TicketController::class, 'index']);
        Route::post('/', [App\Http\Controllers\TicketController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\TicketController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\TicketController::class, 'destroy']);
    });

    Route::prefix('/posts')->group(function () {
        Route::get('/', [App\Http\Controllers\PostController::class, 'index']);
        Route::post('/', [App\Http\Controllers\PostController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\PostController::class, 'update']);
        Route::put('/{id}/comment', [App\Http\Controllers\PostController::class, 'comment']);
        Route::delete('/{id}', [App\Http\Controllers\PostController::class, 'destroy']);
    });

    Route::prefix('/chat')->group(function () {
        Route::get('/', [App\Http\Controllers\ChatController::class, 'index']);
        Route::post('/', [App\Http\Controllers\ChatController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\ChatController::class, 'getChat']);
        Route::get('/{id}/messages', [App\Http\Controllers\ChatController::class, 'getMessages']);
        Route::put('/{id}', [App\Http\Controllers\ChatController::class, 'sendMessage']);
        Route::put('/{id}/read', [App\Http\Controllers\ChatController::class, 'readMessage']);
        Route::put('/{id}/meet', [App\Http\Controllers\ChatController::class, 'addParticipants']);
        Route::put('/{id}/finish', [App\Http\Controllers\ChatController::class, 'finish']);
    });

    Route::prefix('/floral')->group(function () {
        Route::get('/', [App\Http\Controllers\FloralController::class, 'index']);
        Route::post('/', [App\Http\Controllers\FloralController::class, 'store']);
        Route::get('/audit', [App\Http\Controllers\FloralController::class, 'audit']);
        Route::put('/{id}/status', [App\Http\Controllers\FloralController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\FloralController::class, 'destroy']);
    });
});
