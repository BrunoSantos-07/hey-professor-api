<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{assertDatabaseHas, postJson};
use function PHPUnit\Framework\assertTrue;

it('new users can register', function () {
    $response = postJson('/register', [
        'name'                  => 'John Doe',
        'email'                 => 'joe@doe.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertNoContent();

    assertDatabaseHas('users', [
        'name'  => 'John Doe',
        'email' => 'joe@doe.com',
    ]);

    $joeDoe = User::whereEmail('joe@doe.com')->first();

    assertTrue(Hash::check('password', $joeDoe->password));
});

describe('validations', function () {
    test('name', function ($ruleKey, $value, $meta = []) {
        postJson(route('register'), [
            'name' => $value,
        ])
            ->assertJsonValidationErrors([
                'name' => __(
                    $ruleKey,
                    array_merge(['attribute' => 'name'], $meta)
                ),
            ]);
    })
        ->with([
            'required' => ['validation.required', ''],
            'max:255'  => ['validation.max.string', str_repeat('*', 256), ['max' => 255]],
        ]);

    test('email', function ($ruleKey, $value, $meta = []) {
        if ($ruleKey == 'validation.unique') {
            User::factory()->create(['email' => 'joe@doe.com']);
        }

        postJson(route('register'), ['email' => $value])
            ->assertJsonValidationErrors([
                'email' => __(
                    $ruleKey,
                    array_merge(['attribute' => 'email'], $meta)
                ),
            ]);

    })
        ->with([
            'required' => ['validation.required', ''],
            'max:255'  => ['validation.max.string', str_repeat('*', 256), ['max' => 255]],
            'email'    => ['validation.email', 'not-email'],
            'unique'   => ['validation.unique', 'joe@doe.com'],
        ]);

    test('password', function ($ruleKey, $value, $meta = []) {
        postJson(route('register'), ['password' => $value])
            ->assertJsonValidationErrors([
                'password' => __(
                    $ruleKey,
                    array_merge(['attribute' => 'password'], $meta)
                ),
            ]);
    })
        ->with([
            'required' => ['validation.required', ''],
            'min:8'    => ['validation.min.string', 'AB', ['min' => 8]],
        ]);
});
