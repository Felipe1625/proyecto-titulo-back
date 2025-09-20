<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\V1\UsuarioDireccionController;
use App\Models\UsuarioDireccion;
use Illuminate\Http\Request;
use Mockery;
use Illuminate\Validation\ValidationException;

class UsuarioDireccionControllerTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_guardar_usuario_direccion_exitosamente()
    {
        $requestData = [
            'id_usuario' => 1,
            'nombre_direccion' => 'Mi Casa',
            'latitud_direccion' => -36.8277,
            'longitud_direccion' => -73.0506,
        ];

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('validate')->once()->with([
            'id_usuario' => 'required|integer|exists:usuario,id_usuario',
            'nombre_direccion' => 'required|string|max:255',
            'latitud_direccion' => 'required|numeric',
            'longitud_direccion' => 'required|numeric',
        ]);

        $requestMock->id_usuario = $requestData['id_usuario'];
        $requestMock->nombre_direccion = $requestData['nombre_direccion'];
        $requestMock->latitud_direccion = $requestData['latitud_direccion'];
        $requestMock->longitud_direccion = $requestData['longitud_direccion'];
        $direccionMock = Mockery::mock('overload:' . UsuarioDireccion::class);
        $direccionMock->shouldReceive('create')->once()->with([
            'fk_id_usuario' => 1,
            'nombre_direccion' => 'Mi Casa',
            'latitud_direccion' => -36.8277,
            'longitud_direccion' => -73.0506,
            'direccion_verificada' => true
        ])->andReturn((object) $requestData);

        $controller = new UsuarioDireccionController();
        $response = $controller->guardar_usuario_direccion($requestMock);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'status' => 'success',
            'message' => 'Dirección de usuario guardada exitosamente.',
            'data' => (object) $requestData,
        ]), $response->getContent());
    }
    
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_guardar_usuario_direccion_falla_por_validacion()
    {
        $requestData = [
            'id_usuario' => 1,
            'latitud_direccion' => -36.8277,
            'longitud_direccion' => -73.0506,
        ];

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('validate')->once()->andThrow(
            ValidationException::withMessages(['nombre_direccion' => 'El campo nombre de la dirección es obligatorio.'])
        );

        $controller = new UsuarioDireccionController();
        
        $this->expectException(ValidationException::class);
        $controller->guardar_usuario_direccion($requestMock);
    }
    
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
