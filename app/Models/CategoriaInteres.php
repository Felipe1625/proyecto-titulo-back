<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaInteres extends Model
{
    use HasFactory;

    protected $table = 'categoria_interes';
    protected $primaryKey = 'id_categoria_interes';

    protected $fillable = [
        'nombre_categoria_interes',
        'descripcion_categoria_interes',
    ];

    public function intereses()
    {
        return $this->hasMany(Interes::class, 'id_categoria_interes', 'id_categoria_interes');
    }
}
