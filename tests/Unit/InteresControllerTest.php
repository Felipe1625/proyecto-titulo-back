<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\V1\InteresController;
use App\Models\Interes;
use App\Models\UsuarioInteres;
use Illuminate\Http\Request;
use Mockery;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class InteresControllerTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_listado_intereses_exitosamente()
    {
        // 1. Datos de prueba
        $interesesData = [
            (object)['id_interes' => 1, 'nombre_interes' => 'Deportes'],
            (object)['id_interes' => 2, 'nombre_interes' => 'Música'],
        ];
        
        // 2. Mockear el modelo Interes
        $interesMock = Mockery::mock('overload:' . Interes::class);
        $interesMock->shouldReceive('with->get')->once()->andReturn(collect($interesesData));

        // 3. Instanciar el controlador y llamar al método
        $controller = new InteresController();
        $response = $controller->listado();

        // 4. Afirmaciones
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($interesesData), $response->getContent());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_interes_por_categoria_exitosamente()
    {
        // 1. Datos de prueba
        $interesesData = [
            (object)['id_interes' => 3, 'nombre_interes' => 'Fútbol'],
            (object)['id_interes' => 4, 'nombre_interes' => 'Baloncesto'],
        ];
        
        // 2. Mockear el modelo Interes y su consulta encadenada
        $interesMock = Mockery::mock('overload:' . Interes::class);
        $interesMock->shouldReceive('where->with->get')->once()->andReturn(collect($interesesData));

        // 3. Instanciar el controlador y llamar al método
        $controller = new InteresController();
        $response = $controller->interes_por_categoria(1);

        // 4. Afirmaciones
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($interesesData), $response->getContent());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_guardar_usuario_interes_exitosamente()
    {
        // 1. Datos de prueba
        $requestData = [
            'id_usuario' => 1,
            'interes' => [1, 2, 3, 4],
        ];
        $interesesExistentes = [1, 2];
        $interesesAInsertar = [3, 4];

        // 2. Mockear la solicitud HTTP
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('validate')->once()->with([
            'id_usuario' => 'required|integer',
            'interes' => 'required|array',
            'interes.*' => 'integer'
        ]);
        $requestMock->shouldReceive('input')->with('id_usuario')->andReturn($requestData['id_usuario']);
        $requestMock->shouldReceive('input')->with('interes')->andReturn($requestData['interes']);

        // 3. Mockear el modelo UsuarioInteres y sus métodos estáticos
        $usuarioInteresMock = Mockery::mock('overload:' . UsuarioInteres::class);
        $usuarioInteresMock->shouldReceive('where->pluck')->once()->andReturn(collect($interesesExistentes));
        $usuarioInteresMock->shouldReceive('insert')->once()->with(Mockery::on(function ($argument) use ($interesesAInsertar) {
            return count($argument) == count($interesesAInsertar) && 
                   $argument[0]['id_interes'] == $interesesAInsertar[0] &&
                   $argument[1]['id_interes'] == $interesesAInsertar[1];
        }))->andReturn(true);

        // 4. Mockear la fachada de la base de datos
        $dbMock = Mockery::mock('overload:Illuminate\Support\Facades\DB');
        $dbMock->shouldReceive('beginTransaction')->once();
        $dbMock->shouldReceive('commit')->once();
        $dbMock->shouldReceive('rollBack')->never();

        // 5. Instanciar el controlador y llamar al método
        $controller = new InteresController();
        $response = $controller->guardar_usuario_interes($requestMock);

        // 6. Afirmaciones
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['message' => 'intereses de usuario guardados correctamente.']), $response->getContent());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_guardar_usuario_interes_con_error_rollBack()
    {
        // 1. Mockear la solicitud HTTP
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('validate')->once()->with([
            'id_usuario' => 'required|integer',
            'interes' => 'required|array',
            'interes.*' => 'integer'
        ]);
        $requestMock->shouldReceive('input')->with('id_usuario')->andReturn(1);
        $requestMock->shouldReceive('input')->with('interes')->andReturn([1, 2]);

        // 2. Mockear el modelo UsuarioInteres para lanzar una excepción
        $usuarioInteresMock = Mockery::mock('overload:' . UsuarioInteres::class);
        $usuarioInteresMock->shouldReceive('where->pluck')->once()->andThrow(new \Exception('Error de prueba'));
        
        // 3. Mockear la fachada de la base de datos para verificar el rollback
        $dbMock = Mockery::mock('overload:Illuminate\Support\Facades\DB');
        $dbMock->shouldReceive('beginTransaction')->once();
        $dbMock->shouldReceive('commit')->never();
        $dbMock->shouldReceive('rollBack')->once();

        // 4. Instanciar el controlador y llamar al método
        $controller = new InteresController();
        $response = $controller->guardar_usuario_interes($requestMock);

        // 5. Afirmaciones
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString('Error al guardar los intereses.', $response->getContent());
    }
    
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_listar_usuario_interes_exitosamente()
    {
        // 1. Datos de prueba
        $interesesData = [
            (object)['id_interes' => 1, 'nombre_interes' => 'Deportes'],
            (object)['id_interes' => 2, 'nombre_interes' => 'Música'],
        ];

        // 2. Mockear el modelo UsuarioInteres y sus métodos encadenados
        $usuarioInteresMock = Mockery::mock('overload:' . UsuarioInteres::class);
        $usuarioInteresMock->shouldReceive('where->join->select->get')->once()->andReturn(collect($interesesData));

        // 3. Instanciar el controlador y llamar al método
        $controller = new InteresController();
        $response = $controller->listar_usuario_interes(1);

        // 4. Afirmaciones
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($interesesData), $response->getContent());
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
