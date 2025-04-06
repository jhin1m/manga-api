<?php

namespace Infrastructure\Repositories;

use App\Models\User as EloquentUser;
use Domain\User\Models\User;
use Domain\User\Repositories\UserRepositoryInterface;

/**
 * Eloquent implementation of UserRepositoryInterface
 */
class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * Convert Eloquent model to Domain model
     */
    private function toDomainModel(EloquentUser $eloquentUser): User
    {
        return new User(
            id: $eloquentUser->id,
            username: $eloquentUser->username,
            email: $eloquentUser->email,
            password: $eloquentUser->password,
            role: $eloquentUser->role,
            avatar: $eloquentUser->avatar,
            bio: $eloquentUser->bio,
            rememberToken: $eloquentUser->remember_token,
            createdAt: $eloquentUser->created_at,
            updatedAt: $eloquentUser->updated_at,
            deletedAt: $eloquentUser->deleted_at
        );
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User
    {
        $eloquentUser = EloquentUser::find($id);
        
        if (!$eloquentUser) {
            return null;
        }
        
        return $this->toDomainModel($eloquentUser);
    }
    
    /**
     * Find user by username
     */
    public function findByUsername(string $username): ?User
    {
        $eloquentUser = EloquentUser::where('username', $username)->first();
        
        if (!$eloquentUser) {
            return null;
        }
        
        return $this->toDomainModel($eloquentUser);
    }
    
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        $eloquentUser = EloquentUser::where('email', $email)->first();
        
        if (!$eloquentUser) {
            return null;
        }
        
        return $this->toDomainModel($eloquentUser);
    }
    
    /**
     * Get all users with pagination
     */
    public function getAll(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $query = EloquentUser::query();
        
        // Apply filters
        if (isset($filters['role']) && $filters['role']) {
            $query->where('role', $filters['role']);
        }
        
        // Get paginated results
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        
        // Convert to domain models
        $users = $paginator->map(function ($eloquentUser) {
            return $this->toDomainModel($eloquentUser);
        })->all();
        
        return [
            'data' => $users,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage()
        ];
    }
    
    /**
     * Get users by role
     */
    public function getByRole(string $role, int $page = 1, int $perPage = 15): array
    {
        $paginator = EloquentUser::where('role', $role)
            ->paginate($perPage, ['*'], 'page', $page);
        
        $users = $paginator->map(function ($eloquentUser) {
            return $this->toDomainModel($eloquentUser);
        })->all();
        
        return [
            'data' => $users,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage()
        ];
    }
    
    /**
     * Save user (create or update)
     */
    public function save(User $user): User
    {
        if ($user->getId() === 0) {
            // Create new user
            $eloquentUser = new EloquentUser();
        } else {
            // Update existing user
            $eloquentUser = EloquentUser::findOrFail($user->getId());
        }
        
        // Set attributes
        $eloquentUser->username = $user->getUsername();
        $eloquentUser->email = $user->getEmail();
        $eloquentUser->password = $user->getPassword();
        $eloquentUser->role = $user->getRole();
        $eloquentUser->avatar = $user->getAvatar();
        $eloquentUser->bio = $user->getBio();
        $eloquentUser->remember_token = $user->getRememberToken();
        
        // Save to database
        $eloquentUser->save();
        
        // Return domain model with updated ID
        return $this->toDomainModel($eloquentUser);
    }
    
    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        return EloquentUser::destroy($id) > 0;
    }
    
    /**
     * Restore deleted user
     */
    public function restore(int $id): bool
    {
        $eloquentUser = EloquentUser::withTrashed()->find($id);
        
        if (!$eloquentUser) {
            return false;
        }
        
        return $eloquentUser->restore();
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists(string $username, ?int $excludeUserId = null): bool
    {
        $query = EloquentUser::where('username', $username);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->exists();
    }
    
    /**
     * Check if email exists
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $query = EloquentUser::where('email', $email);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->exists();
    }
}
