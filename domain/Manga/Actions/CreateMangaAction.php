<?php

namespace Domain\Manga\Actions;

use Domain\Manga\DataTransferObjects\MangaData;
use Domain\Manga\Models\Manga;
use Domain\Manga\Repositories\MangaRepositoryInterface;
use Domain\Manga\Events\MangaCreated;

/**
 * Action to create a new manga
 */
class CreateMangaAction
{
    /**
     * @param MangaRepositoryInterface $mangaRepository
     */
    public function __construct(
        private MangaRepositoryInterface $mangaRepository
    ) {
    }

    /**
     * Execute the action
     *
     * @param MangaData $mangaData
     * @return Manga
     */
    public function execute(MangaData $mangaData): Manga
    {
        // Create a new Manga domain model
        $manga = new Manga(
            id: 0, // Temporary ID, will be replaced by repository
            title: $mangaData->title,
            slug: $mangaData->slug ?? $this->generateSlug($mangaData->title),
            description: $mangaData->description,
            status: $mangaData->status,
            coverImage: $mangaData->coverImage,
            thumbnail: $mangaData->thumbnail,
            authorId: $mangaData->authorId,
            artistId: $mangaData->artistId,
            releaseYear: $mangaData->releaseYear,
            isFeatured: $mangaData->isFeatured,
            isPublished: $mangaData->isPublished,
            categories: $mangaData->categories,
            tags: $mangaData->tags
        );

        // Save the manga using repository
        $savedManga = $this->mangaRepository->save($manga);

        // Dispatch event
        // In Laravel 11.x, events are auto-discovered
        event(new MangaCreated($savedManga));

        return $savedManga;
    }

    /**
     * Generate a slug from title
     *
     * @param string $title
     * @return string
     */
    private function generateSlug(string $title): string
    {
        // Convert to lowercase
        $slug = strtolower($title);
        
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
