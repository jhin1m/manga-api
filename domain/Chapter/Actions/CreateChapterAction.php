<?php

namespace Domain\Chapter\Actions;

use Domain\Chapter\DataTransferObjects\ChapterData;
use Domain\Chapter\Models\Chapter;
use Domain\Chapter\Repositories\ChapterRepositoryInterface;
use Domain\Chapter\Events\ChapterCreated;
use Domain\Manga\Repositories\MangaRepositoryInterface;

/**
 * Action to create a new chapter
 */
class CreateChapterAction
{
    /**
     * @param ChapterRepositoryInterface $chapterRepository
     * @param MangaRepositoryInterface $mangaRepository
     */
    public function __construct(
        private ChapterRepositoryInterface $chapterRepository,
        private MangaRepositoryInterface $mangaRepository
    ) {
    }

    /**
     * Execute the action
     *
     * @param ChapterData $chapterData
     * @return Chapter
     * @throws \Exception
     */
    public function execute(ChapterData $chapterData): Chapter
    {
        // Verify manga exists
        $manga = $this->mangaRepository->findById($chapterData->mangaId);
        if (!$manga) {
            throw new \Exception("Manga with ID {$chapterData->mangaId} not found");
        }

        // Create a new Chapter domain model
        $chapter = new Chapter(
            id: 0, // Temporary ID, will be replaced by repository
            mangaId: $chapterData->mangaId,
            chapterNumber: $chapterData->chapterNumber,
            slug: $chapterData->slug ?? $this->generateSlug($manga->getSlug(), $chapterData->chapterNumber, $chapterData->title),
            title: $chapterData->title,
            description: $chapterData->description,
            releaseDate: $chapterData->releaseDate ? new \DateTimeImmutable($chapterData->releaseDate) : new \DateTimeImmutable(),
            isPublished: $chapterData->isPublished
        );

        // Save the chapter using repository
        $savedChapter = $this->chapterRepository->save($chapter);

        // Dispatch event
        // In Laravel 11.x, events are auto-discovered
        event(new ChapterCreated($savedChapter));

        return $savedChapter;
    }

    /**
     * Generate a slug from manga slug, chapter number and title
     *
     * @param string $mangaSlug
     * @param float $chapterNumber
     * @param string|null $title
     * @return string
     */
    private function generateSlug(string $mangaSlug, float $chapterNumber, ?string $title = null): string
    {
        $chapterPart = 'chapter-' . str_replace('.', '-', (string)$chapterNumber);
        
        if ($title) {
            // Convert title to slug
            $titleSlug = strtolower($title);
            $titleSlug = preg_replace('/[^a-z0-9]+/', '-', $titleSlug);
            $titleSlug = trim($titleSlug, '-');
            
            return "{$mangaSlug}/{$chapterPart}-{$titleSlug}";
        }
        
        return "{$mangaSlug}/{$chapterPart}";
    }
}
