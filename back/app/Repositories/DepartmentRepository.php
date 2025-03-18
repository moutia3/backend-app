<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function all()
    {
        return Department::all();
    }

    public function create(array $data)
    {
        return Department::create($data);
    }

    public function find($id)
    {
        return Department::find($id);
    }

    public function update($id, array $data)
    {
        $department = Department::find($id);

        if (!$department) {
            return null;
        }

        $department->update($data);

        return $department;
    }

    public function delete($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return false;
        }

        return $department->delete();
    }
}