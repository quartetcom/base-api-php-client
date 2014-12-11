<?php
namespace Quartet\BaseApi;

use Quartet\BaseApi\Entity\TestEntity;

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    public function setUp()
    {
        $this->em = new EntityManager;
    }

    public function test_getEntity()
    {
        $data = [
            'scalar' => 1,
            'array' => [1, 2, 3],
            'test_entity' => [
                'scalar' => 1,
            ],
            'test_entities' => [
                [
                    'scalar' => 1,
                ],
                [
                    'scalar' => 2,
                ],
                [
                    'scalar' => 3,
                ],
            ],
        ];

        $entity = $this->em->getEntity('TestEntity', $data);
        /** @var \Quartet\BaseApi\Entity\TestEntity $entity */

        $this->assertInstanceOf('\Quartet\BaseApi\Entity\TestEntity', $entity);
        $this->assertEquals(1, $entity->scalar);
        $this->assertEquals([1, 2, 3], $entity->array);
        $this->assertInstanceOf('\Quartet\BaseApi\Entity\TestEntity', $entity->test_entity);
        $this->assertEquals(1, $entity->test_entity->scalar);
        $this->assertInstanceOf('\Quartet\BaseApi\Entity\TestEntity', $entity->test_entities[0]);
        $this->assertEquals(1, $entity->test_entities[0]->scalar);
        $this->assertInstanceOf('\Quartet\BaseApi\Entity\TestEntity', $entity->test_entities[1]);
        $this->assertEquals(2, $entity->test_entities[1]->scalar);
        $this->assertInstanceOf('\Quartet\BaseApi\Entity\TestEntity', $entity->test_entities[2]);
        $this->assertEquals(3, $entity->test_entities[2]->scalar);

        $this->assertNull($entity->test_entity->array);
        $this->assertNull($entity->test_entity->test_entity);
        $this->assertNull($entity->test_entity->test_entities);
        $this->assertArrayNotHasKey(3, $entity->test_entities);
    }

    public function test_getEntity_with_undefined_entity_name()
    {
        $this->setExpectedException('\Quartet\BaseApi\Exception\LogicException');
        $this->em->getEntity('Undefined', []);
    }

    public function test_getEntity_with_not_entity_object_name()
    {
        $this->setExpectedException('\Quartet\BaseApi\Exception\LogicException');
        $this->em->getEntity('NotEntity', []);
    }

    public function test_getArray_with_default()
    {
        $childEntity = new TestEntity('child');
        $entity = new TestEntity(
            1,
            [1, 2, 3],
            $childEntity,
            [
                $childEntity,
                $childEntity,
                $childEntity,
            ]
        );

        $array = [
            'scalar' => 1,
            'array' => [1, 2, 3],
            'test_entity' => [
                'scalar' => 'child',
            ],
            'test_entities' => [
                [
                    'scalar' => 'child',
                ],
                [
                    'scalar' => 'child',
                ],
                [
                    'scalar' => 'child',
                ],
            ],
        ];

        $result = $this->em->getArray($entity);

        $this->assertEquals($array, $result);
    }

    public function test_getArray_with_not_filtering()
    {
        $childEntity = new TestEntity('child');
        $entity = new TestEntity(
            1,
            [1, 2, 3],
            $childEntity,
            [
                $childEntity,
                $childEntity,
                $childEntity,
            ]
        );

        $array = [
            'scalar' => 1,
            'array' => [1, 2, 3],
            'test_entity' => [
                'scalar' => 'child',
                'array' => null,
                'test_entity' => null,
                'test_entities' => null,
            ],
            'test_entities' => [
                [
                    'scalar' => 'child',
                    'array' => null,
                    'test_entity' => null,
                    'test_entities' => null,
                ],
                [
                    'scalar' => 'child',
                    'array' => null,
                    'test_entity' => null,
                    'test_entities' => null,
                ],
                [
                    'scalar' => 'child',
                    'array' => null,
                    'test_entity' => null,
                    'test_entities' => null,
                ],
            ],
        ];

        $result = $this->em->getArray($entity, false);

        $this->assertEquals($array, $result);
    }

    public function test_getFlatArray()
    {
        $childEntity = new TestEntity('child');
        $entity = new TestEntity(
            1,
            null,
            null,
            [
                $childEntity,
                $childEntity,
                $childEntity,
            ]
        );

        $flatArray = [
            'scalar' => 1,
            'scalar[0]' => 'child',
            'scalar[1]' => 'child',
            'scalar[2]' => 'child',
        ];

        $result = $this->em->getFlatArray($entity);

        $this->assertEquals($flatArray, $result);
    }
}

/**
 * Test entity.
 */
namespace Quartet\BaseApi\Entity;

class TestEntity implements EntityInterface
{
    public $scalar;
    public $array;
    public $test_entity;
    public $test_entities;

    public function __construct($scalar = null, array $array = null, EntityInterface $test_entity = null, array $test_entities = null)
    {
        $this->scalar = $scalar;
        $this->array = $array;
        $this->test_entity = $test_entity;
        $this->test_entities = $test_entities;
    }
}

class NotEntity
{
}
