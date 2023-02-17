<?php

use App\Models\User;

beforeEach(function (): void {
    $this->testUser = User::factory()->create([
        'email' => 'test@local.dev',
        'first_name' => 'Test',
        'last_name' => 'User',
        'password' => Hash::make('abc123'),
        'email_verified_at' => now(),
    ]);
});

it('can check token status as a logged in user', function (): void {
    $response = $this->json('POST', route('auth.login'), [
        'email' => $this->testUser->email,
        'password' => 'abc123',
        'device_name' => 'test',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.user.email', $this->testUser->email)
        ->assertJsonPath('data.user.full_name', $this->testUser->full_name)
        ->assertJsonPath('data.user.first_name', $this->testUser->first_name)
        ->assertJsonPath('data.user.last_name', $this->testUser->last_name);

    $content = $response->decodeResponseJson();
    $token = $content['data']['token'];

    $this->json('GET', route('auth.check'), [], ['Authorization' => 'Bearer ' . $token])
        ->assertStatus(200);
})->group('auth', 'check');

it('can check my details when logged in', function (): void {
    $response = $this->json('POST', route('auth.login'), [
        'email' => $this->testUser->email,
        'password' => 'abc123',
        'device_name' => 'test',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.user.email', $this->testUser->email)
        ->assertJsonPath('data.user.full_name', $this->testUser->full_name)
        ->assertJsonPath('data.user.first_name', $this->testUser->first_name)
        ->assertJsonPath('data.user.last_name', $this->testUser->last_name);

    $content = $response->decodeResponseJson();
    $token = $content['data']['token'];

    $this->json('GET', route('auth.me'), [], ['Authorization' => 'Bearer ' . $token])
        ->assertStatus(200);
})->group('auth', 'check');

it('cannot check my details when not logged in', function (): void {
    $this->json('GET', route('auth.me'), [], [])
        ->assertStatus(401);
})->group('auth', 'check');

it('cannot check token status as a non logged in user', function (): void {
    $this->json('GET', route('auth.check'), [], [])
        ->assertStatus(401);
})->group('auth', 'check');
