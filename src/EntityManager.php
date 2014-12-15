<?php
namespace Quartet\BaseApi;

use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Quartet\BaseApi\Entity\EntityInterface;
use Quartet\BaseApi\Exception\LogicException;

class EntityManager
{
    /**
     * @param string $entityName
     * @param array $data
     * @return Entity\EntityInterface
     * @throws Exception\LogicException
     */
    public function getEntity($entityName, array $data = [])
    {
        $class = '';

        $classCandidates = [
            __NAMESPACE__ . '\\Entity\\' . trim($entityName, '\\'),
            __NAMESPACE__ . '\\Entity\\Subset\\' . trim($entityName, '\\'),
        ];

        foreach ($classCandidates as $classCandidate) {
            if (class_exists($classCandidate)) {
                $class = $classCandidate;
                break;
            }
        }

        if (!$class) {
            throw new LogicException("Entity \"{$entityName}\" is undefined.");
        }

        $entity = new $class;

        if (! $entity instanceof EntityInterface) {
            throw new LogicException("Class \"{$class}\" isn't an implement of EntityInterface.");
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {

                $childEntityName = Inflector::singularize((string)Inflector::camelize($key));

                if (Hash::check($value, '{s}')) {
                    // has string key => entity.
                    $value = $this->getEntity($childEntityName, $value);
                } elseif (Hash::check($value, '{n}.{s}')) {
                    // has string key under number key => array of entities.
                    $children = [];
                    foreach ($value as $child) {
                        $children[] = $this->getEntity($childEntityName, $child);
                    }
                    $value = $children;
                } else {
                    // else => array of scalar.
                }
            }

            if (property_exists($entity, $key)) {
                $entity->$key = $value;
            }
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param bool $filter
     * @return array
     */
    public function getArray(EntityInterface $entity, $filter = true)
    {
        $properties = get_object_vars($entity);

        array_walk_recursive($properties, function (&$value) use ($filter) {
            if ($value instanceof EntityInterface) {
                $value = $this->getArray($value, $filter);
            }
        });

        if ($filter) {
            $properties = array_filter($properties);
        }

        return $properties;
    }

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function getFlatArray(EntityInterface $entity)
    {
        $array = $this->getArray($entity, true);

        $flattened = Hash::flatten($array);

        $keys = array_keys($flattened);
        $values = array_values($flattened);

        foreach ($keys as &$key) {
            if (preg_match('/^\w+\.(\d+)\.(\w+)$/', $key, $matches)) {
                $key = sprintf('%s[%s]', $matches[2], $matches[1]);
            }
        }
        unset($key);

        $formatted = array_combine($keys, $values);

        return $formatted;
    }
}
