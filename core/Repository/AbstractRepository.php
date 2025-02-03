<?php

namespace Core\Repository;

use Core\Utils\ObjectMapper;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use ReflectionClass;

abstract class AbstractRepository
{
    protected PDO $db;
    protected string $table;
    protected string $modelClass;
    protected ObjectMapper $objectMapper;

    public function __construct(PDO $db, string $modelClass, ObjectMapper $objectMapper)
    {
        $this->db = $db;
        $this->modelClass = $modelClass;

        $this->table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', basename(str_replace('\\', '/', $modelClass))));
        $this->objectMapper = $objectMapper;
    }

    public function findBy(array $criteria = [], $orderBy = null, $orderWay = 'ASC', $page = 1, $limit = 10): array
    {
        $query = 'SELECT * FROM ' . $this->table;
        $params = [];

        if (!empty($criteria)) {
            $conditions = $this->createConditions($criteria, $params);
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        if (!empty($orderBy)) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $orderWay;
        }

        $offset = ($page - 1) * $limit;
        $query .= ' LIMIT ' . $limit;
        if ($offset != 0) {
            $query .= ' OFFSET ' . $offset;
        }

        $results = $this->query($query, $params);

        $collection = [];
        foreach ($results as $result) {
            $collection[] = $this->objectMapper->mapFromArray($this->modelClass, $result);
        }

        return $collection;
    }

    // todo refactor, create (intarfece, classes):
    // FilterInterface, ComparisonFilter BetweenFilter, LikeFilter
    // SortInterface, SortBy
    // HavingInterface, Having
    // OrderIntarface, OrderBy
    protected function createConditions($criteria, &$params)
    {
        $conditions = [];

        foreach ($criteria as $field => $value) {
            if (isset($value['like'])) {
                $conditions[] = $field . ' LIKE :' . $field;
                $params[':' . $field] = '%' . $value['like'] . '%';
            } elseif (isset($value['between'])) {
                if (count($value['between']) != 2) {
                    throw new InvalidArgumentException('Invalid between condition for field: ' . $field);
                }
                $conditions[] = $field . ' BETWEEN :' . $field . '_start AND :' . $field . '_end';
                $params[':' . $field . '_start'] = $value['between'][0];
                $params[':' . $field . '_end'] = $value['between'][1];
            } elseif (isset($value['gt'])) {
                $conditions[] = $field . ' > :' . $field . '_gt';
                $params[':' . $field . '_gt'] = $value['gt'];
            } elseif (isset($value['lt'])) {
                $conditions[] = $field . ' < :' . $field . '_lt';
                $params[':' . $field . '_lt'] = $value['lt'];
            } elseif (isset($value['gte'])) {
                $conditions[] = $field . ' >= :' . $field . '_gte';
                $params[':' . $field . '_gte'] = $value['gte'];
            } elseif (isset($value['lte'])) {
                $conditions[] = $field . ' <= :' . $field . '_lte';
                $params[':' . $field . '_lte'] = $value['lte'];
            } else {
                $conditions[] = $field . ' = :' . $field;
                $params[':' . $field] = $value;
            }
        }

        return $conditions;
    }

    public function count(array $criteria = []): int
    {
        $query = 'SELECT count(*) FROM ' . $this->table;
        $params = [];

        if (!empty($criteria)) {
            $conditions = $this->createConditions($criteria, $params);
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        return $this->query($query, $params)[0]['count(*)'];
    }

    public function findOneBy(array $criteria = [])
    {
        $response = $this->findBy($criteria, null, null, 1, 1);

        return !empty($response) ? $response[0] : false;
    }

    public function save(object $model)
    {
        $params = $this->extractParams($model);
        $columns = implode(', ', array_keys($params));
        $placeholders = implode(', ', array_map(function($col) {return ':' . $col;}, array_keys($params)));

        $sql = 'INSERT INTO ' . $this->table . ' ( ' . $columns . ') VALUES (' . $placeholders . ')';
        $this->execute($sql, $params);
    }

    public function update(object $model, string $primaryKey = 'id')
    {
        $params = $this->extractParams($model);
        $id = $data[$primaryKey] ?? null;
        $params['id'] = $id;

        if (!$id) {
            throw new InvalidArgumentException('ID is required for updating a record.');
        }

        unset($params[$primaryKey]);
        $setFields = implode(', ', array_map(function($col) {return ':' . $col;}, array_keys($params)));

        $sql = 'UPDATE ' . $this->table . ' SET ' . $setFields . ' WHERE ' . $primaryKey . ' = :id';
        $this->execute($sql, $params);
    }

    public function delete(int $id, string $primaryKey = 'id')
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $primaryKey . ' = :id';
        $this->execute($sql, ['id' => $id]);
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function execute(string $sql, array $params): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function extractParams(object $model): array
    {
        $reflection = new ReflectionClass($model);
        $properties = $reflection->getProperties();
        $data = [];

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($model);
        }

        return $data;
    }
}