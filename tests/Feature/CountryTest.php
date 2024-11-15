<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_success()
    {
        $response = $this->getJson('/api/countries');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['code', 'name']
                 ]);
    }

    public function test_show_success()
    {
        $response = $this->getJson('/api/countries/US');

        $response->assertStatus(200)
                 ->assertJson(['name' => 'Estados Unidos']);
    }

    public function test_show_not_found()
    {
        $response = $this->getJson('/api/countries/XX');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'No se encontró el país']);
    }
}
