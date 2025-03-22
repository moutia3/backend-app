<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthRepository implements AuthRepositoryInterface
{
    public function sendWelcomeEmail($email, $password)
    {
        Mail::raw("Votre compte a été créé avec succès. Voici vos informations de connexion :\n\nEmail: $email\nMot de passe: $password", function ($message) use ($email) {
            $message->to($email)
                    ->subject('Bienvenue sur notre application');
        });
    }

    public function sendResetLinkEmail(array $data)
    {
        $status = Password::sendResetLink($data);

        return $status === Password::RESET_LINK_SENT
            ? ['message' => __($status), 'status' => 200]
            : ['message' => __($status), 'status' => 400];
    }

    public function resetPassword(array $data)
    {
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );
    
        return $status === Password::PASSWORD_RESET
            ? ['message' => __($status), 'status' => 200]
            : ['message' => __($status), 'status' => 400];
    }

    public function addUser(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);
        $this->sendWelcomeEmail($user->email, $data['password']);

        return $user;
    }

    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return $token;
        }

        return null;
    }

    public function logout($user)
    {
        $user->tokens()->delete();
    }

    public function getUser($user)
    {
        return $user;
    }

    public function getAllUsers($page = 1, $limit = 6)
{
    return User::with('roles')->paginate($limit, ['*'], 'page', $page);
}
    public function getUserById($id)
{
    return User::find($id);
}
public function updateUser($id, array $data)
{
    $user = User::find($id);
    if ($user) {
        $user->update($data);
        return $user;
    }
    return null;
}
public function deleteUser($id)
{
    $user = User::find($id);
    if ($user) {
        $user->delete();
        return true;
    }
    return false;
}
}