<?php

namespace App\Repository\Impl;

use App\Repository\ContractRepository;
use Core\Repository\AbstractRepository;

class ContractRepositoryImpl extends AbstractRepository implements ContractRepository
{
    // todo refactor, create (intarfece, classes):
    // FilterInterface, ComparisonFilter BetweenFilter, LikeFilter
    // SortInterface, SortBy
    // HavingInterface, Having
    // OrderIntarface, OrderBy
    public function findForAction5($id, $minAmount = null, $orderBy = [])
    {
        $query = "SELECT * FROM $this->table WHERE id = :id";
        $params['id'] = $id;

        if ($minAmount !== null) {
            $query .= " AND amount > :minAmount";
            $params['minAmount'] = $minAmount;
        }
        if (!empty($orderBy)) {
            $query .= " ORDER BY";
        }
        foreach ($orderBy as $column => $orderWay) {
            $query .= " $column $orderWay";
        }

        $results = $this->query($query, $params);

        $collection = [];
        foreach ($results as $result) {
            $collection[] = $this->objectMapper->mapFromArray($this->modelClass, $result);
        }

        return $collection;
    }

    public function findAll()
    {
        return $this->findBy([], 'id', 'ASC', 1, PHP_INT_MAX);
    }
}