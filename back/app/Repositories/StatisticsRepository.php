<?php

namespace App\Repositories;

use App\Models\TeletravailRequest;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class StatisticsRepository implements StatisticsRepositoryInterface
{
    public function getRequestStatistics(): array
    {
        return TeletravailRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getRoleStatistics(): array
    {
        return User::select('roles.name', DB::raw('count(users.id) as count'))
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->pluck('count', 'name')
            ->toArray();
    }

    public function getDepartmentStatistics(): array
    {
        return TeletravailRequest::select('departments.name', DB::raw('count(teletravail_requests.id) as count'))
            ->join('users', 'teletravail_requests.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->groupBy('departments.name')
            ->pluck('count', 'name')
            ->toArray();
    }

    public function getTrendStatistics(): array
    {
        return TeletravailRequest::select(
            DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->month => $item->count];
            })
            ->toArray();
    }
    
}