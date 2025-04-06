<?php

namespace Domain\Bookmark\Models;

/**
 * Domain Model for Bookmark
 */
class Bookmark
{
    private int $id;
    private int $userId;
    private int $mangaId;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;

    /**
     * Create a new Bookmark instance
     */
    public function __construct(
        int $id,
        int $userId,
        int $mangaId,
        ?\DateTimeInterface $createdAt = null,
        ?\DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->mangaId = $mangaId;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
    }

    /**
     * Get bookmark ID
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
