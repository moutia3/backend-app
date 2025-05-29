<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\StatisticsRepositoryInterface;

class StatisticsController extends Controller
{
    protected $statisticsRepository;

    public function __construct(StatisticsRepositoryInterface $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function getStatistics(Request $request)
    {
        $requestStats = $this->statisticsRepository->getRequestStatistics();
        $roleStats = $this->statisticsRepository->getRoleStatistics();
        $departmentStats = $this->statisticsRepository->getDepartmentStatistics();
        $trendStats = $this->statisticsRepository->getTrendStatistics();

        return response()->json([
            'request_stats' => $requestStats,
            'role_stats' => $roleStats,
            'department_stats' => $departmentStats,
            'trend_stats' => $trendStats,
        ], 200);
    }
}