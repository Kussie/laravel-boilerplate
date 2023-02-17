<?php

it('can load health ping', function (): void {
    $response = $this->get(route('health.ping'), []);
    $response->assertStatus(200);
});

it('can load health info', function (): void {
    $response = $this->get(route('health.info'), []);
    $response->assertStatus(200);
});
