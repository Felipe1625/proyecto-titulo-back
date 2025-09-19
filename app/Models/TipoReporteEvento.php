<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoReporteEvento extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'tipo_reporte_evento';

    /**
     * El nombre de la clave primaria.
     *
     * @var string
     */
    protected $primaryKey = 'id_tipo_reporte_evento';

    /**
     * Los atributos que se pueden asignar masivamente.
     * En este caso, solo el nombre del tipo de reporte.
     *
     * @var array
     */
    protected $fillable = [
        'nombre_tipo_reporte',
    ];

    /**
     * Indica si el modelo debe ser fechar timestamps.
     * Esta tabla no los tiene.
     *
     * @var bool
     */
    public $timestamps = false;
}
