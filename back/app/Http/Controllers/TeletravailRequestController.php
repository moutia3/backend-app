<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TeletravailRequestRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class TeletravailRequestController extends Controller
{
    protected $repository;

    public function __construct(TeletravailRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'date' => $request->date,
            'reason' => $request->reason,
        ];

        $teletravailRequest = $this->repository->create($data);

        return response()->json(['message' => 'Demande soumise avec succès', 'request' => $teletravailRequest], 201);
    }

    public function updateRequest(Request $request, $id)
    {
        $request->validate([
            'date' => 'sometimes|date',
            'reason' => 'sometimes|string|max:255',
        ]);

        $data = $request->only(['date', 'reason']);

        $teletravailRequest = $this->repository->update($id, $data);

        if (!$teletravailRequest) {
            return response()->json(['message' => 'Demande non trouvée'], 404);
        }

        return response()->json(['message' => 'Demande mise à jour avec succès', 'request' => $teletravailRequest], 200);
    }
    public function showRequests(Request $request)
    {
        $userId = Auth::id();
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 6); // Default limit
    
        $teletravailRequests = $this->repository->findByUser($userId, $page, $limit);
    
        if ($teletravailRequests->isEmpty()) {
            return response()->json(['message' => 'Aucune demande trouvée'], 404);
        }
    
        return response()->json(['requests' => $teletravailRequests], 200);
    }
}