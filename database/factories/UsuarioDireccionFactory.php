<?php

namespace Database\Factories;

use App\Models\UsuarioDireccion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioDireccionFactory extends Factory
{
    protected $model = UsuarioDireccion::class;

    public function definition()
    {
        return [
            // Se asegura que se inserte solo el ID del usuario
            'fk_id_usuario' => User::factory()->create()->id_usuario,
            'nombre_direccion' => $this->faker->streetAddress,
            'latitud_direccion' => $this->faker->latitude(-90, 90),
            'longitud_direccion' => $this->faker->longitude(-180, 180),
            'direccion_verificada' => $this->faker->boolean(80),
        ];
    }
}
