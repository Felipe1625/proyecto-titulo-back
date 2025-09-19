<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'evento';

    /**
     * El nombre de la clave primaria.
     *
     * @var string
     */
    protected $primaryKey = 'id_evento';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'fk_id_organizador',
        'nombre_evento',
        'descripcion_evento',
        'fecha_evento',
        'hora_inicio_evento',
        'hora_termino_evento',
        'cantidad_personas_evento',
        'fk_id_direccion_particular',
        'fk_id_lugar_publico',
        'estado_evento'
    ];

    /**
     * Indica si el modelo debe fechar timestamps.
     * Esta tabla no los tiene.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relación con el usuario que organiza el evento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organizador()
    {
        return $this->belongsTo(User::class, 'fk_id_organizador');
    }

    /**
     * Relación con la dirección particular del evento.
     * Es una relación opcional, ya que el evento también puede tener una ubicación pública.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function direccionParticular()
    {
        return $this->belongsTo(UsuarioDireccion::class, 'fk_id_direccion_particular');
    }

    /**
     * Relación con el lugar público del evento.
     * Es una relación opcional, ya que el evento también puede tener una dirección particular.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lugarPublico()
    {
        return $this->belongsTo(LugarPublico::class, 'fk_id_lugar_publico');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'evento_usuario', 'fk_id_evento', 'fk_id_usuario');
    }

    public function intereses()
    {
        return $this->belongsToMany(
            Interes::class,  
            'evento_interes',
            'id_evento',     
            'id_interes'     
        );
    }

    public function eventoUsuarios()
    {
        return $this->hasMany(EventoUsuario::class, 'fk_id_evento', 'id_evento');
    }

    public function invitaciones()
    {
        return $this->hasMany(EventoUsuarioInvitacion::class, 'fk_id_evento', 'id_evento');
    }
}
