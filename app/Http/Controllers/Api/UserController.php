<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserCollection;
use Domain\User\Actions\CreateUserAction;
use Domain\User\Actions\UpdateUserAction;
use Domain\User\Actions\DeleteUserAction;
use Domain\User\DataTransferObjects\UserData;
use Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Display a listing of users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['role']);

        $result = $this->userRepository->getAll($page, $perPage, $filters);

        return response()->json([
            'data' => UserCollection::make($result['data']),
            'meta' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page']
            ]
        ]);
    }

    /**
     * Store a newly created user.
     *
     * @param StoreUserRequest $request
     * @param CreateUserAction $createUserAction
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request, CreateUserAction $createUserAction): JsonResponse
    {
        $userData = UserData::fromArray($request->validated());

        try {
            $user = $createUserAction->execute($userData);

            return response()->json([
                'message' => 'User created successfully',
                'data' => new UserResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified user.
     *
     * @param string $username
     * @return JsonResponse
     */
    public function show(string $username): JsonResponse
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update the specified user.
     *
     * @param UpdateUserRequest $request
     * @param string $username
     * @param UpdateUserAction $updateUserAction
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $username, UpdateUserAction $updateUserAction): JsonResponse
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $userData = UserData::fromArray(array_merge(
            ['id' => $user->getId()],
            $request->validated()
        ));

        try {
            $updatedUser = $updateUserAction->execute($userData);

            return response()->json([
                'message' => 'User updated successfully',
                'data' => new UserResource($updatedUser)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified user.
     *
     * @param string $username
     * @param DeleteUserAction $deleteUserAction
     * @return JsonResponse
     */
    public function destroy(string $username, DeleteUserAction $deleteUserAction): JsonResponse
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $deleteUserAction->execute($user->getId());

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get users by role.
     *
     * @param Request $request
     * @param string $role
     * @return JsonResponse
     */
    public function byRole(Request $request, string $role): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);

        $result = $this->userRepository->getByRole($role, $page, $perPage);

        return response()->json([
            'data' => UserCollection::make($result['data']),
            'meta' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page']
            ]
        ]);
    }

    /**
     * Get current authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Convert Eloquent user to Domain user
        $domainUser = $this->userRepository->findById($user->id);

        return response()->json([
            'data' => new UserResource($domainUser)
        ]);
    }
}
