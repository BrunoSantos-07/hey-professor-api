<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{assertAuthenticatedAs, postJson};

it('should be able to login', function () {
    $user = User::factory()->create(['email' => 'joe@doe.com', 'password' => Hash::make('password')]);

    postJson(route('login'), [
        'email'    => 'joe@doe.com',
        'password' => 'password',
    ])->assertNoContent();

    assertAuthenticatedAs($user);
});

it('should check if the email and password is valid', function ($email, $password) {
    User::factory()->create([
        'email'    => 'joe@doe.com',
        'password' => Hash::make('password'),
    ]);

    postJson(route('login'), [
        'email'    => $email,
        'password' => $password,
    ])->assertJsonValidationErrors([
        'email' => __('auth.failed'),
    ]);
})->with([
    'wrong email'    => ['wrong@email.com', 'password'],
    'wrong password' => ['wrong@email.com', 'password1231234'],
    'invalid email'  => ['invalid-email', 'password1231234'],
]);

it('required fileds', function () {

    postJson(route('login', [
        'email'    => '',
        'password' => '',
    ]))->assertJsonValidationErrors([
        'email'    => __('validation.required', ['attribute' => 'email']),
        'password' => __('validation.required', ['attribute' => 'password']),
    ]);
});
