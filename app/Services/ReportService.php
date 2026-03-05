<?php

namespace App\Services;

use App\Repositories\IncomeRepository;
use App\Repositories\ExpenseRepository;

class ReportService
{
    protected $incomeRepository;
    protected $expenseRepository;

    public function __construct(
        IncomeRepository $incomeRepository,
        ExpenseRepository $expenseRepository
    ) {
        $this->incomeRepository = $incomeRepository;
        $this->expenseRepository = $expenseRepository;
    }

    // Business logic methods here
}
