<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Support\Facades\{Auth, Validator};

class MineController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $status = request()->status;
        Validator::validate(
            ['status' => $status],
            ['status' => ['required', 'in:draft,published,arquived']]
        );
        $questions = Question::query()
            ->whereUserId(Auth::id())
            ->where('status', '=', $status)
            ->get();

        return QuestionResource::collection($questions);
    }
}
