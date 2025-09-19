<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User; 

class EventoUsuarioInvitacion extends Model
{
    use HasFactory;

    /**
     * Constantes para los estados de la invitación.
     * Esto hace el código más claro y seguro.
     */
    const ESTADO_ESPERA = 'en espera';
    const ESTADO_ACEPTADA = 'aceptada';
    const ESTADO_DECLINADA = 'declinada';

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'evento_usuario_invitacion';

    /**
     * El nombre de la clave primaria.
     *
     * @var string
     */
    protected $primaryKey = 'id_evento_usuario_invitacion';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'fk_id_evento',
        'fk_id_usuario',
        'fk_id_organizador',
        'estado_invitacion',
        'fecha_invitacion',
        'mensaje_organizador',
    ];

    /**
     * Indica si el modelo debe fechar timestamps.
     * Como ya tienes 'fecha_invitacion', puedes desactivar los timestamps por defecto de Eloquent.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relación con el evento.
     *
     * @return BelongsTo
     */
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'fk_id_evento');
    }

    /**
     * Relación con el usuario invitado.
     *
     * @return BelongsTo
     */
    public function invitado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_id_usuario');
    }

    /**
     * Relación con el usuario que organizó y envió la invitación.
     *
     * @return BelongsTo
     */
    public function organizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_id_organizador');
    }

}
