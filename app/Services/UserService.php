<?php
namespace App\Services;
use App\Models\User;


class UserService
{
    public static function formatedUserData($userData): array
    {
        return [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
        ];
    }

}