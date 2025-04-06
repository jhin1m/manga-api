<?php

namespace Domain\User\DataTransferObjects;

/**
 * Data Transfer Object for User
 */
class UserData
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $username = '',
        public readonly string $email = '',
        public readonly ?string $password = null,
        public readonly string $role = 'user',
        public readonly ?string $avatar = null,
        public readonly ?string $bio = null
    ) {
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            username: $data['username'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? null,
            role: $data['role'] ?? 'user',
            avatar: $data['avatar'] ?? null,
            bio: $data['bio'] ?? null
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'avatar' => $this->avatar,
            'bio' => $this->bio
        ];
    }
}
