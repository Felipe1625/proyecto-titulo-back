<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; 

class EventoUsuario extends Model
{
    use HasFactory;

    protected $table = 'evento_usuario';
    protected $primaryKey = 'id_evento_usuario';
    public $timestamps = false;

    protected $fillable = [
        'fk_id_usuario',
        'fk_id_evento',
    ];

    // public function evento()
    // {
    //     return $this->belongsTo(Evento::class, 'fk_id_evento', 'id_evento');
    // }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'fk_id_evento');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario');
    }
}