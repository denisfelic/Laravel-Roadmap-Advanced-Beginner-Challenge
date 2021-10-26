<?php

namespace App\Services;

use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;


class UserService
{
    /**
     * Delete user and return his data
     *
     * @return array
     */
    public function deleteUser(User $user): array
    {
        $userData = $user->toArray();
        if ($user->role == 'admin') {
            throw new HttpException(403);
        }
        $user->delete();
        return $userData;
    }


    public function changeRole(User $user, ?string $role): void
    {
        if (is_null($role)) {
            $role = "";
        }

        if ($user->role == 'admin') {
            throw new HttpException(403);
        }
        $user->role = $role;
        $user->save();
    }
}
