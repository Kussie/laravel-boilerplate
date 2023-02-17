<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;

beforeEach(function (): void {
    $this->verifiedUser = User::factory()->create([
        'email' => 'verified@local.dev',
        'password' => Hash::make('abc123'),
        'email_verified_at' => now(),
    ]);
});

it('can request a forgotten password', function (): void {
    Event::fake();

    $response = $this->json('POST', route('auth.reset.email'), [
        'email' => $this->verifiedUser->email,
    ]);
    $response->assertStatus(200);

    $resets = DB::table('password_resets')->first();
    $pwresponse = $this->json('POST', route('auth.reset.password'), [
        'email' => $this->verifiedUser->email,
        'token' => $resets->token,
        'password' => 'Abc!2345',
        'password_confirmation' => 'Abc!2345',
    ]);

    $pwresponse->assertStatus(200)
        ->assertJsonPath('success', 'true');

    Event::assertDispatched(PasswordReset::class);
})->group('auth', 'forgot-password');
