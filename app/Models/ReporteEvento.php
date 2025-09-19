<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteEvento extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'reporte_evento';

    /**
     * El nombre de la clave primaria.
     *
     * @var string
     */
    protected $primaryKey = 'id_reporte_evento';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'fk_id_reportador',
        'fk_id_evento_reportado',
        'fk_id_tipo_reporte_evento',
        'comentario',
        'fecha_reporte',
    ];

    /**
     * Indica si el modelo debe fechar timestamps.
     * Como ya tienes 'fecha_reporte', puedes desactivar los timestamps por defecto de Eloquent.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relación con el usuario que hizo el reporte.
     *
     * @return BelongsTo
     */
    public function reportador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_id_reportador');
    }

    /**
     * Relación con el evento que fue reportado.
     *
     * @return BelongsTo
     */
    public function eventoReportado(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'fk_id_evento_reportado');
    }

    /**
     * Relación con el tipo de reporte.
     *
     * @return BelongsTo
     */
    public function tipoReporte(): BelongsTo
    {
        return $this->belongsTo(TipoReporteEvento::class, 'fk_id_tipo_reporte_evento');
    }
}
