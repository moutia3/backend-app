<?php

namespace App\Repositories;

use App\Models\TeletravailRequest;

class TeletravailRequestRepository implements TeletravailRequestRepositoryInterface
{
    public function create(array $data)
    {
        return TeletravailRequest::create($data);
    }
    
    public function update($id, array $data)
    {
        $teletravailRequest = TeletravailRequest::find($id);

        if (!$teletravailRequest) {
            return null;
        }

        $teletravailRequest->update($data);

        return $teletravailRequest;
    }
public function find($id)
    {
        return TeletravailRequest::find($id);
    }
}