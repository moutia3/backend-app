<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AuthRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = $this->authRepository->sendResetLinkEmail($request->only('email'));

        return response()->json(['message' => $response['message']], $response['status']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $response = $this->authRepository->resetPassword($request->only(
            'email', 'password', 'password_confirmation', 'token'
        ));
    
        return response()->json(['message' => $response['message']], $response['status']);
    }

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,manager,employee',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->authRepository->addUser($request->all());

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $token = $this->authRepository->login($request->only('email', 'password'));

        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $token], 200);
    }

    public function logout(Request $request)
    {
        $this->authRepository->logout($request->user());

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function user(Request $request)
    {
        $user = $this->authRepository->getUser($request->user());
    
        return response()->json($user);
    }

    public function getAllUsers(Request $request)
{
    $page = $request->input('page', 1);
    $limit = 6;

    $users = $this->authRepository->getAllUsers($page, $limit);
    return response()->json($users);
}
public function getUserById(Request $request, $id)
{
    $user = $this->authRepository->getUserById($id);
    
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json($user);
}

    public function updateUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:admin,manager,employee',
            'password' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = $this->authRepository->getUserById($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update($request->only('name', 'email'));

        if ($request->has('role')) {
            $user->syncRoles([$request->input('role')]);
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

public function deleteUser($id)
{

    if (!auth()->user()->hasRole('admin')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

  
    $user = $this->authRepository->getUserById($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->delete();

    return response()->json(['message' => 'User deleted successfully'], 200);
}
public function updateProfile(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|string',
        'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
        'password' => 'sometimes|string|min:8', 
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $request->only('name', 'email');
    if ($request->has('password')) {
        $data['password'] = Hash::make($request->input('password'));
    }

    $user = $this->authRepository->updateUser(auth()->id(), $data);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
}
public function getProfile(Request $request)
    {
        $user = $this->authRepository->getUserById(auth()->id());
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function deleteProfile(Request $request)
{
    if (!auth()->user()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $deleted = $this->authRepository->deleteUser(auth()->id());

    if (!$deleted) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json(['message' => 'User deleted successfully'], 200);
}

public function getUserRoles(Request $request)
{
    // Get the authenticated user
    $user = Auth::user();

    // Return the user's roles
    return response()->json($user->getRoleNames(), 200);
}


}