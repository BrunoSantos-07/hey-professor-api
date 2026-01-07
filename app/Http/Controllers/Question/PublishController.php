<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Symfony\Component\HttpFoundation\Response;

class PublishController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Question $question)
    {
        abort_unless($question->status === 'draft', Response::HTTP_NOT_FOUND);
        $this->authorize('publish', $question);
        $question->status = 'published';
        $question->save();

        return response()->noContent();
    }
}
