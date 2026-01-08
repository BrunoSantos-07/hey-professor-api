<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{assertDatabaseHas, postJson};
use function PHPUnit\Framework\assertTrue;

it('should be able to register in the application', function () {
    postJson(route('register'), [
        'name'     => 'John Doe',
        'email'    => 'joe@doe.com',
        'password' => 'password',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('users', [
        'name'  => 'John Doe',
        'email' => 'joe@doe.com',
    ]);

    $joeDoe = User::whereEmail('joe@doe.com')->first();

    assertTrue(Hash::check('password', $joeDoe->password));
});

describe('validations', function () {

    /**
     * Testa as validações do campo "name" no endpoint de registro.
     *
     * O teste recebe:
     * - $ruleKey: chave da tradução da regra (ex: validation.max.string)
     * - $value: valor inválido a ser enviado no request
     * - $meta: parâmetros extras usados na mensagem de erro (ex: min, max)
     */
    test('name', function ($ruleKey, $value, $meta = []) {

        postJson(route('register'), [
            // Valor inválido que será testado para o campo "name"
            'name' => $value,
        ])
        ->assertJsonValidationErrors([
            'name' => __(
                // Chave da tradução da regra de validação
                $ruleKey,

                // Merge do atributo com os metadados da regra
                // (ex: ['min' => 3] ou ['max' => 255])
                array_merge(['attribute' => 'name'], $meta)
            ),
        ]);
    })
    ->with([
        'required' => ['validation.required', ''],
        'min:3'    => ['validation.min.string', 'AB', ['min' => 3]],
        'max:255'  => ['validation.max.string', str_repeat('*', 256), ['max' => 255]],
    ]);

    test('email', function ($ruleKey, $value, $meta = []) {

        postJson(route('register'), ['email' => $value])
        ->assertJsonValidationErrors([
            'email' => __(
                // Chave da tradução da regra de validação
                $ruleKey,
                array_merge(['attribute' => 'email'], $meta)
            ),
        ]);
    })
    ->with([
        'required' => ['validation.required', ''],
        'max:255'  => ['validation.max.string', str_repeat('*', 256), ['max' => 255]],
        'email'    => ['validation.email', 'not-email'],
    ]);

    test('password', function ($ruleKey, $value, $meta = []) {

        postJson(route('register'), ['password' => $value])
        ->assertJsonValidationErrors([
            'password' => __(
                // Chave da tradução da regra de validação
                $ruleKey,
                array_merge(['attribute' => 'password'], $meta)
            ),
        ]);
    })
    ->with([
        'required' => ['validation.required', ''],
        'min:8'    => ['validation.min.string', 'AB', ['min' => 8]],
        'max:40'   => ['validation.max.string', str_repeat('*', 41), ['max' => 40]],
    ]);
});
