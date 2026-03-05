<?php

namespace App\Services;

use App\Repositories\ExpenseRepository;

class ExpenseService
{
    protected $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepository)
    {
        $this->expenseRepository = $expenseRepository;
    }

    // Business logic methods here
}
