<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipo_usuario')->insert([
            ['id_tipo_usuario' => 1, 'nombre_tipo_usuario' => 'Administrador', 'descripcion_tipo_usuario' => 'Usuario con permisos de administrador.'],
            ['id_tipo_usuario' => 2, 'nombre_tipo_usuario' => 'Usuario', 'descripcion_tipo_usuario' => 'Usuario regular del sistema.'],
        ]);
    }
}
