<?php

namespace Domain\Manga\Models;

use Domain\Chapter\Models\Chapter;
use Domain\User\Models\User;

/**
 * Domain Model for Manga
 */
class Manga
{
    private int $id;
    private string $title;
    private string $slug;
    private ?string $description;
    private string $status;
    private ?string $coverImage;
    private ?string $thumbnail;
    private ?int $authorId;
    private ?int $artistId;
    private ?int $releaseYear;
    private bool $isFeatured;
    private bool $isPublished;
    private int $views;
    private float $averageRating;
    private array $categories;
    private array $tags;
    private array $chapters;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
    private ?\DateTimeInterface $deletedAt;

    /**
     * Create a new Manga instance
     */
    public function __construct(
        int $id,
        string $title,
        string $slug,
        ?string $description = null,
        string $status = 'ongoing',
        ?string $coverImage = null,
        ?string $thumbnail = null,
        ?int $authorId = null,
        ?int $artistId = null,
        ?int $releaseYear = null,
        bool $isFeatured = false,
        bool $isPublished = true,
        int $views = 0,
        float $averageRating = 0.0,
        array $categories = [],
        array $tags = [],
        array $chapters = [],
        ?\DateTimeInterface $createdAt = null,
        ?\DateTimeInterface $updatedAt = null,
        ?\DateTimeInterface $deletedAt = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->description = $description;
        $this->status = $status;
        $this->coverImage = $coverImage;
        $this->thumbnail = $thumbnail;
        $this->authorId = $authorId;
        $this->artistId = $artistId;
        $this->releaseYear = $releaseYear;
        $this->isFeatured = $isFeatured;
        $this->isPublished = $isPublished;
        $this->views = $views;
        $this->averageRating = $averageRating;
        $this->categories = $categories;
        $this->tags = $tags;
        $this->chapters = $chapters;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
        $this->deletedAt = $deletedAt;
    }

    /**
     * Get manga ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get manga title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set manga title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set manga slug
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set manga description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set manga status
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga cover image
     */
    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    /**
     * Set manga cover image
     */
    public function setCoverImage(?string $coverImage): self
    {
        $this->coverImage = $coverImage;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga thumbnail
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * Set manga thumbnail
     */
    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga author ID
     */
    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    /**
     * Set manga author ID
     */
    public function setAuthorId(?int $authorId): self
    {
        $this->authorId = $authorId;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga artist ID
     */
    public function getArtistId(): ?int
    {
        return $this->artistId;
    }

    /**
     * Set manga artist ID
     */
    public function setArtistId(?int $artistId): self
    {
        $this->artistId = $artistId;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga release year
     */
    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    /**
     * Set manga release year
     */
    public function setReleaseYear(?int $releaseYear): self
    {
        $this->releaseYear = $releaseYear;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Check if manga is featured
     */
    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    /**
     * Set manga featured status
     */
    public function setFeatured(bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Check if manga is published
     */
    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    /**
     * Set manga published status
     */
    public function setPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga views count
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * Increment manga views count
     */
    public function incrementViews(): self
    {
        $this->views++;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Set manga views count
     */
    public function setViews(int $views): self
    {
        $this->views = $views;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga average rating
     */
    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    /**
     * Set manga average rating
     */
    public function setAverageRating(float $averageRating): self
    {
        $this->averageRating = $averageRating;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga categories
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Set manga categories
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Add a category to manga
     */
    public function addCategory(int $categoryId): self
    {
        if (!in_array($categoryId, $this->categories)) {
            $this->categories[] = $categoryId;
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * Remove a category from manga
     */
    public function removeCategory(int $categoryId): self
    {
        $key = array_search($categoryId, $this->categories);
        if ($key !== false) {
            unset($this->categories[$key]);
            $this->categories = array_values($this->categories);
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * Get manga tags
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Set manga tags
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Add a tag to manga
     */
    public function addTag(int $tagId): self
    {
        if (!in_array($tagId, $this->tags)) {
            $this->tags[] = $tagId;
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * Remove a tag from manga
     */
    public function removeTag(int $tagId): self
    {
        $key = array_search($tagId, $this->tags);
        if ($key !== false) {
            unset($this->tags[$key]);
            $this->tags = array_values($this->tags);
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * Get manga chapters
     */
    public function getChapters(): array
    {
        return $this->chapters;
    }

    /**
     * Set manga chapters
     */
    public function setChapters(array $chapters): self
    {
        $this->chapters = $chapters;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Add a chapter to manga
     */
    public function addChapter(Chapter $chapter): self
    {
        $this->chapters[] = $chapter;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get manga created at datetime
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Get manga updated at datetime
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Get manga deleted at datetime
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * Soft delete manga
     */
    public function delete(): self
    {
        $this->deletedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Restore deleted manga
     */
    public function restore(): self
    {
        $this->deletedAt = null;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Check if manga is deleted
     */
    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
