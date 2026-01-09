<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $questions = Question::query()
            ->where('status', '=', 'published')
            ->get();

        return QuestionResource::collection($questions);
    }
}
