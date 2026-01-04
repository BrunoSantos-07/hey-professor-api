<?php

use App\Http\Controllers\Question;

Route::middleware('auth:sanctum')->group(function () {
    // region questions
    Route::post('/questions', Question\StoreController::class)->name('questions.store');
    // endregion questions
});
