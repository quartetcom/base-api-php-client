<?php
namespace Quartet\BaseApi;

use Quartet\BaseApi\Entity\EntityInterface;
use Quartet\BaseApi\Exception\LogicException;

class EntityFactory
{
    /**
     * @param string $entityName
     * @param array $properties
     * @return \Quartet\BaseApi\Entity\EntityInterface
     * @throws Exception\LogicException
     */
    public static function get($entityName, array $properties)
    {
        $class = __NAMESPACE__ . '\\Entity\\' . trim($entityName, '\\');

        if (!class_exists($class)) {
            throw new LogicException("Class \"{$class}\" is undefined.");
        }

        $entity = new $class;

        if (! $entity instanceof EntityInterface) {
            throw new LogicException("Class \"{$class}\" isn't an implement of EntityInterface.");
        }

        foreach ($properties as $key => $value) {
            if (property_exists($entity, $key)) {
                if (!is_array($value)) {
                    $entity->$key = $properties[$key];
                }
            }
        }

        return $entity;
    }
}
