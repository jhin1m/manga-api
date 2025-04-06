<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admin and mod can view user listings
        return in_array($user->role, ['admin', 'mod']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Admin and mod can view any user
        return in_array($user->role, ['admin', 'mod']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create users (besides registration)
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Admin can update any user
        if ($user->role === 'admin') {
            return true;
        }

        // Mod can update regular users but not other mods or admins
        if ($user->role === 'mod' && !in_array($model->role, ['admin', 'mod'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Users can delete their own account
        if ($user->id === $model->id) {
            return true;
        }

        // Admin can delete any user except other admins
        if ($user->role === 'admin' && $model->role !== 'admin') {
            return true;
        }

        // Mod can delete regular users but not other mods or admins
        if ($user->role === 'mod' && !in_array($model->role, ['admin', 'mod'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Only admin can restore users
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Only admin can permanently delete users
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can change role of the model.
     */
    public function changeRole(User $user, User $model): bool
    {
        // Only admin can change user roles
        return $user->role === 'admin';
    }
}
