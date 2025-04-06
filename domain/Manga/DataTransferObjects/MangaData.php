<?php

namespace Domain\Manga\DataTransferObjects;

/**
 * Data Transfer Object for Manga
 */
class MangaData
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $title = '',
        public readonly ?string $slug = null,
        public readonly ?string $description = null,
        public readonly string $status = 'ongoing',
        public readonly ?string $coverImage = null,
        public readonly ?string $thumbnail = null,
        public readonly ?int $authorId = null,
        public readonly ?int $artistId = null,
        public readonly ?int $releaseYear = null,
        public readonly bool $isFeatured = false,
        public readonly bool $isPublished = true,
        public readonly array $categories = [],
        public readonly array $tags = []
    ) {
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? '',
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'ongoing',
            coverImage: $data['cover_image'] ?? null,
            thumbnail: $data['thumbnail'] ?? null,
            authorId: $data['author_id'] ?? null,
            artistId: $data['artist_id'] ?? null,
            releaseYear: $data['release_year'] ?? null,
            isFeatured: $data['is_featured'] ?? false,
            isPublished: $data['is_published'] ?? true,
            categories: $data['categories'] ?? [],
            tags: $data['tags'] ?? []
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'cover_image' => $this->coverImage,
            'thumbnail' => $this->thumbnail,
            'author_id' => $this->authorId,
            'artist_id' => $this->artistId,
            'release_year' => $this->releaseYear,
            'is_featured' => $this->isFeatured,
            'is_published' => $this->isPublished,
            'categories' => $this->categories,
            'tags' => $this->tags
        ];
    }
}
