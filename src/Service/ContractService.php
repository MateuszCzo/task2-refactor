<?php

namespace App\Service;

use Psr\Http\Message\ServerRequestInterface;

interface ContractService
{
    public function getContracts(ServerRequestInterface $request);
}
