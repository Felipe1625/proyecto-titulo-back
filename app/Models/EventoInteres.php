<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoInteres extends Model
{
    use HasFactory;

    protected $table = 'evento_interes';

    // Deshabilita la clave primaria por defecto, ya que no tienes una columna 'id'.
    // En su lugar, usas una clave primaria compuesta.
    protected $primaryKey = null;

    // Indica a Laravel que la clave primaria no es de auto-incremento.
    public $incrementing = false;

    // Deshabilita los timestamps si no los tienes en la tabla
    public $timestamps = false;

    protected $fillable = [
        'id_evento',
        'id_interes',
    ];

    /**
     * Relación con el evento.
     */
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'id_evento', 'id_evento');
    }

    /**
     * Relación con el interés.
     */
    public function interes()
    {
        return $this->belongsTo(Interes::class, 'id_interes', 'id_interes');
    }
}