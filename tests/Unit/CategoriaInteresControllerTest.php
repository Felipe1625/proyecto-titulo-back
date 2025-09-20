<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\V1\CategoriaInteresController;
use App\Models\CategoriaInteres;
use App\Models\Interes;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Mockery;

class CategoriaInteresControllerTest extends TestCase
{
    use WithFaker;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_listado_devuelve_categorias_correctamente()
    {
        $categoriasMock = collect([
            (object)['id_categoria_interes' => 1, 'nombre_categoria' => 'Deportes'],
            (object)['id_categoria_interes' => 2, 'nombre_categoria' => 'Tecnología'],
        ]);

        $categoriaInteresMock = Mockery::mock('alias:'.CategoriaInteres::class);
        $categoriaInteresMock->shouldReceive('all')->once()->andReturn($categoriasMock);
        $controller = new CategoriaInteresController();
        $response = $controller->listado();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($categoriasMock), $response->getContent());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_listado_categoria_intereses_devuelve_intereses_correctamente()
    {
        $interesesMock = collect([
            (object)['id_interes' => 1, 'nombre_interes' => 'Fútbol'],
            (object)['id_interes' => 2, 'nombre_interes' => 'Programación'],
        ]);
        
        $categoriasMock = collect([
            (object)['id_categoria_interes' => 1, 'nombre_categoria' => 'Deportes', 'intereses' => $interesesMock],
        ]);

        $categoriaInteresMock = Mockery::mock('alias:'.CategoriaInteres::class);
        $categoriaInteresMock->shouldReceive('with')->once()->with('intereses')->andReturnSelf();
        $categoriaInteresMock->shouldReceive('get')->once()->andReturn($categoriasMock);
        $controller = new CategoriaInteresController();
        $response = $controller->listado_categoria_intereses();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($categoriasMock), $response->getContent());
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
