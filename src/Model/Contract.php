<?php

namespace App\Model;

class Contract
{
    private $id;
    private $business_name;
    private $nip;
    private $amount;

    public function __construct($id, $business_name, $nip, $amount)
    {
        $this->id = $id;
        $this->business_name = $business_name;
        $this->nip = $nip;
        $this->amount = $amount;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBusinessName()
    {
        return $this->business_name;
    }

    public function getNip()
    {
        return $this->nip;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}
