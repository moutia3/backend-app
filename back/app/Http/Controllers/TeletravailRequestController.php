<?php

namespace App\Http\Controllers;
use App\Models\GlobalSetting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\TeletravailRequestRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\TeletravailRequest; 
use App\Models\Notification;

class TeletravailRequestController extends Controller
{
    protected $repository;

    public function __construct(TeletravailRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function showRequestsByUser($userId)
    {
        $teletravailRequests = $this->repository->findByUser($userId);
        
        if ($teletravailRequests->isEmpty()) {
            return response()->json(['message' => 'Aucune demande trouvée pour cet utilisateur'], 404);
        }
        
        return response()->json(['requests' => $teletravailRequests], 200);
    }
     
    
    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,approved,rejected'
    ]);

    $teletravailRequest = $this->repository->find($id);

    if (!$teletravailRequest) {
        return response()->json(['message' => 'Demande non trouvée'], 404);
    }

    $user = $teletravailRequest->user;

    if (auth()->user()->hasRole('manager') && $teletravailRequest->user_id == auth()->id()) {
        return response()->json(['message' => 'Vous ne pouvez pas approuver votre propre demande'], 403);
    }

    if (auth()->user()->hasRole('manager') && $user->hasRole('manager')) {
        return response()->json(['message' => 'Vous ne pouvez pas approuver les demandes d\'autres managers'], 403);
    }

    $oldStatus = $teletravailRequest->status;
    $updatedRequest = $this->repository->updateStatus($id, $request->status);

    if ($oldStatus !== $request->status) {
        Notification::create([
            'user_id' => $teletravailRequest->user_id,
            'message' => "Votre demande de télétravail pour le {$teletravailRequest->date} a été {$request->status}.",
            'type' => $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'error' : 'info'),
            'data' => ['request_id' => $teletravailRequest->id]
        ]);

        $requestDetails = [
            'user_name' => $user->name,
            'date' => $teletravailRequest->date,
            'status' => $request->status
        ];
        $this->repository->sendStatusEmail($user->email, $requestDetails);
    }

    return response()->json([
        'message' => 'Statut mis à jour avec succès',
        'request' => $updatedRequest
    ], 200);
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
        'status' => 'pending', 
        'department_id' => Auth::user()->department_id
    ];

    $teletravailRequest = TeletravailRequest::create($data);

    return response()->json([
        'message' => 'Demande soumise avec succès. En attente d\'approbation.',
        'request' => $teletravailRequest
    ], 201);
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
        $limit = $request->input('limit', 6);
    
        $teletravailRequests = $this->repository->findByUser($userId, $page, $limit);
    
        if ($teletravailRequests->isEmpty()) {
            return response()->json(['message' => 'Aucune demande trouvée'], 404);
        }
    
        return response()->json(['requests' => $teletravailRequests], 200);
    }

    public function showRequest($id)
    {
        $teletravailRequest = $this->repository->find($id);

        if (!$teletravailRequest) {
            return response()->json(['message' => 'Demande non trouvée'], 404);
        }

        return response()->json(['request' => $teletravailRequest], 200);
    }

    public function index()
    {
        $requests = TeletravailRequest::with('user.department')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return response()->json([
            'data' => $requests
        ]);
    }
}