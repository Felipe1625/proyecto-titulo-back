<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\V1\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Mockery;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Database\Seeders\TipoUsuarioSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_actualizar_ciudad_usuario_exitosamente()
    {
        // 1. Preparamos el mock del Request
        $requestMock = \Mockery::mock(\Illuminate\Http\Request::class);
        $requestMock->shouldReceive('validate')->once()->with([
            'id_usuario' => 'required|integer',
            'ciudad_usuario' => 'required|string|max:255',
        ]);
        $requestMock->shouldReceive('input')->once()->with('id_usuario')->andReturn(1);
        $requestMock->shouldReceive('input')->once()->with('ciudad_usuario')->andReturn('Concepción');

        // 2. Preparamos el mock del modelo con overload
        $userMock = \Mockery::mock('overload:App\Models\User')->makePartial();
        $userMock->id_usuario = 1;
        $userMock->nombre_usuario = 'Test User';
        $userMock->ciudad_usuario = 'Santiago';

        $userMock->shouldReceive('save')->once()->andReturn(true);

        // findOrFail debe devolver nuestro mock
        $userMock->shouldReceive('findOrFail')->once()->with(1)->andReturn($userMock);

        // 3. Ejecutamos el controlador
        $controller = new \App\Http\Controllers\Api\V1\UserController();
        $response = $controller->actualizar_ciudad_usuario($requestMock);

        // 4. Verificamos la respuesta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode([
            'message' => 'Ciudad de usuario actualizada exitosamente.',
            'user' => [
                'id' => 1,
                'nombre' => 'Test User',
                'ciudad' => 'Concepción',
            ]
        ]), $response->getContent());
    }



    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_actualizar_ciudad_usuario_devuelve_404_si_no_existe()
    {
        // 1. Preparamos el mock de la solicitud y el error
        $requestData = ['id_usuario' => 99, 'ciudad_usuario' => 'Concepción'];
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('validate')->once()->with([
            'id_usuario' => 'required|integer',
            'ciudad_usuario' => 'required|string|max:255',
        ]);
        $requestMock->shouldReceive('input')->once()->with('id_usuario')->andReturn(99);

        // 2. Mockeamos la excepción de no encontrado
        $userMock = Mockery::mock('alias:'.User::class);
        $userMock->shouldReceive('findOrFail')->once()->with(99)->andThrow(new ModelNotFoundException());
        
        // 3. Instanciamos el controlador y llamamos al método
        $controller = new UserController();
        $response = $controller->actualizar_ciudad_usuario($requestMock);

        // 4. Verificamos la respuesta
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('Usuario no encontrado', $response->getContent());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_obtener_listado_usuarios_exitosamente()
    {
        // 1. Preparamos datos de prueba
        $usersMock = collect([
            (object)['id_usuario' => 1, 'nombre_usuario' => 'User1'],
            (object)['id_usuario' => 2, 'nombre_usuario' => 'User2'],
        ]);

        // 2. Mockeamos el modelo User
        $userMock = Mockery::mock('alias:' . User::class);
        $userMock->shouldReceive('all')->once()->andReturn($usersMock);

        // 3. Instanciamos el controlador y llamamos al método
        $controller = new UserController();
        $response = $controller->obtener_listado_usuarios();

        // 4. Verificamos la respuesta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'message' => 'Listado de usuarios obtenido exitosamente.',
            'data' => $usersMock,
            'count' => 2,
        ]), $response->getContent());
    }

    public function test_obtener_eventos_usuario_exitosamente()
    {
        // Seed si es necesario
        $this->seed(TipoUsuarioSeeder::class);

        // Creamos usuario real en DB
        $user = \App\Models\User::factory()->create([
            'id_usuario' => 1,
            'nombre_usuario' => 'Test User',
            'ciudad_usuario' => 'Santiago',
        ]);

        // Simulamos relaciones vacías
        $user->setRelation('eventoUsuarioDetalle', collect());
        $user->setRelation('eventosOrganizadosDetalle', collect());
        $user->setRelation('eventoUsuarioInvitacionDetalle', collect());

        // Request
        $request = new \Illuminate\Http\Request(['id_usuario' => 1]);

        // Ejecutamos controlador
        $controller = new \App\Http\Controllers\Api\V1\UserController();
        $response = $controller->obtener_eventos_usuario($request);

        // Verificaciones
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals('Estructura de datos de usuario recuperada.', $responseData['message']);
        $this->assertEquals(1, $responseData['user']['id_usuario']);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}