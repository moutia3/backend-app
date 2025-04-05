<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\GlobalSettingRepositoryInterface;
use Illuminate\Support\Facades\Validator;

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
}