<?php

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return (bool)$user->is_admin;
    }

    public function update(User $user): bool
    {
        return (bool)$user->is_admin;
    }

    public function delete(User $user): bool
    {
        return (bool)$user->is_admin;
    }
}
