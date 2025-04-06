<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chapter\StoreChapterRequest;
use App\Http\Requests\Chapter\UpdateChapterRequest;
use App\Http\Resources\Chapter\ChapterResource;
use App\Http\Resources\Chapter\ChapterCollection;
use Domain\Chapter\Actions\CreateChapterAction;
use Domain\Chapter\Actions\UpdateChapterAction;
use Domain\Chapter\Actions\DeleteChapterAction;
use Domain\Chapter\DataTransferObjects\ChapterData;
use Domain\Chapter\Repositories\ChapterRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChapterController extends Controller
{
    /**
     * @param ChapterRepositoryInterface $chapterRepository
     */
    public function __construct(
        private ChapterRepositoryInterface $chapterRepository
    ) {
    }

    /**
     * Display a listing of chapters for a manga.
     *
     * @param Request $request
     * @param int $mangaId
     * @return JsonResponse
     */
    public function index(Request $request, int $mangaId): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);
        $onlyPublished = $request->boolean('only_published', true);

        $result = $this->chapterRepository->getByMangaId($mangaId, $page, $perPage, $onlyPublished);

        return response()->json([
            'data' => ChapterCollection::make($result['data']),
            'meta' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page']
            ]
        ]);
    }

    /**
     * Store a newly created chapter.
     *
     * @param StoreChapterRequest $request
     * @param int $mangaId
     * @param CreateChapterAction $createChapterAction
     * @return JsonResponse
     */
    public function store(StoreChapterRequest $request, int $mangaId, CreateChapterAction $createChapterAction): JsonResponse
    {
        $chapterData = ChapterData::fromArray(array_merge(
            ['manga_id' => $mangaId],
            $request->validated()
        ));

        try {
            $chapter = $createChapterAction->execute($chapterData);

            return response()->json([
                'message' => 'Chapter created successfully',
                'data' => new ChapterResource($chapter)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified chapter.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $chapter = $this->chapterRepository->findBySlug($slug);

        if (!$chapter) {
            return response()->json([
                'message' => 'Chapter not found'
            ], 404);
        }

        // Increment views
        $this->chapterRepository->incrementViews($chapter->getId());

        return response()->json([
            'data' => new ChapterResource($chapter)
        ]);
    }

    /**
     * Update the specified chapter.
     *
     * @param UpdateChapterRequest $request
     * @param string $slug
     * @param UpdateChapterAction $updateChapterAction
     * @return JsonResponse
     */
    public function update(UpdateChapterRequest $request, string $slug, UpdateChapterAction $updateChapterAction): JsonResponse
    {
        $chapter = $this->chapterRepository->findBySlug($slug);

        if (!$chapter) {
            return response()->json([
                'message' => 'Chapter not found'
            ], 404);
        }

        $chapterData = ChapterData::fromArray(array_merge(
            [
                'id' => $chapter->getId(),
                'manga_id' => $chapter->getMangaId()
            ],
            $request->validated()
        ));

        try {
            $updatedChapter = $updateChapterAction->execute($chapterData);

            return response()->json([
                'message' => 'Chapter updated successfully',
                'data' => new ChapterResource($updatedChapter)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified chapter.
     *
     * @param string $slug
     * @param DeleteChapterAction $deleteChapterAction
     * @return JsonResponse
     */
    public function destroy(string $slug, DeleteChapterAction $deleteChapterAction): JsonResponse
    {
        $chapter = $this->chapterRepository->findBySlug($slug);

        if (!$chapter) {
            return response()->json([
                'message' => 'Chapter not found'
            ], 404);
        }

        $deleteChapterAction->execute($chapter->getId());

        return response()->json([
            'message' => 'Chapter deleted successfully'
        ]);
    }

    /**
     * Get latest chapters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 20);
        $onlyPublished = $request->boolean('only_published', true);

        $chapters = $this->chapterRepository->getLatest($limit, $onlyPublished);

        return response()->json([
            'data' => ChapterCollection::make($chapters)
        ]);
    }

    /**
     * Get next chapter.
     *
     * @param int $mangaId
     * @param float $chapterNumber
     * @return JsonResponse
     */
    public function next(int $mangaId, float $chapterNumber): JsonResponse
    {
        $chapter = $this->chapterRepository->getNextChapter($mangaId, $chapterNumber);

        if (!$chapter) {
            return response()->json([
                'message' => 'No next chapter found'
            ], 404);
        }

        return response()->json([
            'data' => new ChapterResource($chapter)
        ]);
    }

    /**
     * Get previous chapter.
     *
     * @param int $mangaId
     * @param float $chapterNumber
     * @return JsonResponse
     */
    public function previous(int $mangaId, float $chapterNumber): JsonResponse
    {
        $chapter = $this->chapterRepository->getPreviousChapter($mangaId, $chapterNumber);

        if (!$chapter) {
            return response()->json([
                'message' => 'No previous chapter found'
            ], 404);
        }

        return response()->json([
            'data' => new ChapterResource($chapter)
        ]);
    }
}
