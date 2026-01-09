<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{Validator};

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
            ['status' => ['required', 'in:draft,published,archived']]
        );

        $questions = user()->questions()
            ->when(
                $status === 'archived',
                fn (Builder $q) => $q->onlyTrashed(),
                fn (Builder $q) => $q->where('status', '=', $status),
            )
            ->get();

        return QuestionResource::collection($questions);
    }
}
