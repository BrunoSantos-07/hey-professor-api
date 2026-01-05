<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, putJson};

it('should be able to edit a question', function () {
    $user     = User::factory()->createOne();
    $question = Question::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    putJson(route('questions.update', $question), [
        'question' => 'Update question?',
    ])->assertOK();

    assertDatabaseHas('questions', [
        'id'       => $question->id,
        'user_id'  => $user->id,
        'question' => 'Update question?',
    ]);
});

describe('validation rules', function () {
    test('question::required', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => '',
        ])->assertJsonValidationErrors([
            'question' => 'required.',
        ]);
    });

    test('question::ending with question mark', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Question without a question mark',
        ])->assertJsonValidationErrors([
            'question' => 'with a question mark.',
        ]);
    });

    test('question::min:10', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Lorem ip',
        ])->assertJsonValidationErrors([
            'question' => 'must be at least 10 characters.',
        ]);
    });

    test('question::max:255', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => str_repeat('a', 256) . '?',
        ])->assertJsonValidationErrors([
            'question' => 'must not be greater than 255 characters.',
        ]);
    });

    test('question::should be unique only if id is different', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'question' => 'Lorem ipsum jeremias?',
            'status'   => 'draft',
            'user_id'  => $user->id,
        ]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => $question->question,
        ])->assertOk();
    });

    test('question::should be able to edit only if the status is in draft', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'published']);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Question should have a mark?',
        ])->assertJsonValidationErrors([
            'question' => 'The question should be a draft to be able to edit.',
        ]);
    });

});

describe('security', function () {
    test('only the person who create the question can update the same question', function () {
        $user1    = User::factory()->create();
        $user2    = User::factory()->create();
        $question = Question::factory()->create([
            'user_id' => $user1->id,
        ]);

        Sanctum::actingAs($user2);

        putJson(route('questions.update', $question), [
            'question' => 'Updating the question?',
        ])->assertForbidden();

        assertDatabaseHas('questions', [
            'id'       => $question->id,
            'question' => $question->question,
        ]);
    });
});

test('should return a status 200 with the updated question', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $request = putJson(route('questions.update', $question), [
        'question' => 'Update question?',
    ])->assertOK();

    $question = Question::latest()->first();

    $request->assertJson([
        'data' => [
            'id'         => $question->id,
            'question'   => $question->question,
            'status'     => $question->status,
            'created_by' => [
                'id'   => $user->id,
                'name' => $user->name,
            ],
            'created_at' => $question->created_at->format('Y-m-d h:i:s'),
            'updated_at' => $question->updated_at->format('Y-m-d h:i:s'),
        ],
    ]);
});
