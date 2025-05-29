<?php

namespace App\Repositories;

interface AuthRepositoryInterface
{
    public function addUser(array $data);
    public function login(array $credentials);
    public function logout($user);
    public function getUser($user);
    public function getAllUsers();
    public function getUserById($id);
    public function sendResetLinkEmail(array $data);
   
    public function resetPassword(array $data);
    public function updateUser($id, array $data);
    public function deleteUser($id); 
    public function sendWelcomeEmail($email, $password);
}