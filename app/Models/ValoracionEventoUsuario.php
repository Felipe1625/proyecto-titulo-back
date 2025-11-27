<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ValoracionEventoUsuario extends Model
{
    protected $table = 'valoracion_evento_usuario';
    protected $primaryKey = 'id_valoracion';
    public $timestamps = false;
    protected $dates = ['fecha_valoracion'];

    protected $guarded = [];

    protected $casts = [
        'calificacion' => 'integer',
        'fecha_valoracion' => 'datetime',
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'id_evento', 'id_evento');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

     public function problemasAsociados(): HasMany
    {
        return $this->hasMany(ValoracionEventoUsuarioProblema::class, 'id_valoracion');
    }
}