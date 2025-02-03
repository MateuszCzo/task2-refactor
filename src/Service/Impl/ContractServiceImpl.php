<?php

namespace App\Service\Impl;

use App\Repository\ContractRepository;
use App\Response\ContractResponse;
use App\Service\ContractService;
use Core\Utils\ObjectMapper;
use Psr\Http\Message\ServerRequestInterface;

class ContractServiceImpl implements ContractService
{
    private ContractRepository $contractRepository;
    private ObjectMapper $objectmapper;

    public function __construct(ContractRepository $contractRepository, ObjectMapper $objectmapper)
    {
        $this->contractRepository = $contractRepository;
        $this->objectmapper = $objectmapper;
    }

    public function getContracts(ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();
        $id = isset($queryParams['i']) ? (int)$queryParams['i'] : null;
        $action = (int)($queryParams['akcja'] ?? 0);
        $sort = (int)($queryParams['sort'] ?? 0);
        $amount = 10;

        $sortBy = [];

        switch ($sort) {
            case 1:
                $sortBy['business_name'] = 'ASC';
                $sortBy['nip'] = 'DESC';
                break;
            case 2:
                $sortBy['amount'] = 'ASC';
                break;
        }

        $contracts = [];

        switch ($action) {
            case 5:
                $contracts = $this->contractRepository->findForAction5($id, $amount, $sortBy);
                break;
            default:
                $contracts = $this->contractRepository->findAll();
        }

        return [
            'contracts' => array_map(function($contact) {
                    return $this->objectmapper->map(ContractResponse::class, $contact);
                }, $contracts),
            'action' => $action
        ];
    }
}
