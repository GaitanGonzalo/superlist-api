<?php

namespace Tests\Feature;

use Tests\TestCase;

class GeolocationApiTest extends TestCase
{
    /**
     * Test GET /countries returns list of countries.
     */
    public function test_can_get_countries_list(): void
    {
        $response = $this->getJson('/api/countries');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
            ]
        ]);
    }

    /**
     * Test GET /api/countries with search query.
     */
    public function test_can_search_countries(): void
    {
        $response = $this->getJson('/api/countries?search=Argentina');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json());
        $this->assertEquals('Argentina', $response->json()[0]['name']);
    }

    /**
     * Test GET /api/countries/{country_id}/states returns states for that country.
     */
    public function test_can_get_states_by_country(): void
    {
        // Country 5 is Argentina
        $response = $this->getJson('/api/countries/5/states');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'country_id',
                'name',
            ]
        ]);
        
        $states = $response->json();
        $this->assertNotEmpty($states);
        foreach ($states as $state) {
            $this->assertEquals(5, $state['country_id']);
        }
    }

    /**
     * Test GET /api/states/{state_id}/locations returns locations for that state.
     */
    public function test_can_get_locations_by_state(): void
    {
        // State 1818 has locations in LocalidadesLatan.sql
        $response = $this->getJson('/api/states/1818/locations');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'state_id',
                'name',
            ]
        ]);

        $locations = $response->json();
        $this->assertNotEmpty($locations);
        foreach ($locations as $location) {
            $this->assertEquals(1818, $location['state_id']);
        }
    }

    /**
     * Test end-to-end user registration using IDs obtained from mobile local database (Argentina -> Buenos Aires -> D'Orbigny).
     */
    public function test_user_registration_with_mobile_offline_ids(): void
    {
        $payload = [
            'name' => 'Usuario Móvil',
            'last_name' => 'Test',
            'email' => 'mobile.geo.user@example.com',
            'password' => 'Password123!',
            'country_id' => 5,
            'state_id' => 1818,
            'location_id' => 8,
            'address' => 'Calle Falsa 123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'mobile.geo.user@example.com',
            'country_id' => 5,
            'state_id' => 1818,
            'location_id' => 8,
        ]);
    }
}
