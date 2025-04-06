<?php

namespace Domain\User\Repositories;

use Domain\User\Models\User;

/**
 * Interface for User Repository
 */
interface UserRepositoryInterface
{
    /**
     * Find user by ID
     */
    public function findById(int $id): ?User;
    
    /**
     * Find user by username
     */
    public function findByUsername(string $username): ?User;
    
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;
    
    /**
     * Get all users with pagination
     */
    public function getAll(int $page = 1, int $perPage = 15, array $filters = []): array;
    
    /**
     * Get users by role
     */
    public function getByRole(string $role, int $page = 1, int $perPage = 15): array;
    
    /**
     * Save user (create or update)
     */
    public function save(User $user): User;
    
    /**
     * Delete user
     */
    public function delete(int $id): bool;
    
    /**
     * Restore deleted user
     */
    public function restore(int $id): bool;
    
    /**
     * Check if username exists
     */
    public function usernameExists(string $username, ?int $excludeUserId = null): bool;
    
    /**
     * Check if email exists
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool;
}
