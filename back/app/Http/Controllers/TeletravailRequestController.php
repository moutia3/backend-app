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
}