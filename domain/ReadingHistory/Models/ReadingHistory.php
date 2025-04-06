<?php

namespace Domain\ReadingHistory\Models;

/**
 * Domain Model for ReadingHistory
 */
class ReadingHistory
{
    private int $id;
    private int $userId;
    private int $mangaId;
    private int $chapterId;
    private float $chapterNumber;
    private int $pageNumber;
    private \DateTimeInterface $lastReadAt;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;

    /**
     * Create a new ReadingHistory instance
     */
    public function __construct(
        int $id,
        int $userId,
        int $mangaId,
        int $chapterId,
        float $chapterNumber,
        int $pageNumber = 1,
        ?\DateTimeInterface $lastReadAt = null,
        ?\DateTimeInterface $createdAt = null,
        ?\DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->mangaId = $mangaId;
        $this->chapterId = $chapterId;
        $this->chapterNumber = $chapterNumber;
        $this->pageNumber = $pageNumber;
        $this->lastReadAt = $lastReadAt ?? new \DateTimeImmutable();
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
    }

    /**
     * Get reading history ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get user ID
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Get manga ID
     */
    public function getMangaId(): int
    {
        return $this->mangaId;
    }

    /**
     * Get chapter ID
     */
    public function getChapterId(): int
    {
        return $this->chapterId;
    }

    /**
     * Get chapter number
     */
    public function getChapterNumber(): float
    {
        return $this->chapterNumber;
    }

    /**
     * Get page number
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * Set page number
     */
    public function setPageNumber(int $pageNumber): self
    {
        $this->pageNumber = $pageNumber;
        $this->lastReadAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get last read at datetime
     */
    public function getLastReadAt(): \DateTimeInterface
    {
        return $this->lastReadAt;
    }

    /**
     * Update last read at datetime
     */
    public function updateLastReadAt(): self
    {
        $this->lastReadAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get created at datetime
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Get updated at datetime
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}
