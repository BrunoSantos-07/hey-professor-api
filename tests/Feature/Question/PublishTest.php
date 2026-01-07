<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, putJson};

it('should be able to publish a question', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->for($user)->create(['status' => 'draft']);

    Sanctum::actingAs($user);

    putJson(route('questions.publish', $question))
        ->assertNoContent();

    assertDatabaseHas('questions', ['id' => $question->id, 'status' => 'published']);
});

it('should allow that only the creator can publish', function () {
    $user1    = User::factory()->create();
    $user2    = User::factory()->create();
    $question = Question::factory()->for($user1)->create(['status' => 'draft']);

    Sanctum::actingAs($user2);

    putJson(route('questions.publish', $question))
        ->assertForbidden();

    assertDatabaseHas('questions', ['id' => $question->id, 'status' => 'draft']);
});

it('should only publish when the question is on status draft', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->for($user)->create(['status' => 'published']);

    Sanctum::actingAs($user);

    putJson(route('questions.publish', $question))
        ->assertNotFound();

    assertDatabaseHas('questions', ['id' => $question->id, 'status' => 'published']);
});
