<?php

namespace Domain\Manga\Repositories;

use Domain\Manga\Models\Manga;

/**
 * Interface for Manga Repository
 */
interface MangaRepositoryInterface
{
    /**
     * Find manga by ID
     */
    public function findById(int $id): ?Manga;
    
    /**
     * Find manga by slug
     */
    public function findBySlug(string $slug): ?Manga;
    
    /**
     * Get all mangas with pagination
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param array $filters Optional filters
     * @return array Array with mangas and pagination info
     */
    public function getAll(int $page = 1, int $perPage = 15, array $filters = []): array;
    
    /**
     * Get featured mangas
     */
    public function getFeatured(int $limit = 10): array;
    
    /**
     * Get popular mangas based on views
     */
    public function getPopular(int $limit = 10): array;
    
    /**
     * Get latest updated mangas
     */
    public function getLatestUpdated(int $limit = 10): array;
    
    /**
     * Get mangas by category
     */
    public function getByCategory(int $categoryId, int $page = 1, int $perPage = 15): array;
    
    /**
     * Get mangas by author
     */
    public function getByAuthor(int $authorId, int $page = 1, int $perPage = 15): array;
    
    /**
     * Get mangas by tag
     */
    public function getByTag(int $tagId, int $page = 1, int $perPage = 15): array;
    
    /**
     * Search mangas
     */
    public function search(string $query, int $page = 1, int $perPage = 15): array;
    
    /**
     * Save manga (create or update)
     */
    public function save(Manga $manga): Manga;
    
    /**
     * Delete manga
     */
    public function delete(int $id): bool;
    
    /**
     * Restore deleted manga
     */
    public function restore(int $id): bool;
    
    /**
     * Increment manga views
     */
    public function incrementViews(int $id): bool;
    
    /**
     * Update manga average rating
     */
    public function updateAverageRating(int $id): bool;
    
    /**
     * Add category to manga
     */
    public function addCategory(int $mangaId, int $categoryId): bool;
    
    /**
     * Remove category from manga
     */
    public function removeCategory(int $mangaId, int $categoryId): bool;
    
    /**
     * Add tag to manga
     */
    public function addTag(int $mangaId, int $tagId): bool;
    
    /**
     * Remove tag from manga
     */
    public function removeTag(int $mangaId, int $tagId): bool;
}
