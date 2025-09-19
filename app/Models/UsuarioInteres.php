<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioInteres extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'usuario_interes';

    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_usuario',
        'id_interes',
    ];
}