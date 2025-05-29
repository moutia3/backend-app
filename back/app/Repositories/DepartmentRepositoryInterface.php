<?php

namespace App\Repositories;

interface DepartmentRepositoryInterface
{
    public function all();
    public function create(array $data);
    public function find($id);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($page, $limit);
}