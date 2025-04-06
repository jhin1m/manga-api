<?php

namespace Infrastructure\Repositories;

use App\Models\Chapter as EloquentChapter;
use Domain\Chapter\Models\Chapter;
use Domain\Chapter\Repositories\ChapterRepositoryInterface;

/**
 * Eloquent implementation of ChapterRepositoryInterface
 */
class EloquentChapterRepository implements ChapterRepositoryInterface
{
    /**
     * Convert Eloquent model to Domain model
     */
    private function toDomainModel(EloquentChapter $eloquentChapter): Chapter
    {
        return new Chapter(
            id: $eloquentChapter->id,
            mangaId: $eloquentChapter->manga_id,
            chapterNumber: $eloquentChapter->chapter_number,
            slug: $eloquentChapter->slug,
            title: $eloquentChapter->title,
            description: $eloquentChapter->description,
            releaseDate: $eloquentChapter->release_date,
            views: $eloquentChapter->views,
            isPublished: $eloquentChapter->is_published,
            pages: $eloquentChapter->pages->toArray(), // Convert pages to array
            createdAt: $eloquentChapter->created_at,
            updatedAt: $eloquentChapter->updated_at,
            deletedAt: $eloquentChapter->deleted_at
        );
    }

    /**
     * Find chapter by ID
     */
    public function findById(int $id): ?Chapter
    {
        $eloquentChapter = EloquentChapter::with('pages')->find($id);
        
        if (!$eloquentChapter) {
            return null;
        }
        
        return $this->toDomainModel($eloquentChapter);
    }
    
    /**
     * Find chapter by slug
     */
    public function findBySlug(string $slug): ?Chapter
    {
        $eloquentChapter = EloquentChapter::with('pages')->where('slug', $slug)->first();
        
        if (!$eloquentChapter) {
            return null;
        }
        
        return $this->toDomainModel($eloquentChapter);
    }
    
    /**
     * Get chapters by manga ID with pagination
     */
    public function getByMangaId(int $mangaId, int $page = 1, int $perPage = 15, bool $onlyPublished = true): array
    {
        $query = EloquentChapter::where('manga_id', $mangaId);
        
        if ($onlyPublished) {
            $query->where('is_published', true);
        }
        
        $paginator = $query->orderBy('chapter_number', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);
        
        $chapters = $paginator->map(function ($eloquentChapter) {
            return $this->toDomainModel($eloquentChapter);
        })->all();
        
        return [
            'data' => $chapters,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage()
        ];
    }
    
    /**
     * Get latest chapters
     */
    public function getLatest(int $limit = 20, bool $onlyPublished = true): array
    {
        $query = EloquentChapter::query();
        
        if ($onlyPublished) {
            $query->where('is_published', true);
        }
        
        $eloquentChapters = $query->orderBy('release_date', 'desc')
            ->limit($limit)
            ->get();
        
        return $eloquentChapters->map(function ($eloquentChapter) {
            return $this->toDomainModel($eloquentChapter);
        })->all();
    }
    
    /**
     * Get chapter by manga ID and chapter number
     */
    public function getByMangaIdAndNumber(int $mangaId, float $chapterNumber): ?Chapter
    {
        $eloquentChapter = EloquentChapter::with('pages')
            ->where('manga_id', $mangaId)
            ->where('chapter_number', $chapterNumber)
            ->first();
        
        if (!$eloquentChapter) {
            return null;
        }
        
        return $this->toDomainModel($eloquentChapter);
    }
    
    /**
     * Get next chapter
     */
    public function getNextChapter(int $mangaId, float $currentChapterNumber): ?Chapter
    {
        $eloquentChapter = EloquentChapter::where('manga_id', $mangaId)
            ->where('chapter_number', '>', $currentChapterNumber)
            ->where('is_published', true)
            ->orderBy('chapter_number', 'asc')
            ->first();
        
        if (!$eloquentChapter) {
            return null;
        }
        
        return $this->toDomainModel($eloquentChapter);
    }
    
    /**
     * Get previous chapter
     */
    public function getPreviousChapter(int $mangaId, float $currentChapterNumber): ?Chapter
    {
        $eloquentChapter = EloquentChapter::where('manga_id', $mangaId)
            ->where('chapter_number', '<', $currentChapterNumber)
            ->where('is_published', true)
            ->orderBy('chapter_number', 'desc')
            ->first();
        
        if (!$eloquentChapter) {
            return null;
        }
        
        return $this->toDomainModel($eloquentChapter);
    }
    
    /**
     * Save chapter (create or update)
     */
    public function save(Chapter $chapter): Chapter
    {
        if ($chapter->getId() === 0) {
            // Create new chapter
            $eloquentChapter = new EloquentChapter();
        } else {
            // Update existing chapter
            $eloquentChapter = EloquentChapter::findOrFail($chapter->getId());
        }
        
        // Set attributes
        $eloquentChapter->manga_id = $chapter->getMangaId();
        $eloquentChapter->chapter_number = $chapter->getChapterNumber();
        $eloquentChapter->title = $chapter->getTitle();
        $eloquentChapter->slug = $chapter->getSlug();
        $eloquentChapter->description = $chapter->getDescription();
        $eloquentChapter->release_date = $chapter->getReleaseDate();
        $eloquentChapter->views = $chapter->getViews();
        $eloquentChapter->is_published = $chapter->isPublished();
        
        // Save to database
        $eloquentChapter->save();
        
        // Return domain model with updated ID
        return $this->toDomainModel($eloquentChapter);
    }
    
    /**
     * Delete chapter
     */
    public function delete(int $id): bool
    {
        return EloquentChapter::destroy($id) > 0;
    }
    
    /**
     * Restore deleted chapter
     */
    public function restore(int $id): bool
    {
        $eloquentChapter = EloquentChapter::withTrashed()->find($id);
        
        if (!$eloquentChapter) {
            return false;
        }
        
        return $eloquentChapter->restore();
    }
    
    /**
     * Increment chapter views
     */
    public function incrementViews(int $id): bool
    {
        return EloquentChapter::where('id', $id)->increment('views') > 0;
    }
}
