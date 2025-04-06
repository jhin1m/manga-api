<?php

namespace Infrastructure\Repositories;

use App\Models\Manga as EloquentManga;
use Domain\Manga\Models\Manga;
use Domain\Manga\Repositories\MangaRepositoryInterface;

/**
 * Eloquent implementation of MangaRepositoryInterface
 */
class EloquentMangaRepository implements MangaRepositoryInterface
{
    /**
     * Convert Eloquent model to Domain model
     */
    private function toDomainModel(EloquentManga $eloquentManga): Manga
    {
        return new Manga(
            id: $eloquentManga->id,
            title: $eloquentManga->title,
            slug: $eloquentManga->slug,
            description: $eloquentManga->description,
            status: $eloquentManga->status,
            coverImage: $eloquentManga->cover_image,
            thumbnail: $eloquentManga->thumbnail,
            authorId: $eloquentManga->author_id,
            artistId: $eloquentManga->artist_id,
            releaseYear: $eloquentManga->release_year,
            isFeatured: $eloquentManga->is_featured,
            isPublished: $eloquentManga->is_published,
            views: $eloquentManga->views,
            averageRating: $eloquentManga->average_rating,
            categories: $eloquentManga->categories->pluck('id')->toArray(),
            tags: $eloquentManga->tags->pluck('id')->toArray(),
            chapters: [], // We don't load chapters by default for performance reasons
            createdAt: $eloquentManga->created_at,
            updatedAt: $eloquentManga->updated_at,
            deletedAt: $eloquentManga->deleted_at
        );
    }

    /**
     * Find manga by ID
     */
    public function findById(int $id): ?Manga
    {
        $eloquentManga = EloquentManga::find($id);
        
        if (!$eloquentManga) {
            return null;
        }
        
        return $this->toDomainModel($eloquentManga);
    }
    
    /**
     * Find manga by slug
     */
    public function findBySlug(string $slug): ?Manga
    {
        $eloquentManga = EloquentManga::where('slug', $slug)->first();
        
        if (!$eloquentManga) {
            return null;
        }
        
        return $this->toDomainModel($eloquentManga);
    }
    
    /**
     * Get all mangas with pagination
     */
    public function getAll(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $query = EloquentManga::query();
        
        // Apply filters
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['is_published']) && is_bool($filters['is_published'])) {
            $query->where('is_published', $filters['is_published']);
        }
        
        if (isset($filters['author_id']) && $filters['author_id']) {
            $query->where('author_id', $filters['author_id']);
        }
        
        if (isset($filters['artist_id']) && $filters['artist_id']) {
            $query->where('artist_id', $filters['artist_id']);
        }
        
        // Get paginated results
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        
        // Convert to domain models
        $mangas = $paginator->map(function ($eloquentManga) {
            return $this->toDomainModel($eloquentManga);
        })->all();
        
        return [
            'data' => $mangas,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage()
        ];
    }
    
    /**
     * Get featured mangas
     */
    public function getFeatured(int $limit = 10): array
    {
        $eloquentMangas = EloquentManga::where('is_featured', true)
            ->where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
        
        return $eloquentMangas->map(function ($eloquentManga) {
            return $this->toDomainModel($eloquentManga);
        })->all();
    }
    
    /**
     * Get popular mangas based on views
     */
    public function getPopular(int $limit = 10): array
    {
        $eloquentMangas = EloquentManga::where('is_published', true)
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
        
        return $eloquentMangas->map(function ($eloquentManga) {
            return $this->toDomainModel($eloquentManga);
        })->all();
    }
    
    /**
     * Get latest updated mangas
     */
    public function getLatestUpdated(int $limit = 10): array
    {
        $eloquentMangas = EloquentManga::where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
        
        return $eloquentMangas->map(function ($eloquentManga) {
            return $this->toDomainModel($eloquentManga);
        })->all();
    }
    
    /**
     * Get mangas by category
     */
    public function getByCategory(int $categoryId, int $page = 1, int $perPage = 15): array
    {
        $paginator = EloquentManga::whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->where('is_published', true)
            ->paginate($perPage, ['*'], 'page', $page);
        
        $mangas = $paginator->map(function ($eloquentManga) {
            return $this->toDomainModel($eloquentManga);
        })->all();
        
        return [
            'data' => $mangas,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage()
        ];
    }
    
    /**
     * Get mangas by author
     */
    public function getByAuthor(int $authorId, int $page = 1, int $perPage = 15): array
    {
        $paginator = EloquentManga::where('author_id', $authorId)
            ->where('is_published', true)
            ->paginate($perPage, ['*'], 'page', $page);
        
        $mangas = $paginator->map(function ($eloquentManga) {
            return $this->toDomainModel($eloquentManga);
        })->all();
        
        return [
            'data' => $mangas,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage()
        ];
    }
    
    /**
     * Get mangas by tag
     */
    public function getByTag(int $tagId, int $page = 1, int $perPage = 15): array
    {
        $paginator = EloquentManga::whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            })
            ->where('is_published', true)
            ->paginate($perPage, ['*'], 'page', $page);
        
        $mangas = $paginator->map(function ($eloquentManga) {
            return $this->toDomainModel($eloquentManga);
        })->all();
        
        return [
            'data' => $mangas,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage()
        ];
    }
    
    /**
     * Search mangas
     */
    public function search(string $query, int $page = 1, int $perPage = 15): array
    {
        $paginator = EloquentManga::where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->where('is_published', true)
            ->paginate($perPage, ['*'], 'page', $page);
        
        $mangas = $paginator->map(function ($eloquentManga) {
            return $this->toDomainModel($eloquentManga);
        })->all();
        
        return [
            'data' => $mangas,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage()
        ];
    }
    
    /**
     * Save manga (create or update)
     */
    public function save(Manga $manga): Manga
    {
        if ($manga->getId() === 0) {
            // Create new manga
            $eloquentManga = new EloquentManga();
        } else {
            // Update existing manga
            $eloquentManga = EloquentManga::findOrFail($manga->getId());
        }
        
        // Set attributes
        $eloquentManga->title = $manga->getTitle();
        $eloquentManga->slug = $manga->getSlug();
        $eloquentManga->description = $manga->getDescription();
        $eloquentManga->status = $manga->getStatus();
        $eloquentManga->cover_image = $manga->getCoverImage();
        $eloquentManga->thumbnail = $manga->getThumbnail();
        $eloquentManga->author_id = $manga->getAuthorId();
        $eloquentManga->artist_id = $manga->getArtistId();
        $eloquentManga->release_year = $manga->getReleaseYear();
        $eloquentManga->is_featured = $manga->isFeatured();
        $eloquentManga->is_published = $manga->isPublished();
        $eloquentManga->views = $manga->getViews();
        $eloquentManga->average_rating = $manga->getAverageRating();
        
        // Save to database
        $eloquentManga->save();
        
        // Sync categories and tags
        $eloquentManga->categories()->sync($manga->getCategories());
        $eloquentManga->tags()->sync($manga->getTags());
        
        // Return domain model with updated ID
        return $this->toDomainModel($eloquentManga);
    }
    
    /**
     * Delete manga
     */
    public function delete(int $id): bool
    {
        return EloquentManga::destroy($id) > 0;
    }
    
    /**
     * Restore deleted manga
     */
    public function restore(int $id): bool
    {
        $eloquentManga = EloquentManga::withTrashed()->find($id);
        
        if (!$eloquentManga) {
            return false;
        }
        
        return $eloquentManga->restore();
    }
    
    /**
     * Increment manga views
     */
    public function incrementViews(int $id): bool
    {
        return EloquentManga::where('id', $id)->increment('views') > 0;
    }
    
    /**
     * Update manga average rating
     */
    public function updateAverageRating(int $id): bool
    {
        $eloquentManga = EloquentManga::find($id);
        
        if (!$eloquentManga) {
            return false;
        }
        
        // Calculate average rating from reviews
        $averageRating = $eloquentManga->reviews()->avg('rating') ?? 0;
        
        // Update manga
        $eloquentManga->average_rating = $averageRating;
        
        return $eloquentManga->save();
    }
    
    /**
     * Add category to manga
     */
    public function addCategory(int $mangaId, int $categoryId): bool
    {
        $eloquentManga = EloquentManga::find($mangaId);
        
        if (!$eloquentManga) {
            return false;
        }
        
        $eloquentManga->categories()->syncWithoutDetaching([$categoryId]);
        
        return true;
    }
    
    /**
     * Remove category from manga
     */
    public function removeCategory(int $mangaId, int $categoryId): bool
    {
        $eloquentManga = EloquentManga::find($mangaId);
        
        if (!$eloquentManga) {
            return false;
        }
        
        $eloquentManga->categories()->detach($categoryId);
        
        return true;
    }
    
    /**
     * Add tag to manga
     */
    public function addTag(int $mangaId, int $tagId): bool
    {
        $eloquentManga = EloquentManga::find($mangaId);
        
        if (!$eloquentManga) {
            return false;
        }
        
        $eloquentManga->tags()->syncWithoutDetaching([$tagId]);
        
        return true;
    }
    
    /**
     * Remove tag from manga
     */
    public function removeTag(int $mangaId, int $tagId): bool
    {
        $eloquentManga = EloquentManga::find($mangaId);
        
        if (!$eloquentManga) {
            return false;
        }
        
        $eloquentManga->tags()->detach($tagId);
        
        return true;
    }
}
