<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LugarPublico extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'lugar_publico';

    /**
     * El nombre de la clave primaria.
     *
     * @var string
     */
    protected $primaryKey = 'id_lugar';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'nombre_lugar',
        'latitud_lugar',
        'longitud_lugar',
    ];

    /**
     * Indica si el modelo debe fechar timestamps.
     * Esta tabla no los tiene.
     *
     * @var bool
     */
    public $timestamps = false;
}
