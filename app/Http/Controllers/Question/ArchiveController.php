<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Models\Question;

class ArchiveController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Question $question)
    {
        $this->authorize('archive', $question);

        $question->delete();

        return response()->noContent();
    }
}
