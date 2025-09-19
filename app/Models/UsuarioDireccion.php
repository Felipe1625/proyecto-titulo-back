<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioDireccion extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'usuario_direccion';

    /**
     * El nombre de la clave primaria.
     *
     * @var string
     */
    protected $primaryKey = 'id_usuario_direccion';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'fk_id_usuario',
        'nombre_direccion',
        'latitud_direccion',
        'longitud_direccion',
        'direccion_verificada',
    ];

    /**
     * Indica si el modelo debe ser fechar timestamps.
     * En este caso, la tabla no tiene 'created_at' ni 'updated_at'.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Define la relación "pertenece a" con el modelo Usuario.
     * Una dirección pertenece a un solo usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario', 'id_usuario');
    }
}
