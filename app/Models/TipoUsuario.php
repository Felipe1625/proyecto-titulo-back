<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    use HasFactory;
    protected $table = 'tipo_usuario';
    protected $primaryKey = 'id_tipo_usuario';

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_tipo_usuario', 'id_tipo_usuario');
    }
}
