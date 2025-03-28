<?php

namespace App\Repositories;

interface TeletravailRequestRepositoryInterface
{
    public function create(array $data);
    public function update($id, array $data);
    public function find($id);
    public function findByUser($userId, $page = 1, $limit = 6);
}