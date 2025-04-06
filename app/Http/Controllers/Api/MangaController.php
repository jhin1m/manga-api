<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manga\StoreMangaRequest;
use App\Http\Requests\Manga\UpdateMangaRequest;
use App\Http\Resources\Manga\MangaResource;
use App\Http\Resources\Manga\MangaCollection;
use Domain\Manga\Actions\CreateMangaAction;
use Domain\Manga\Actions\UpdateMangaAction;
use Domain\Manga\Actions\DeleteMangaAction;
use Domain\Manga\DataTransferObjects\MangaData;
use Domain\Manga\Repositories\MangaRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MangaController extends Controller
{
    /**
     * @param MangaRepositoryInterface $mangaRepository
     */
    public function __construct(
        private MangaRepositoryInterface $mangaRepository
    ) {
    }

    /**
     * Display a listing of mangas.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['status', 'is_published', 'author_id', 'artist_id']);

        $result = $this->mangaRepository->getAll($page, $perPage, $filters);

        return response()->json([
            'data' => MangaCollection::make($result['data']),
            'meta' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page']
            ]
        ]);
    }

    /**
     * Store a newly created manga.
     *
     * @param StoreMangaRequest $request
     * @param CreateMangaAction $createMangaAction
     * @return JsonResponse
     */
    public function store(StoreMangaRequest $request, CreateMangaAction $createMangaAction): JsonResponse
    {
        $mangaData = MangaData::fromArray($request->validated());
        $manga = $createMangaAction->execute($mangaData);

        return response()->json([
            'message' => 'Manga created successfully',
            'data' => new MangaResource($manga)
        ], 201);
    }

    /**
     * Display the specified manga.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $manga = $this->mangaRepository->findBySlug($slug);

        if (!$manga) {
            return response()->json([
                'message' => 'Manga not found'
            ], 404);
        }

        return response()->json([
            'data' => new MangaResource($manga)
        ]);
    }

    /**
     * Update the specified manga.
     *
     * @param UpdateMangaRequest $request
     * @param string $slug
     * @param UpdateMangaAction $updateMangaAction
     * @return JsonResponse
     */
    public function update(UpdateMangaRequest $request, string $slug, UpdateMangaAction $updateMangaAction): JsonResponse
    {
        $manga = $this->mangaRepository->findBySlug($slug);

        if (!$manga) {
            return response()->json([
                'message' => 'Manga not found'
            ], 404);
        }

        $mangaData = MangaData::fromArray(array_merge(
            ['id' => $manga->getId()],
            $request->validated()
        ));

        $updatedManga = $updateMangaAction->execute($mangaData);

        return response()->json([
            'message' => 'Manga updated successfully',
            'data' => new MangaResource($updatedManga)
        ]);
    }

    /**
     * Remove the specified manga.
     *
     * @param string $slug
     * @param DeleteMangaAction $deleteMangaAction
     * @return JsonResponse
     */
    public function destroy(string $slug, DeleteMangaAction $deleteMangaAction): JsonResponse
    {
        $manga = $this->mangaRepository->findBySlug($slug);

        if (!$manga) {
            return response()->json([
                'message' => 'Manga not found'
            ], 404);
        }

        $deleteMangaAction->execute($manga->getId());

        return response()->json([
            'message' => 'Manga deleted successfully'
        ]);
    }

    /**
     * Get featured mangas.
     *
     * @return JsonResponse
     */
    public function featured(): JsonResponse
    {
        $mangas = $this->mangaRepository->getFeatured();

        return response()->json([
            'data' => MangaCollection::make($mangas)
        ]);
    }

    /**
     * Get popular mangas.
     *
     * @return JsonResponse
     */
    public function popular(): JsonResponse
    {
        $mangas = $this->mangaRepository->getPopular();

        return response()->json([
            'data' => MangaCollection::make($mangas)
        ]);
    }

    /**
     * Get latest updated mangas.
     *
     * @return JsonResponse
     */
    public function latest(): JsonResponse
    {
        $mangas = $this->mangaRepository->getLatestUpdated();

        return response()->json([
            'data' => MangaCollection::make($mangas)
        ]);
    }

    /**
     * Get mangas by category.
     *
     * @param Request $request
     * @param int $categoryId
     * @return JsonResponse
     */
    public function byCategory(Request $request, int $categoryId): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);

        $result = $this->mangaRepository->getByCategory($categoryId, $page, $perPage);

        return response()->json([
            'data' => MangaCollection::make($result['data']),
            'meta' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page']
            ]
        ]);
    }

    /**
     * Search mangas.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);

        if (!$query) {
            return response()->json([
                'message' => 'Search query is required'
            ], 400);
        }

        $result = $this->mangaRepository->search($query, $page, $perPage);

        return response()->json([
            'data' => MangaCollection::make($result['data']),
            'meta' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page']
            ]
        ]);
    }
}
