<?php

use App\Models\User;

beforeEach(function (): void {
    $this->verifiedUser = User::factory()->create([
        'email' => 'verified@local.dev',
        'password' => Hash::make('abc123'),
        'email_verified_at' => now(),
    ]);

    $this->unverifiedUser = User::factory()->create([
        'email' => 'unverified@local.dev',
        'password' => Hash::make('abc123'),
        'email_verified_at' => null,
    ]);
});

it('can login as a user', function (): void {
    $response = $this->json('POST', route('auth.login'), [
        'email' => $this->verifiedUser->email,
        'password' => 'abc123',
        'device_name' => 'test',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.user.email', $this->verifiedUser->email)
        ->assertJsonPath('data.user.full_name', $this->verifiedUser->full_name)
        ->assertJsonPath('data.user.first_name', $this->verifiedUser->first_name)
        ->assertJsonPath('data.user.last_name', $this->verifiedUser->last_name);
})->group('auth', 'login');

it('can not login as a non-existent user', function (): void {
    $response = $this->json('POST', route('auth.login'), [
        'email' => 'testsdfsdfds@example.com',
        'password' => 'Abc123!',
        'device_name' => 'test',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The provided credentials are incorrect.');
})->group('auth', 'login');

it('cannot login as an unverified user', function (): void {
    $response = $this->json('POST', route('auth.login'), [
        'email' => $this->unverifiedUser->email,
        'password' => 'abc123',
        'device_name' => 'test',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The provided account has not been verified')
        ->assertJsonPath('errors.data.0', 'The provided account has not been verified');
})->group('auth', 'email-verification', 'login');

it('can logout as a logged in user', function (): void {
    $response = $this->json('POST', route('auth.login'), [
        'email' => $this->verifiedUser->email,
        'password' => 'abc123',
        'device_name' => 'test',
    ]);

    $response->assertStatus(200);

    $content = $response->decodeResponseJson();
    $token = $content['data']['token'];

    $this->json('POST', route('auth.logout'), [], ['Authorization' => 'Bearer ' . $token])
        ->assertStatus(200);
})->group('auth', 'logout');

it('cannot logout as a non logged in user', function (): void {
    $this->json('POST', route('auth.logout'), [], [])
        ->assertStatus(401);
})->group('auth', 'logout');
