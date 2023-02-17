<?php

namespace Tests\Unit;

use App\Models\User;

it('can get the full name attribute', function (): void {
    $testUser = User::factory()->create([
        'email' => 'test@local.dev',
        'first_name' => 'Test',
        'last_name' => 'User',
    ]);

    $this->assertEquals('Test User', $testUser->full_name);

    $testUser->full_name = 'Hello World';
    $testUser->save();

    $this->assertEquals('Hello World', $testUser->full_name);
});

it('Can set the full name attribute through mutator', function (): void {
    $testUser = User::factory()->create([
        'email' => 'test@local.dev',
    ]);

    $testUser->full_name = 'Hello World';
    $testUser->save();

    $this->assertEquals('Hello', $testUser->first_name);
    $this->assertEquals('World', $testUser->last_name);
});
