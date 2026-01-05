<?php

use App\Http\Controllers\Question;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // region questions
    Route::post('/questions', Question\StoreController::class)->name('questions.store');
    Route::put('/questions/{question}', Question\UpdateController::class)->name('questions.update');
    // endregion questions
});
