<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LugarPublico;

class LugarPublicoFactory extends Factory
{
    protected $model = LugarPublico::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre_lugar' => $this->faker->address(),
            'latitud_lugar' => $this->faker->latitude(-90, 90),
            'longitud_lugar' => $this->faker->longitude(-180, 180),
        ];
    }
}
