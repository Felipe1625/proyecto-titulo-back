<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemaValoracionEvento extends Model
{
    use HasFactory;

    protected $table = 'problema_valoracion_evento';
    protected $primaryKey = 'id_problema_valoracion_evento';

    protected $fillable = [
        'nombre_problema_valoracion_evento'
    ];

}
