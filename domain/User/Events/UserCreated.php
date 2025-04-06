<?php

namespace Domain\User\Events;

use Domain\User\Models\User;

/**
 * Event triggered when a user is created
 */
class UserCreated
{
    /**
     * @param User $user
     */
    public function __construct(
        public readonly User $user
    ) {
    }
}
