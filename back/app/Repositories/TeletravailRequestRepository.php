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

    public function findByUser($userId, $page = 1, $limit = 6)
{
    return TeletravailRequest::where('user_id', $userId)
        ->paginate($limit, ['*'], 'page', $page);
}
}