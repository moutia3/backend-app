<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\GlobalSettingRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use App\Models\GlobalSetting;
use App\Models\TeletravailRequest;
use App\Models\User;

class GlobalSettingController extends Controller
{
    protected $repository;

    public function __construct(GlobalSettingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $settings = $this->repository->all();
        return response()->json($settings, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|unique:global_settings,date',
            'status' => 'required|in:blocked,limited,available',
            'daily_limit' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting = $this->repository->create($request->all());
        return response()->json(['message' => 'Paramètre ajouté avec succès', 'setting' => $setting], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'sometimes|date|unique:global_settings,date,' . $id,
            'status' => 'sometimes|in:blocked,limited,available',
            'daily_limit' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting = $this->repository->update($id, $request->all());
        if (!$setting) {
            return response()->json(['message' => 'Paramètre non trouvé'], 404);
        }

        return response()->json(['message' => 'Paramètre mis à jour', 'setting' => $setting], 200);
    }

    public function destroy($id)
    {
        if (!$this->repository->delete($id)) {
            return response()->json(['message' => 'Paramètre non trouvé'], 404);
        }

        return response()->json(['message' => 'Paramètre supprimé'], 200);
    }

    public function getSettings(Request $request)
    {
        $settings = $this->repository->all();
        return response()->json($settings, 200);
    }

public function checkAvailability(Request $request)
{
    $date = $request->input('date');
    $setting = GlobalSetting::where('date', $date)->first();
    
    if (!$setting) {
        return response()->json([
            'status' => 'available',
            'message' => 'Aucune restriction pour cette date'
        ]);
    }
    
    if ($setting->status === 'blocked') {
        return response()->json([
            'status' => 'blocked',
            'message' => 'Télétravail non autorisé cette date'
        ]);
    }
    


    if ($setting->status === 'limited') {
        
        $totalEmployees = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['employee', 'manager']);
        })->count();
        
        $absoluteLimit = max(1, ceil($totalEmployees * $setting->daily_limit / 100));
        
        // Compter seulement les demandes APPROUVÉES
        $approvedCount = TeletravailRequest::where('date', $date)
            ->where('status', 'approved')
            ->count();
            
        $remaining = max(0, $absoluteLimit - $approvedCount);
        
        return response()->json([
            'status' => $remaining > 0 ? 'limited' : 'blocked',
            'remaining_slots' => $remaining,
            'daily_limit' => $setting->daily_limit,
            'absolute_limit' => $absoluteLimit,
            'current_count' => $approvedCount, 
            'message' => $remaining > 0 
                ? "Places restantes: $remaining/$absoluteLimit (limite: {$setting->daily_limit}%)" 
                : "Quota atteint pour cette date"
        ]);
    }
    
    return response()->json([
        'status' => 'available',
        'message' => 'Télétravail autorisé'
    ]);
}
}