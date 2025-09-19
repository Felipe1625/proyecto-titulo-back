<?php
namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_usuario' => 'required|string|max:255',
            'email_usuario' => 'required|string|email|max:255|unique:usuario,email_usuario',
            'password_usuario' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
            'nombre_usuario.string' => 'El nombre de usuario debe ser una cadena de texto.',
            'nombre_usuario.max' => 'El nombre de usuario no puede exceder los 255 caracteres.',

            'email_usuario.required' => 'El correo electrónico es obligatorio.',
            'email_usuario.email' => 'El formato del correo electrónico no es válido.',
            'email_usuario.unique' => 'El correo electrónico ya está registrado.',

            'password_usuario.required' => 'La contraseña es obligatoria.',
            'password_usuario.string' => 'La contraseña debe ser una cadena de texto.',
            'password_usuario.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password_usuario.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }
}