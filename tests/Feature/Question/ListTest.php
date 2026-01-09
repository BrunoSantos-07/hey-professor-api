<?php

use App\Models\{Question, User};

use function Pest\Laravel\{actingAs, getJson};

it('shoulde be able to list only published questions', function () {
    $published = Question::factory()->published()->create();
    $draft     = Question::factory()->draft()->create();
    $user      = User::factory()->create();

    actingAs($user);

    $request = getJson(route('questions.index'))
        ->assertOk();

    $request->assertJsonFragment([
        'id'         => $published->id,
        'question'   => $published->question,
        'status'     => $published->status,
        'created_by' => [
            'id'   => $published->user->id,
            'name' => $published->user->name,
        ],
        'created_at' => $published->created_at->format('Y-m-d h:i:s'),
        'updated_at' => $published->updated_at->format('Y-m-d h:i:s'),
        //TODO: add like and unlike

    ])->assertJsonMissing([
        'id'       => $draft->id,
        'question' => $draft->question,
    ]);
});
