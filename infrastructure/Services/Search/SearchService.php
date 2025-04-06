<?php

namespace Infrastructure\Services\Search;

use Domain\Manga\Repositories\MangaRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Service for advanced search functionality
 */
class SearchService
{
    /**
     * @param MangaRepositoryInterface $mangaRepository
     */
    public function __construct(
        private MangaRepositoryInterface $mangaRepository
    ) {
    }

    /**
     * Search manga with advanced filters
     *
     * @param array $params
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function searchManga(array $params, int $page = 1, int $perPage = 15): array
    {
        $query = DB::table('mangas')
            ->select('mangas.*')
            ->where('mangas.is_published', true)
            ->whereNull('mangas.deleted_at');

        // Apply text search
        if (!empty($params['q'])) {
            $searchTerm = '%' . $params['q'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('mangas.title', 'like', $searchTerm)
                  ->orWhere('mangas.description', 'like', $searchTerm);
            });
        }

        // Filter by status
        if (!empty($params['status'])) {
            $query->where('mangas.status', $params['status']);
        }

        // Filter by release year
        if (!empty($params['year'])) {
            $query->where('mangas.release_year', $params['year']);
        }

        // Filter by author
        if (!empty($params['author_id'])) {
            $query->where('mangas.author_id', $params['author_id']);
        }

        // Filter by artist
        if (!empty($params['artist_id'])) {
            $query->where('mangas.artist_id', $params['artist_id']);
        }

        // Filter by categories
        if (!empty($params['categories'])) {
            $categoryIds = is_array($params['categories']) 
                ? $params['categories'] 
                : explode(',', $params['categories']);
            
            $query->join('category_manga', 'mangas.id', '=', 'category_manga.manga_id')
                  ->whereIn('category_manga.category_id', $categoryIds)
                  ->groupBy('mangas.id')
                  ->havingRaw('COUNT(DISTINCT category_manga.category_id) = ?', [count($categoryIds)]);
        }

        // Filter by tags
        if (!empty($params['tags'])) {
            $tagIds = is_array($params['tags']) 
                ? $params['tags'] 
                : explode(',', $params['tags']);
            
            $query->join('manga_tag', 'mangas.id', '=', 'manga_tag.manga_id')
                  ->whereIn('manga_tag.tag_id', $tagIds)
                  ->groupBy('mangas.id');
        }

        // Sort results
        $sortField = $params['sort_by'] ?? 'updated_at';
        $sortDirection = $params['sort_direction'] ?? 'desc';
        
        $allowedSortFields = ['title', 'views', 'average_rating', 'created_at', 'updated_at', 'release_year'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy("mangas.{$sortField}", $sortDirection);
        } else {
            $query->orderBy('mangas.updated_at', 'desc');
        }

        // Get paginated results
        $total = $query->count();
        $items = $query->forPage($page, $perPage)->get();
        
        // Convert to domain models
        $mangaIds = $items->pluck('id')->toArray();
        $mangas = [];
        
        foreach ($mangaIds as $mangaId) {
            $manga = $this->mangaRepository->findById($mangaId);
            if ($manga) {
                $mangas[] = $manga;
            }
        }

        return [
            'data' => $mangas,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
}
