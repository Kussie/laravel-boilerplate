<?php

use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;

it('can register a user', function (): void {
    $initialDispatcher = Event::getFacadeRoot();
    Event::fake();
    Model::setEventDispatcher($initialDispatcher);

    $response = $this->json('POST', route('auth.register'), [
        'email' => 'register@local.dev',
        'password' => 'abc123',
        'password_confirmation' => 'abc123',
        'first_name' => 'Test',
        'last_name' => 'User',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', 'true')
        ->assertJsonPath('data.user.email', 'register@local.dev')
        ->assertJsonPath('data.user.full_name', 'Test User')
        ->assertJsonPath('data.user.first_name', 'Test')
        ->assertJsonPath('data.user.last_name', 'User');

    Event::assertDispatched(Registered::class);
})->group('auth', 'register');

it('cannot register a user with an invalid email', function (): void {
    $response = $this->json('POST', route('auth.register'), [
        'email' => 'test&&@example,com',
        'password' => 'Abc123!',
        'password_confirmation' => 'Abc123!',
        'first_name' => 'Test',
        'last_name' => 'McTestFace',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The email must be a valid email address.');
})->group('auth', 'register');

it('cannot register a user  with a password which doesnt match', function (): void {
    $response = $this->json('POST', route('auth.register'), [
        'email' => 'test2@example.com',
        'password' => 'Abc123!',
        'password_confirmation' => 'Afdfbc123!',
        'first_name' => 'Test',
        'last_name' => 'McTestFace',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The password confirmation does not match.')
        ->assertJsonPath('errors.password.0', 'The password confirmation does not match.');
})->group('auth', 'register');

it('cannot register a user with a short password', function (): void {
    $response = $this->json('POST', route('auth.register'), [
        'email' => 'test2@example.com',
        'password' => 'abc12',
        'password_confirmation' => 'abc12',
        'first_name' => 'Test',
        'last_name' => 'McTestFace',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The password must be at least 6 characters.')
        ->assertJsonPath('errors.password.0', 'The password must be at least 6 characters.');
})->group('auth', 'register');

it('cannot register a user with a blank password', function (): void {
    $response = $this->json('POST', route('auth.register'), [
        'email' => 'test2@example.com',
        'password' => '',
        'password_confirmation' => '',
        'first_name' => 'Test',
        'last_name' => 'McTestFace',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The password field is required.')
        ->assertJsonPath('errors.password.0', 'The password field is required.');
})->group('auth', 'register');

it('cannot register a user with a short first name', function (): void {
    $response = $this->json('POST', route('auth.register'), [
        'email' => 'test2@example.com',
        'password' => 'Abc123!',
        'password_confirmation' => 'Abc123!',
        'first_name' => 'f',
        'last_name' => 'McTestFace',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The first name must be at least 3 characters.');
})->group('auth', 'register');

it('cannot register a user with a blank first name', function (): void {
    $response = $this->json('POST', route('auth.register'), [
        'email' => 'test2@example.com',
        'password' => 'Abc123!',
        'password_confirmation' => 'Abc123!',
        'first_name' => '',
        'last_name' => 'McTestFace',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The first name field is required.');
})->group('auth', 'register');

it('cannot register a user with a short last name', function (): void {
    $response = $this->json('POST', route('auth.register'), [
        'email' => 'test2@example.com',
        'password' => 'Abc123!',
        'password_confirmation' => 'Abc123!',
        'first_name' => 'Test',
        'last_name' => 'f',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The last name must be at least 3 characters.');
})->group('auth', 'register');

it('cannot register a user with a blank last name', function (): void {
    $response = $this->json('POST', route('auth.register'), [
        'email' => 'test2@example.com',
        'password' => 'Abc123!',
        'password_confirmation' => 'Abc123!',
        'first_name' => 'Test',
        'last_name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The last name field is required.');
})->group('auth', 'register');
