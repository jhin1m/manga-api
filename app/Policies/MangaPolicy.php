<?php

namespace App\Policies;

use App\Models\Manga;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MangaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view manga listings
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Manga $manga): bool
    {
        // Anyone can view published manga
        if ($manga->is_published) {
            return true;
        }

        // Only admin, mod, or translator can view unpublished manga
        return $user && in_array($user->role, ['admin', 'mod', 'translator']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin, mod, or translator can create manga
        return in_array($user->role, ['admin', 'mod', 'translator']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Manga $manga): bool
    {
        // Only admin, mod, or translator can update manga
        return in_array($user->role, ['admin', 'mod', 'translator']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Manga $manga): bool
    {
        // Only admin can delete manga
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Manga $manga): bool
    {
        // Only admin can restore manga
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Manga $manga): bool
    {
        // Only admin can permanently delete manga
        return $user->role === 'admin';
    }
}
