<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Task;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\UserController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class)->except(['update']);
    Route::put('tasks/{task}', [TaskController::class, 'replace']);

    Route::apiResource('users', UserController::class);
    // Route::apiResource('creators', CreatorController::class);
    // Route::apiResource('creators.loans', CreatorLoansController::class);


    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
