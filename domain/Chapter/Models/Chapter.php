<?php

namespace Domain\Chapter\Models;

use Domain\Manga\Models\Manga;

/**
 * Domain Model for Chapter
 */
class Chapter
{
    private int $id;
    private int $mangaId;
    private float $chapterNumber;
    private ?string $title;
    private string $slug;
    private ?string $description;
    private ?\DateTimeInterface $releaseDate;
    private int $views;
    private bool $isPublished;
    private array $pages;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
    private ?\DateTimeInterface $deletedAt;

    /**
     * Create a new Chapter instance
     */
    public function __construct(
        int $id,
        int $mangaId,
        float $chapterNumber,
        string $slug,
        ?string $title = null,
        ?string $description = null,
        ?\DateTimeInterface $releaseDate = null,
        int $views = 0,
        bool $isPublished = true,
        array $pages = [],
        ?\DateTimeInterface $createdAt = null,
        ?\DateTimeInterface $updatedAt = null,
        ?\DateTimeInterface $deletedAt = null
    ) {
        $this->id = $id;
        $this->mangaId = $mangaId;
        $this->chapterNumber = $chapterNumber;
        $this->slug = $slug;
        $this->title = $title;
        $this->description = $description;
        $this->releaseDate = $releaseDate;
        $this->views = $views;
        $this->isPublished = $isPublished;
        $this->pages = $pages;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
        $this->deletedAt = $deletedAt;
    }

    /**
     * Get chapter ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get manga ID
     */
    public function getMangaId(): int
    {
        return $this->mangaId;
    }

    /**
     * Get chapter number
     */
    public function getChapterNumber(): float
    {
        return $this->chapterNumber;
    }

    /**
     * Set chapter number
     */
    public function setChapterNumber(float $chapterNumber): self
    {
        $this->chapterNumber = $chapterNumber;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get chapter title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set chapter title
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get chapter slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set chapter slug
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get chapter description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set chapter description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get chapter release date
     */
    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    /**
     * Set chapter release date
     */
    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get chapter views count
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * Increment chapter views count
     */
    public function incrementViews(): self
    {
        $this->views++;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Set chapter views count
     */
    public function setViews(int $views): self
    {
        $this->views = $views;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Check if chapter is published
     */
    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    /**
     * Set chapter published status
     */
    public function setPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get chapter pages
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * Set chapter pages
     */
    public function setPages(array $pages): self
    {
        $this->pages = $pages;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Add a page to chapter
     */
    public function addPage(object $page): self
    {
        $this->pages[] = $page;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get chapter created at datetime
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Get chapter updated at datetime
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Get chapter deleted at datetime
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * Soft delete chapter
     */
    public function delete(): self
    {
        $this->deletedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Restore deleted chapter
     */
    public function restore(): self
    {
        $this->deletedAt = null;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Check if chapter is deleted
     */
    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
