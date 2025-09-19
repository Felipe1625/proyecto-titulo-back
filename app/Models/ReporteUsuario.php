<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteUsuario extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'reporte_usuario';

    /**
     * El nombre de la clave primaria.
     *
     * @var string
     */
    protected $primaryKey = 'id_reporte_usuario';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'fk_id_reportador',
        'fk_id_usuario_reportado',
        'fk_id_tipo_reporte_usuario',
        'comentario',
        'fecha_reporte',
    ];

    /**
     * Indica si el modelo debe fechar timestamps.
     * Esta tabla no los tiene.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relación con el usuario que hizo el reporte.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reportador()
    {
        return $this->belongsTo(User::class, 'fk_id_reportador');
    }

    /**
     * Relación con el usuario que fue reportado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuarioReportado()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_reportado');
    }

    /**
     * Relación con el tipo de reporte.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoReporte()
    {
        return $this->belongsTo(TipoReporteUsuario::class, 'fk_id_tipo_reporte_usuario');
    }
}
