<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValoracionEventoUsuarioProblema extends Model
{
    protected $table = 'valoracion_evento_usuario_problema';
    protected $primaryKey = 'id_valoracion_evento_usuario_problema';
    public $timestamps = false;

    protected $guarded = [];

    public function valoracion(): BelongsTo
    {
        return $this->belongsTo(ValoracionEventoUsuario::class, 'id_valoracion', 'id_valoracion');
    }

    public function detalleProblema(): BelongsTo
    {
        return $this->belongsTo(
            ProblemaValoracionEvento::class,
            'id_problema_valoracion_evento',
            'id_problema_valoracion_evento'
        );
    }
}