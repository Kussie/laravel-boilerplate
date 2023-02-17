<?php

use App\Events\ResendVerification;
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

it('can resend verification email', function (): void {
    Event::fake();

    $response = $this->json('POST', route('auth.resend.verification'), [
        'email' => $this->unverifiedUser->email,
    ]);

    Event::assertDispatched(ResendVerification::class);
    $response->assertStatus(200);
})->group('auth', 'email-verification');

it('can verify email address', function (): void {
    $response = $this->json('POST', route('auth.login'), [
        'email' => $this->unverifiedUser->email,
        'password' => 'abc123',
        'device_name' => 'test',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'The provided account has not been verified')
        ->assertJsonPath('errors.data.0', 'The provided account has not been verified');

    $regData = ['id' => $this->unverifiedUser->id, 'hash' => sha1($this->unverifiedUser->email)];

    $this->get(route('auth.email.verification', $regData))
        ->assertStatus(200);
})->group('auth', 'email-verification');

it('cannot verify an already verified email address', function (): void {
    $regData = ['id' => $this->verifiedUser->id, 'hash' => sha1($this->verifiedUser->email)];

    $this->get(route('auth.email.verification', $regData))
        ->assertStatus(400);
})->group('auth', 'email-verification');

it('cannot verify email address of unknown user', function (): void {
    $regData = ['id' => 9999, 'hash' => sha1($this->unverifiedUser->email)];

    $this->get(route('auth.email.verification', $regData))
        ->assertStatus(404);
})->group('auth', 'email-verification');
