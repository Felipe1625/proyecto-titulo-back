<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interes extends Model
{
    use HasFactory;

    protected $table = 'interes';
    protected $primaryKey = 'id_interes';

    protected $fillable = [
        'id_categoria_interes',
        'nombre_interes',
        'descripcion_interes',
    ];

    public function categoriaInteres()
    {
        return $this->belongsTo(CategoriaInteres::class, 'id_categoria_interes', 'id_categoria_interes');
    }
}
