<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_tipo_usuario',
        'nombre_usuario',
        'email_usuario',
        'password_usuario',
        'url_img_usuario',
        'ciudad_usuario',
        'alias_usuario',
    ];

    protected $hidden = [
        'password_usuario',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_usuario' => 'hashed'
    ];

    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'id_tipo_usuario', 'id_tipo_usuario');
    }

    public function getAuthPassword()
    {
        return $this->password_usuario;
    }

    public function eventos()
    {
        return $this->belongsToMany(Evento::class, 'evento_usuario', 'fk_id_usuario', 'fk_id_evento');
    }

    public function intereses()
    {
        return $this->belongsToMany(
            Interes::class,
            'usuario_interes',
            'id_usuario',
            'id_interes'
        );
    }

    public function eventoUsuario()
    {
        return $this->belongsToMany(
            Evento::class,
            'evento_usuario',
            'fk_id_usuario',
            'fk_id_evento'
        );
    }

    public function eventoUsuarioInvitacion()
    {
        return $this->hasMany(EventoUsuarioInvitacion::class, 'fk_id_usuario', 'id_usuario');
    }

    public function usuarioDireccion()
    {
        return $this->hasMany(UsuarioDireccion::class, 'fk_id_usuario', 'id_usuario');
    }

    public function eventosOrganizados()
    {
        return $this->hasMany(Evento::class, 'fk_id_organizador', 'id_usuario');
    }

    // public function eventoUsuarioInvitacionDetalle()
    // {
    //     return $this->hasMany(EventoUsuarioInvitacion::class, 'fk_id_usuario', 'id_usuario')
    //                 ->with(['evento' => function ($query) {
    //                     $query->with(['lugarPublico', 'direccionParticular','intereses']);
    //                 }]);
    // }

    public function eventoUsuarioInvitacionDetalle()
    {
        return $this->hasMany(EventoUsuarioInvitacion::class, 'fk_id_usuario', 'id_usuario')
                    ->where('estado_invitacion', 'en espera')
                    ->with([
                        'evento' => function ($query) {
                            $query->with(['lugarPublico', 'direccionParticular', 'intereses']);
                        }
                    ]);
    }
    

    // public function eventosOrganizadosDetalle()
    // {
    //     return $this->hasMany(Evento::class, 'fk_id_organizador', 'id_usuario');
    // }

    public function eventosOrganizadosDetalle()
    {
        return $this->hasMany(Evento::class, 'fk_id_organizador', 'id_usuario')
                    ->with('lugarPublico', 'direccionParticular','intereses');
    }

    // public function eventoUsuarioDetalle()
    // {
    //     return $this->hasMany(EventoUsuario::class, 'fk_id_usuario', 'id_usuario')
    //                 ->with('evento'); // carga los datos del evento asociado
    // }

    public function eventoUsuarioDetalle()
    {
        return $this->hasMany(EventoUsuario::class, 'fk_id_usuario', 'id_usuario')
                    ->with(['evento' => function ($query) {
                        $query->with(['lugarPublico', 'direccionParticular','intereses']);
                    }]); // carga los datos del evento asociado
    }

}
