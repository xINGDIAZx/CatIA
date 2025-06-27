<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class AITest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_no_autenticado_puede_acceder_a_catia_consejo()
    {
        $response = $this->postJson('/api/catiaConsejo', [
            'message' => 'Test message'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'response'
            ]);
    }

    public function test_usuario_autenticado_puede_acceder_a_catia()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/catia', [
            'message' => 'Test message'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'response'
            ]);
    }

    public function test_usuario_no_autenticado_no_puede_acceder_a_catia()
    {
        $response = $this->postJson('/api/catia', [
            'message' => 'Test message'
        ]);

        $response->assertStatus(401);
    }
}
