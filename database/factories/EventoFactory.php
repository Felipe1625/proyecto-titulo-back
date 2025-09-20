<?php

namespace Database\Factories;

use App\Models\Evento;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class EventoFactory extends Factory
{
    protected $model = Evento::class;

    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('+1 days', '+2 months');
        $endDate = (clone $startDate)->modify('+2 hours');

        return [
            'fk_id_organizador' => 1, // se puede sobrescribir en el create()
            'nombre_evento' => $this->faker->sentence(3),
            'descripcion_evento' => $this->faker->paragraph,
            'fecha_evento' => $startDate->format('Y-m-d'),
            'hora_inicio_evento' => $startDate->format('H:i:s'),
            'hora_termino_evento' => $endDate->format('H:i:s'),
            'cantidad_personas_evento' => $this->faker->numberBetween(5, 100),
            'fk_id_direccion_particular' => null, // opcional
            'fk_id_lugar_publico' => null,       // opcional
            'estado_evento' => 'activo',         // valor por defecto
        ];
    }
}
