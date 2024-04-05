<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('queues', App\Http\Controllers\QueueController::class);

Route::apiResource('queue-logs', App\Http\Controllers\QueueLogController::class);

Route::post("/notification", function (Request $request) {
    $request->validate([
        "bot_id" => "required",
        "message" => "required"
    ]);

    \App\Models\Queue::query()->create([
        'bot_id' => $request->bot_id,
        'content' => $request->message ?? null,
        'reply_keyboard' => is_null($request->reply_keyboard ?? null) ? null : json_decode($request->reply_keyboard),
        'inline_keyboard' => is_null($request->inline_keyboard ?? null) ? null : json_decode($request->inline_keyboard),
        'images' => json_decode($request->images ?? '[]'),
        'videos' => $request->videos ?? null,
        'audios' => $request->audios ?? null,
        'cron_time' => $request->cron_time ?? null,
    ]);
});
