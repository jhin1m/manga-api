<?php

namespace Domain\Chapter\Repositories;

use Domain\Chapter\Models\Chapter;

/**
 * Interface for Chapter Repository
 */
interface ChapterRepositoryInterface
{
    /**
     * Find chapter by ID
     */
    public function findById(int $id): ?Chapter;
    
    /**
     * Find chapter by slug
     */
    public function findBySlug(string $slug): ?Chapter;
    
    /**
     * Get chapters by manga ID with pagination
     */
    public function getByMangaId(int $mangaId, int $page = 1, int $perPage = 15, bool $onlyPublished = true): array;
    
    /**
     * Get latest chapters
     */
    public function getLatest(int $limit = 20, bool $onlyPublished = true): array;
    
    /**
     * Get chapter by manga ID and chapter number
     */
    public function getByMangaIdAndNumber(int $mangaId, float $chapterNumber): ?Chapter;
    
    /**
     * Get next chapter
     */
    public function getNextChapter(int $mangaId, float $currentChapterNumber): ?Chapter;
    
    /**
     * Get previous chapter
     */
    public function getPreviousChapter(int $mangaId, float $currentChapterNumber): ?Chapter;
    
    /**
     * Save chapter (create or update)
     */
    public function save(Chapter $chapter): Chapter;
    
    /**
     * Delete chapter
     */
    public function delete(int $id): bool;
    
    /**
     * Restore deleted chapter
     */
    public function restore(int $id): bool;
    
    /**
     * Increment chapter views
     */
    public function incrementViews(int $id): bool;
}
