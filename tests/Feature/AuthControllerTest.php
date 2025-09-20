<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\TipoUsuarioSeeder;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Prueba que un usuario se puede registrar exitosamente.
     *
     * @return void
     */
    public function test_usuario_puede_registrarse_exitosamente()
    {

        $this->seed(TipoUsuarioSeeder::class);

        $userData = [
            'nombre_usuario' => $this->faker->userName,
            'email_usuario' => $this->faker->unique()->safeEmail,
            'password_usuario' => 'password123',
            'password_usuario_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/registro', $userData);
        // $response->dump();
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'access_token',
                     'token_type'
                 ]);

        $this->assertDatabaseHas('usuario', [
            'email_usuario' => $userData['email_usuario'],
        ]);
    }

    /**
     * Prueba que el registro falla con datos inválidos.
     *
     * @return void
     */
    public function test_registro_falla_con_datos_invalidos()
    {
        // Falta el email, lo que debería causar un error de validación.
        $userData = [
            'nombre_usuario' => $this->faker->userName,
            'password_usuario' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/registro', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email_usuario']);
    }

    /**
     * Prueba que un usuario puede iniciar sesión exitosamente.
     *
     * @return void
     */

    public function test_usuario_puede_iniciar_sesion_exitosamente()
    {
        $this->seed(TipoUsuarioSeeder::class);
        $user = \App\Models\User::factory()->create([
            'nombre_usuario' => 'testuser',
            'email_usuario' => 'test@example.com',
            'password_usuario' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email_usuario' => 'test@example.com',
            'password_usuario' => 'password123',
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
            'user'
        ]);
    }

    /**
     * Prueba que el inicio de sesión falla con credenciales inválidas.
     *
     * @return void
     */
    public function test_login_falla_con_credenciales_invalidas()
    {
        $loginData = [
            'email_usuario' => $this->faker->unique()->safeEmail,
            'password_usuario' => 'password_incorrecta',
        ];

        $response = $this->postJson('/api/v1/auth/login', $loginData);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciales inválidas.']);
    }

    /**
     * Prueba que un usuario puede cerrar sesión.
     *
     * @return void
     */

    public function test_usuario_puede_cerrar_sesion_exitosamente()
    {   
        $this->seed(TipoUsuarioSeeder::class);
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Sesión cerrada exitosamente.']);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'auth_token',
        ]);
    }
}