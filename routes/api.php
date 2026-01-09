<?php

use App\Http\Controllers\Auth\{LoginController, RegisterController};
use App\Http\Controllers\Question;
use Illuminate\Support\Facades\Route;

Route::post('login', LoginController::class)->name('login');
Route::post('register', RegisterController::class)->name('register');

Route::middleware('auth:sanctum')->group(function () {
    // region questions
    Route::get('my-questions/{status}', Question\MineController::class)->name('my-questions');
    Route::get('questions', Question\IndexController::class)->name('questions.index');
    Route::post('/questions', Question\StoreController::class)->name('questions.store');
    Route::put('/questions/{question}', Question\UpdateController::class)->name('questions.update');
    Route::delete('questions/{question}', Question\DeleteController::class)->name('questions.delete');
    Route::delete('questions/{question}/archive', Question\ArchiveController::class)->name('questions.archive');
    Route::put('questions/{question}/restore', Question\RestoreController::class)->name('questions.restore');
    Route::put('questions/{question}/publish', Question\PublishController::class)->name('questions.publish');
    // endregion questions
});
