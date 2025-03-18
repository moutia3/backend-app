<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DepartmentRepositoryInterface;

class DepartmentController extends Controller
{
    protected $repository;

    public function __construct(DepartmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $departments = $this->repository->all();
        return response()->json(['departments' => $departments], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department = $this->repository->create($request->all());

        return response()->json(['message' => 'Department created successfully', 'department' => $department], 201);
    }

    public function show($id)
    {
        $department = $this->repository->find($id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json(['department' => $department], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department = $this->repository->update($id, $request->all());

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json(['message' => 'Department updated successfully', 'department' => $department], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json(['message' => 'Department deleted successfully'], 200);
    }
}