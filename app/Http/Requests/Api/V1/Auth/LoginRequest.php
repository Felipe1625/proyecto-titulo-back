<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email_usuario' => 'required|string|email|max:255',
            'password_usuario' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'email_usuario.required' => 'El correo electrónico es obligatorio.',
            'email_usuario.email' => 'El formato del correo electrónico no es válido.',
            
            'password_usuario.required' => 'La contraseña es obligatoria.',
            'password_usuario.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
