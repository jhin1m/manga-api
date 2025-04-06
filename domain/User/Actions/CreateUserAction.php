<?php

namespace Domain\User\Actions;

use Domain\User\DataTransferObjects\UserData;
use Domain\User\Models\User;
use Domain\User\Repositories\UserRepositoryInterface;
use Domain\User\Events\UserCreated;
use Illuminate\Support\Facades\Hash;

/**
 * Action to create a new user
 */
class CreateUserAction
{
    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Execute the action
     *
     * @param UserData $userData
     * @return User
     * @throws \Exception
     */
    public function execute(UserData $userData): User
    {
        // Check if username already exists
        if ($this->userRepository->usernameExists($userData->username)) {
            throw new \Exception("Username '{$userData->username}' is already taken");
        }

        // Check if email already exists
        if ($this->userRepository->emailExists($userData->email)) {
            throw new \Exception("Email '{$userData->email}' is already registered");
        }

        // Hash password
        $hashedPassword = Hash::make($userData->password);

        // Create a new User domain model
        $user = new User(
            id: 0, // Temporary ID, will be replaced by repository
            username: $userData->username,
            email: $userData->email,
            password: $hashedPassword,
            role: $userData->role,
            avatar: $userData->avatar,
            bio: $userData->bio
        );

        // Save the user using repository
        $savedUser = $this->userRepository->save($user);

        // Dispatch event
        // In Laravel 11.x, events are auto-discovered
        event(new UserCreated($savedUser));

        return $savedUser;
    }
}
