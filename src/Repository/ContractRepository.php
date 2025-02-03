<?php

namespace App\Repository;

interface ContractRepository
{
    public function findForAction5($id, $minAmount = null, $orderBy = []);

    public function findAll();
}
