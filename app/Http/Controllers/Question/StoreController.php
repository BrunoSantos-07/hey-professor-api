<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Question;

class StoreController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreRequest $request)
    {
        Question::create([
            'user_id'  => auth()->id(),
            'question' => $request->question,
        ]);

        return response()->noContent();
    }
}
