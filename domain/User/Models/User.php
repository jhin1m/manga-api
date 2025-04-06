<?php

namespace Domain\User\Models;

/**
 * Domain Model for User
 */
class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $role;
    private ?string $avatar;
    private ?string $bio;
    private string $rememberToken;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
    private ?\DateTimeInterface $deletedAt;

    /**
     * Create a new User instance
     */
    public function __construct(
        int $id,
        string $username,
        string $email,
        string $password,
        string $role = 'user',
        ?string $avatar = null,
        ?string $bio = null,
        string $rememberToken = '',
        ?\DateTimeInterface $createdAt = null,
        ?\DateTimeInterface $updatedAt = null,
        ?\DateTimeInterface $deletedAt = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->avatar = $avatar;
        $this->bio = $bio;
        $this->rememberToken = $rememberToken;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
        $this->deletedAt = $deletedAt;
    }

    /**
     * Get user ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get username
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set username
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get password (hashed)
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set password (should be hashed before setting)
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get user role
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Set user role
     */
    public function setRole(string $role): self
    {
        $this->role = $role;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get user avatar
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Set user avatar
     */
    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get user bio
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * Set user bio
     */
    public function setBio(?string $bio): self
    {
        $this->bio = $bio;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get remember token
     */
    public function getRememberToken(): string
    {
        return $this->rememberToken;
    }

    /**
     * Set remember token
     */
    public function setRememberToken(string $rememberToken): self
    {
        $this->rememberToken = $rememberToken;
        return $this;
    }

    /**
     * Get user created at datetime
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Get user updated at datetime
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Get user deleted at datetime
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * Soft delete user
     */
    public function delete(): self
    {
        $this->deletedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Restore deleted user
     */
    public function restore(): self
    {
        $this->deletedAt = null;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Check if user is deleted
     */
    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
