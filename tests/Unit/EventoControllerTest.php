<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\V1\EventoController;
use App\Models\User;
use App\Models\LugarPublico;
use App\Models\UsuarioDireccion;
use App\Models\Evento;
use App\Models\EventoUsuario;
use App\Models\EventoInteres;
use App\Models\EventoUsuarioInvitacion;
use Illuminate\Http\Request;
use Mockery;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Database\Seeders\TipoUsuarioSeeder;

class EventoControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * Se ejecuta después de cada test para cerrar el Mockery.
     */
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Testea que la creación de un evento público falle si el organizador no existe.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_crear_evento_publico_falla_con_organizacion_no_encontrado()
    {
        // 1. Datos de prueba
        $eventoData = [
            'evento' => ['id_organizador' => 999], // ID que no existe
        ];

        // 2. Mocks
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')->andReturn($eventoData);
        $requestMock->shouldReceive('input')->with('evento.id_organizador')->andReturn(999);
        $requestMock->shouldReceive('validate')->once()->andReturn(true);

        $userMock = Mockery::mock('overload:' . User::class);
        $userMock->shouldReceive('findOrFail')->once()->andThrow(new ModelNotFoundException());

        // 3. Ejecución del método del controlador
        $controller = new EventoController();
        $response = $controller->crear_evento_publico($requestMock);

        // 4. Afirmaciones
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('El organizador especificado no fue encontrado.', $response->getContent());
    }

    /**
     * Testea que la eliminación de un evento falle si no se encuentra.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_eliminar_evento_falla_con_evento_no_encontrado()
    {
        // 1. Datos de prueba
        $eventoId = 999;

        // 2. Mocks
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('validate')->once()->andReturn(true);
        $requestMock->shouldReceive('input')->with('id_evento')->andReturn($eventoId);
        
        $eventoMock = Mockery::mock('overload:' . Evento::class);
        $eventoMock->shouldReceive('find')->once()->with($eventoId)->andReturn(null);
        
        $dbMock = Mockery::mock('overload:Illuminate\Support\Facades\DB');
        $dbMock->shouldReceive('beginTransaction')->once();
        $dbMock->shouldReceive('rollBack')->once();
        $dbMock->shouldReceive('commit')->never();

        // 3. Ejecución del método del controlador
        $controller = new EventoController();
        $response = $controller->eliminar_evento($requestMock);

        // 4. Afirmaciones
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('El evento no fue encontrado.', $response->getContent());
    }
}
