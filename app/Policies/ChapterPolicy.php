<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChapterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view chapter listings
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Chapter $chapter): bool
    {
        // Anyone can view published chapters of published manga
        if ($chapter->is_published && $chapter->manga->is_published) {
            return true;
        }

        // Only admin, mod, or translator can view unpublished chapters
        return $user && in_array($user->role, ['admin', 'mod', 'translator']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin, mod, or translator can create chapters
        return in_array($user->role, ['admin', 'mod', 'translator']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Chapter $chapter): bool
    {
        // Only admin, mod, or translator can update chapters
        return in_array($user->role, ['admin', 'mod', 'translator']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Chapter $chapter): bool
    {
        // Only admin or mod can delete chapters
        return in_array($user->role, ['admin', 'mod']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Chapter $chapter): bool
    {
        // Only admin can restore chapters
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Chapter $chapter): bool
    {
        // Only admin can permanently delete chapters
        return $user->role === 'admin';
    }
}
