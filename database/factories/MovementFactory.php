<?php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovementFactory extends Factory
{
    protected $model = Movement::class;

    public function definition(): array
    {
        $isIncome = $this->faker->boolean();

        return [
            'wallet_id' => Wallet::factory(),
            'mes' => $this->faker->date('Y-m'),
            'detalle_ingreso' => $isIncome ? $this->faker->sentence() : null,
            'ingreso' => $isIncome ? $this->faker->randomFloat(2, 0, 1000) : null,
            'detalle_gasto' => !$isIncome ? $this->faker->sentence() : null,
            'gasto' => !$isIncome ? $this->faker->randomFloat(2, 0, 1000) : null
        ];
    }
}
