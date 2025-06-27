<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Movement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class DataFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'ciudad' => 'Bogotá'
        ]);
        Sanctum::actingAs($this->user);
    }

    public function test_puede_obtener_datos_del_usuario()
    {
        $response = $this->postJson('/api/dataUser');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'ciudad'
                ]
            ]);
    }

    public function test_puede_actualizar_datos_del_usuario()
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'ciudad' => 'Medellín'
        ];

        $response = $this->postJson('/api/updateUser', $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'ciudad'
                ]
            ]);
    }

    public function test_puede_obtener_carteras()
    {
        Wallet::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->postJson('/api/getWallets');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'wallets' => [
                    '*' => [
                        'id',
                        'user_id',
                        'nombre',
                        'monto',
                        'descripcion'
                    ]
                ]
            ]);
    }

    public function test_puede_agregar_cartera()
    {
        $walletData = [
            'nombre' => 'Test Wallet',
            'monto' => 1000,
            'descripcion' => 'Test Description'
        ];

        $response = $this->postJson('/api/addWallet', $walletData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'wallet' => [
                    'id',
                    'user_id',
                    'nombre',
                    'monto',
                    'descripcion'
                ]
            ]);
    }

    public function test_puede_eliminar_cartera()
    {
        $wallet = Wallet::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->postJson('/api/deleteWallet', [
            'wallet_id' => $wallet->id,
            'user_id' => $this->user->id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Cartera eliminada correctamente'
            ]);

        $this->assertDatabaseMissing('billeteras', [
            'id' => $wallet->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_puede_obtener_movimientos_por_cartera()
    {
        $wallet = Wallet::factory()->create([
            'user_id' => $this->user->id
        ]);

        Movement::factory()->count(3)->create([
            'wallet_id' => $wallet->id
        ]);

        $response = $this->postJson('/api/getMovementsbyWallet', [
            'wallet_id' => $wallet->id
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'wallet_id',
                    'mes',
                    'detalle_ingreso',
                    'ingreso',
                    'detalle_gasto',
                    'gasto',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_puede_agregar_movimiento_a_cartera()
    {
        $wallet = Wallet::factory()->create([
            'user_id' => $this->user->id
        ]);

        $movementData = [
            'wallet_id' => $wallet->id,
            'tipo' => 'Ingreso',
            'monto' => 100,
            'detalle' => 'Test Movement',
            'mes' => date('Y-m')
        ];

        $response = $this->postJson('/api/addMovementsbyWallet', $movementData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'movement' => [
                    'id',
                    'wallet_id',
                    'mes',
                    'detalle_ingreso',
                    'ingreso',
                    'detalle_gasto',
                    'gasto'
                ]
            ]);
    }
}
