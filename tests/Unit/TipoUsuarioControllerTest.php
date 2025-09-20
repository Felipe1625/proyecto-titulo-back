<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\V1\TipoUsuarioController;
use App\Models\TipoUsuario;
use Illuminate\Http\Request;
use Mockery;

class TipoUsuarioControllerTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_index_devuelve_lista_correcta_de_tipos_de_usuario()
    {
        $tiposUsuarioMock = collect([
            (object)['id_tipo_usuario' => 1, 'nombre_tipo' => 'Administrador'],
            (object)['id_tipo_usuario' => 2, 'nombre_tipo' => 'Usuario'],
        ]);

        $tipoUsuarioMock = Mockery::mock('alias:' . TipoUsuario::class);
        $tipoUsuarioMock->shouldReceive('all')->once()->andReturn($tiposUsuarioMock);
        $controller = new TipoUsuarioController();
        $response = $controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($tiposUsuarioMock), $response->getContent());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_show_devuelve_tipo_de_usuario_existente()
    {
        $tipoUsuarioMock = (object)['id_tipo_usuario' => 1, 'nombre_tipo' => 'Administrador'];
        $tipoUsuarioMocked = Mockery::mock('alias:' . TipoUsuario::class);
        $tipoUsuarioMocked->shouldReceive('find')->once()->with(1)->andReturn($tipoUsuarioMock);
        $controller = new TipoUsuarioController();
        $response = $controller->show(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($tipoUsuarioMock), $response->getContent());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_show_devuelve_404_si_el_tipo_de_usuario_no_existe()
    {
        $tipoUsuarioMocked = Mockery::mock('alias:' . TipoUsuario::class);
        $tipoUsuarioMocked->shouldReceive('find')->once()->with(999)->andReturn(null);
        $controller = new TipoUsuarioController();
        $response = $controller->show(999);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(json_encode(['message' => 'Tipo de usuario no encontrado']), $response->getContent());
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}