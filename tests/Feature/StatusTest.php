<?php

namespace Tests\Feature;
use Tests\TestCase;

class StatusTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_status_route(): void
    {
        $response = $this->getJson(route('api.status'));

        $response->assertStatus(200);
    }
}