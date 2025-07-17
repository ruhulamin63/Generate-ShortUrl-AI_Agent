<?php

use App\Http\Controllers\Api\v1\Agent\AgentChatController;
use App\Http\Controllers\Api\v1\Agent\Conversation\DeleteConversationController;
use App\Http\Controllers\Api\v1\Agent\Conversation\ListConvertsarionController;
use App\Http\Controllers\Api\v1\Agent\ConversationController;
use App\Http\Controllers\Api\v1\Agent\SendMessageController;
use App\Http\Controllers\Api\v1\Agent\SendMessageControllerc;
use App\Http\Controllers\Api\v1\AuthController\AuthController;
use App\Http\Controllers\Api\v1\Group\CreateUrlGroupController;
use App\Http\Controllers\Api\v1\Group\DeleteUrlGroupController;
use App\Http\Controllers\Api\v1\Group\UpdateUrlGroupController;
use App\Http\Controllers\Api\v1\Url\DeleteShortUrlcontroller;
use App\Http\Controllers\Api\v1\Url\ListAllUrlController;
use App\Http\Controllers\Api\v1\Url\RedirectUrlController;
use App\Http\Controllers\Api\v1\Url\ShortenUrlController;
use App\Http\Controllers\Api\v1\Url\UpdateShortUrlController;
use App\Http\Controllers\Api\v1\UrlGroup\AssignGroupFromUrlController;
use App\Http\Controllers\Api\v1\UrlGroup\ListGroupsWithUrlsController;
use App\Http\Controllers\Api\v1\UrlGroup\UnassignGroupFromUrlController;
use App\Http\Controllers\Api\v1\User\RegisterUserController;
use App\Http\Controllers\Api\v1\User\ShowUserController;
use App\Http\Controllers\Api\v1\User\UpdateUserController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterUserController::class)->name('register');
Route::post('/login', AuthController::class)->middleware('check.blocked.ip','throttle:login')->name('login');


Route::middleware(['check.blocked.ip','throttle:global','jwt.auth','check.redis.token', 'role:user'])->group(function () {
//User Routes
    Route::get('/user/show', ShowUserController::class)->name('user.show');
    Route::post('/user/update', UpdateUserController::class)->name('user.update');
//Url Routes
    Route::post('/url/shorten', ShortenUrlController::class)->name('url.shorten');
    Route::delete('url/delete/{shortUrl}', DeleteShortUrlcontroller::class)->name('url.delete');
    Route::get('/url/list', ListAllUrlController::class)->name('url.list');
    Route::put('/url/update/{url}', UpdateShortUrlController::class)->name('url.update');
//Group Routes
    Route::post('/group', CreateUrlGroupController::class)->name('group.create');
    Route::put('/group/{groupId}', UpdateUrlGroupController::class)->name('group.update');
    Route::delete('/group/{groupId}', DeleteUrlGroupController::class)->name('group.delete');

    //Group-Url Routes
    Route::post('/group/assign', AssignGroupFromUrlController::class)->name('group.assign');
    Route::post('/group/unassign', UnassignGroupFromUrlController::class)->name('group.unassign');
    Route::get('/group/list', ListGroupsWithUrlsController::class)->name('group.list');

    //Chat Routes
    Route::post('/chat/send', SendMessageController::class)->name('chat.send');
    Route::post('/conversation', ConversationController::class)->name('conversation.create');
    Route::delete('/conversation', DeleteConversationController::class)->name('conversation.delete');
    Route::get('/conversation/list', ListConvertsarionController::class)->name('conversation.list');
});

Route::get('/{shortUrl}', RedirectUrlController::class)->name('url.redirect');

 