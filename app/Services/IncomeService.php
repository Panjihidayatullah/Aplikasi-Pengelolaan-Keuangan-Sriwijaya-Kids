<?php

namespace App\Services;

use App\Repositories\IncomeRepository;

class IncomeService
{
    protected $incomeRepository;

    public function __construct(IncomeRepository $incomeRepository)
    {
        $this->incomeRepository = $incomeRepository;
    }

    // Business logic methods here
}
