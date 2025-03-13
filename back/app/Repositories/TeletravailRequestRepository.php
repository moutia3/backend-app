<?php

namespace App\Repositories;

use App\Models\TeletravailRequest;

class TeletravailRequestRepository implements TeletravailRequestRepositoryInterface
{
    public function create(array $data)
    {
        return TeletravailRequest::create($data);
    }
    
}