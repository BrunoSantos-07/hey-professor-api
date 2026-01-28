<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, postJson};

it('should be able to store a question', function () {
    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    postJson(route('questions.store'), [
        'question' => 'Lorem ipsum jeremias?',
    ]);

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'question' => 'Lorem ipsum jeremias?',
    ]);
});

test('with the creation of the question, we need to make sure that it creates with status _draft_', function () {
    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    postJson(route('questions.store'), [
        'question' => 'Lorem ipsum jeremias?',
    ]);

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'status'   => 'draft',
        'question' => 'Lorem ipsum jeremias?',
    ]);
});

describe('validation rules', function () {
    test('question::required', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store'), [
            'question' => '',
        ])->assertJsonValidationErrors([
            'question' => __('validation.required', ['attribute' => __('validation.attributes.question')]),
        ]);
    });

    test('question::ending with question mark', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store'), [
            'question' => 'Question without a question mark',
        ])->assertJsonValidationErrors([
            'question' => __('validation.with_question_mark', ['attribute' => __('validation.attributes.question')]),
        ]);
    });

    test('question::min:10', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store'), [
            'question' => 'Lorem ip',
        ])->assertJsonValidationErrors([
            'question' => __('validation.min.string', ['attribute' => __('validation.attributes.question'), 'min' => 10]),
        ]);
    });

    test('question::max:255', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store'), [
            'question' => str_repeat('a', 256) . '?',
        ])->assertJsonValidationErrors([
            'question' => __('validation.max.string', ['attribute' => __('validation.attributes.question'), 'max' => 255]),
        ]);
    });

    test('question::should be unique', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'question' => 'Lorem ipsum jeremias?',
            'status'   => 'draft',
            'user_id'  => $user->id,
        ]);

        Sanctum::actingAs($user);

        postJson(route('questions.store'), [
            'question' => $question->question,
        ])->assertJsonValidationErrors([
            'question' => __('validation.unique', ['attribute' => __('validation.attributes.question')]),
        ]);
    });

});

test('after creating we should return a status 201 with the created question', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $request = postJson(route('questions.store', [
        'question' => 'Lorem ipsum jeremias?',
    ]))->assertCreated();

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
