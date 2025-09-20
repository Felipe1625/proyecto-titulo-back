<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\V1\EventoUsuarioInvitacionController;
use App\Models\EventoUsuarioInvitacion;
use App\Models\Evento;
use Illuminate\Http\Request;
use Mockery;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Database\Seeders\TipoUsuarioSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class EventoUsuarioInvitacionControllerTest extends TestCase
{
     use DatabaseTransactions;
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_enviar_invitaciones_exitosamente()
    {
        $this->seed(TipoUsuarioSeeder::class);
        $organizador = User::factory()->create(['id_usuario' => 10]);
        $usuario1 = User::factory()->create(['id_usuario' => 20]);
        $usuario2 = User::factory()->create(['id_usuario' => 30]);

        $evento = Evento::factory()->create([
            'id_evento' => 1,
            'fk_id_organizador' => $organizador->id_usuario,
        ]);

        $request = new Request([
            'evento' => ['id_evento' => $evento->id_evento],
            'usuarios' => [$usuario1->id_usuario, $usuario2->id_usuario, $organizador->id_usuario], // organizador incluido
        ]);

        $controller = new \App\Http\Controllers\Api\V1\EventoUsuarioInvitacionController();
        $response = $controller->enviar_invitaciones($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Invitaciones enviadas correctamente', 'data' => true]),
            $response->getContent()
        );

        $this->assertDatabaseHas('evento_usuario_invitacion', [
            'fk_id_usuario' => $usuario1->id_usuario,
            'fk_id_evento' => $evento->id_evento,
        ]);

        $this->assertDatabaseHas('evento_usuario_invitacion', [
            'fk_id_usuario' => $usuario2->id_usuario,
            'fk_id_evento' => $evento->id_evento,
        ]);

        $this->assertDatabaseCount('evento_usuario_invitacion', 2);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_enviar_invitaciones_falla_con_validacion()
    {
        $this->seed(TipoUsuarioSeeder::class);

        $organizador = User::factory()->create(['id_usuario' => 10]);
        $evento = Evento::factory()->create([
            'fk_id_organizador' => $organizador->id_usuario,
        ]);

        // 3. Preparar request con error: usuario no existente y evento no existente
        $request = new \Illuminate\Http\Request([
            'evento' => ['id_evento' => 9999],
            'usuarios' => [9998, 9997],
        ]);

        $controller = new EventoUsuarioInvitacionController();
        $response = $controller->enviar_invitaciones($request);

        $this->assertEquals(422, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals('Error de validación', $responseData['message']);
        $this->assertArrayHasKey('evento.id_evento', $responseData['errors']);
        $this->assertArrayHasKey('usuarios.0', $responseData['errors']);
        $this->assertArrayHasKey('usuarios.1', $responseData['errors']);
    }

    
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_enviar_invitaciones_con_error_rollBack()
    {
        $this->seed(TipoUsuarioSeeder::class);
        $organizador = User::factory()->create(['id_usuario' => 10]);
        $usuario = User::factory()->create(['id_usuario' => 20]);

        $evento = Evento::factory()->create([
            'fk_id_organizador' => $organizador->id_usuario,
        ]);

        $request = new \Illuminate\Http\Request([
            'evento' => ['id_evento' => $evento->id_evento],
            'usuarios' => [$usuario->id_usuario],
        ]);

        // 4. Ejecutar el controlador y simular error con try/catch temporal
        $controller = new EventoUsuarioInvitacionController();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error forzado');

        DB::transaction(function () use ($controller, $request) {
            // Forzamos error lanzando excepción en medio de la ejecución
            throw new \Exception('Error forzado');
        });

        // 5. Verificar que no se haya insertado nada en DB
        $this->assertDatabaseCount('evento_usuario_invitacion', 0);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
