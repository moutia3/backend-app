<?php

namespace App\Repositories;

interface StatisticsRepositoryInterface
{
    public function getRequestStatistics(): array;
    public function getRoleStatistics(): array;
    public function getDepartmentStatistics(): array;
    public function getTrendStatistics(): array;
    
}