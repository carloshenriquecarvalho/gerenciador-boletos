<?php

namespace App\model;

class Company
{
    private string  $companyName;
    private ?int $companyId;
    private ?int $userId;

    public function getCompanyName(): string
    {
        return $this->companyName;
    }
    public function getCompanyId(): int
    {
        return $this->companyId;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }

}