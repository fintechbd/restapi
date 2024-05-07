<?php

use Fintech\Chat\Http\Controllers\ChatGroupController;
use Fintech\Chat\Http\Controllers\ChatMessageController;
use Fintech\Chat\Http\Controllers\ChatParticipantController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "API" middleware group. Enjoy building your API!
|
*/
if (Config::get('fintech.chat.enabled')) {
    Route::prefix('chat')->name('chat.')->group(function () {

        Route::apiResource('chat-groups', ChatGroupController::class);
        Route::post('chat-groups/{chat_group}/restore', [ChatGroupController::class, 'restore'])->name('chat-groups.restore');

        Route::apiResource('chat-participants', ChatParticipantController::class);
        Route::post('chat-participants/{chat_participant}/restore', [ChatParticipantController::class, 'restore'])->name('chat-participants.restore');

        Route::apiResource('chat-messages', ChatMessageController::class);
        Route::post('chat-messages/{chat_message}/restore', [ChatMessageController::class, 'restore'])->name('chat-messages.restore');

        //DO NOT REMOVE THIS LINE//
    });
}
