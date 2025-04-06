<?php

namespace Domain\Chapter\DataTransferObjects;

/**
 * Data Transfer Object for Chapter
 */
class ChapterData
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly int $mangaId = 0,
        public readonly float $chapterNumber = 0.0,
        public readonly ?string $title = null,
        public readonly ?string $slug = null,
        public readonly ?string $description = null,
        public readonly ?string $releaseDate = null,
        public readonly bool $isPublished = true,
        public readonly array $pages = []
    ) {
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            mangaId: $data['manga_id'] ?? 0,
            chapterNumber: $data['chapter_number'] ?? 0.0,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            releaseDate: $data['release_date'] ?? null,
            isPublished: $data['is_published'] ?? true,
            pages: $data['pages'] ?? []
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'manga_id' => $this->mangaId,
            'chapter_number' => $this->chapterNumber,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'release_date' => $this->releaseDate,
            'is_published' => $this->isPublished,
            'pages' => $this->pages
        ];
    }
}
