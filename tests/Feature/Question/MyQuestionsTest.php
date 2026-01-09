<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{actingAs, getJson};

it('should list only questions that the logged user has been created :: published', function () {
    $user              = User::factory()->create();
    $userQuestion      = Question::factory()->published()->for($user)->create();
    $userDraftQuestion = Question::factory()->draft()->for($user)->create();

    $anotherUserQuestion      = Question::factory()->published()->create();
    $anotherUserDraftQuestion = Question::factory()->draft()->create();

    Sanctum::actingAs($user);

    $request = getJson(route('my-questions', ['status' => 'published']))
        ->assertOk();

    $request->assertJsonFragment([
        'id'         => $userQuestion->id,
        'question'   => $userQuestion->question,
        'status'     => $userQuestion->status,
        'created_by' => [
            'id'   => $userQuestion->user->id,
            'name' => $userQuestion->user->name,
        ],
        'created_at' => $userQuestion->created_at->format('Y-m-d h:i:s'),
        'updated_at' => $userQuestion->updated_at->format('Y-m-d h:i:s'),
        //TODO: add like and unlike

    ])
        ->assertJsonMissing(['question' => $anotherUserQuestion->question])
        ->assertJsonMissing(['question' => $userDraftQuestion->question])
        ->assertJsonMissing(['question' => $anotherUserDraftQuestion->question]);
});

it('should list only questions that the logged user has been created :: draft', function () {
    $user         = User::factory()->create();
    $userQuestion = Question::factory()->draft()->for($user)->create();

    $anotherUserQuestion = Question::factory()->draft()->create();

    Sanctum::actingAs($user);

    $request = getJson(route('my-questions', ['status' => 'draft']))
        ->assertOk();

    $request->assertJsonFragment([
        'id'         => $userQuestion->id,
        'question'   => $userQuestion->question,
        'status'     => $userQuestion->status,
        'created_by' => [
            'id'   => $userQuestion->user->id,
            'name' => $userQuestion->user->name,
        ],
        'created_at' => $userQuestion->created_at->format('Y-m-d h:i:s'),
        'updated_at' => $userQuestion->updated_at->format('Y-m-d h:i:s'),
        //TODO: add like and unlike

    ])->assertJsonMissing([
        'id'       => $anotherUserQuestion->id,
        'question' => $anotherUserQuestion->question,
    ]);
});

it('should list only questions that the logged user has been created :: archived', function () {
    $user         = User::factory()->create();
    $userQuestion = Question::factory()->archived()->for($user)->create();

    $anotherUserQuestion = Question::factory()->archived()->create();

    Sanctum::actingAs($user);

    $request = getJson(route('my-questions', ['status' => 'archived']))
        ->assertOk();

    $request->assertJsonFragment([
        'id'         => $userQuestion->id,
        'question'   => $userQuestion->question,
        'status'     => $userQuestion->status,
        'created_by' => [
            'id'   => $userQuestion->user->id,
            'name' => $userQuestion->user->name,
        ],
        'created_at' => $userQuestion->created_at->format('Y-m-d h:i:s'),
        'updated_at' => $userQuestion->updated_at->format('Y-m-d h:i:s'),
        //TODO: add like and unlike

    ])->assertJsonMissing([
        'id'       => $anotherUserQuestion->id,
        'question' => $anotherUserQuestion->question,
    ]);
});

test('making sure that only draft, published, and archived statuses can be passed to the route', function ($status, $code) {
    $user = User::factory()->create();
    actingAs($user);

    getJson(route('my-questions', ['status' => $status]))
        ->assertStatus($code);
})->with([
    'draft'     => ['draft', 200],
    'published' => ['published', 200],
    'archived'  => ['archived', 200],
    'anything'  => ['anything', 422],
]);
