<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Generate income report.
     */
    public function income()
    {
        //
    }

    /**
     * Generate expense report.
     */
    public function expense()
    {
        //
    }

    /**
     * Generate financial summary report.
     */
    public function summary()
    {
        //
    }
}
