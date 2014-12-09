<?php
namespace Quartet\BaseApi;

use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Quartet\BaseApi\Entity\EntityInterface;
use Quartet\BaseApi\Exception\LogicException;

class EntityFactory
{
    /**
     * @param string $entityName
     * @param array $data
     * @return Entity\EntityInterface
     * @throws Exception\LogicException
     */
    public static function get($entityName, array $data)
    {
        $class = __NAMESPACE__ . '\\Entity\\' . trim($entityName, '\\');

        if (!class_exists($class)) {
            throw new LogicException("Class \"{$class}\" is undefined.");
        }

        $entity = new $class;

        if (! $entity instanceof EntityInterface) {
            throw new LogicException("Class \"{$class}\" isn't an implement of EntityInterface.");
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $childEntityName = Inflector::singularize(Inflector::camelize($key));

                switch (Hash::dimensions($value)) {
                    case 1: // entity.
                        $value = self::get($childEntityName, $value);
                        break;
                    case 2: // array of entities.
                        $children = [];
                        foreach ($value as $child) {
                            $children[] = self::get($childEntityName, $child);
                        }
                        $value = $children;
                        break;
                    default:
                        break;
                }
            }

            if (property_exists($entity, $key)) {
                $entity->$key = $value;
            }
        }

        return $entity;
    }
}
