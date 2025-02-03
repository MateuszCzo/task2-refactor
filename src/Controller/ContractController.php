<?php

namespace App\Controller;

use App\Constants\ContractConstants;
use App\Service\ContractService;
use Core\Response\ViewResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContractController
{
    public function __construct(private ContractService $contractService)
    {
        
    }

    public function getContacts(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->contractService->getContracts($request);

        return new ViewResponse(200, [], ContractConstants::CONTRACTS_VIEW, $data);
    }
}
