<?php

namespace Baubyte\Tests\Container;

use Baubyte\Container\Container;
use Baubyte\Container\DependencyInjection;
use Baubyte\Database\Model;
use PHPUnit\Framework\TestCase;

class MockModel extends Model {
    public static function find(int|string $id): ?static {
        $model = new self();
        $model->id = 1;

        return $model;
    }
}

interface DependencyInjectionMockInterface {
}

class TestClassImplementesInterface01 implements DependencyInjectionMockInterface {
}

class TestClassImplementesInterface02 implements DependencyInjectionMockInterface {
}

class TestClassNoInterface01 {
}

class TestClassNoInterface02 {
}

class DependencyInjectionTest extends TestCase {
    public function test_resolves_callback_parameters_from_container() {
        $callback = fn (TestClassNoInterface01 $t1, TestClassNoInterface02 $t2) => "test";

        $t1 = new TestClassNoInterface01();
        $t2 = new TestClassNoInterface02();

        Container::singleton(TestClassNoInterface01::class, fn () => $t1);
        Container::singleton(TestClassNoInterface02::class, fn () => $t2);

        $this->assertEquals([$t1, $t2], DependencyInjection::resolveParameters($callback));
    }

    public function test_resolves_callback_parameters_from_route_parameters() {
        $callback = fn (int $id, string $param) => "test";

        $routeParams = ["id" => 1, "param" => "test"];

        $this->assertEquals(
            array_values($routeParams),
            DependencyInjection::resolveParameters($callback, $routeParams)
        );
    }

    /**
     * @depends test_resolves_callback_parameters_from_container
     * @depends test_resolves_callback_parameters_from_route_parameters
     */
    public function test_resolves_callback_parameters_from_container_and_route_parameters() {
        $callback = fn (int $id, string $param, TestClassImplementesInterface01 $t1, TestClassImplementesInterface02 $t2) => "test";

        $t1 = new TestClassImplementesInterface01();
        $t2 = new TestClassImplementesInterface02();


        Container::singleton(TestClassImplementesInterface01::class, fn () => $t1);
        Container::singleton(TestClassImplementesInterface02::class, fn () => $t2);

        $routeParams = ["id" => 1, "param" => "test"];

        $this->assertEquals(
            [1, "test", $t1, $t2],
            DependencyInjection::resolveParameters($callback, $routeParams)
        );
    }

    public function test_resolvesModelFromDatabase() {
        $callback = fn (MockModel $model) => "test";

        $model = MockModel::find(1);

        $routeParams = ["mock_model" => 1];

        $this->assertEquals(
            [$model],
            DependencyInjection::resolveParameters($callback, $routeParams)
        );
    }
}
