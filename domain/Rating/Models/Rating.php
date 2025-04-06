<?php

namespace Domain\Rating\Models;

/**
 * Domain Model for Rating
 */
class Rating
{
    private int $id;
    private int $userId;
    private int $mangaId;
    private float $score;
    private ?string $comment;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;

    /**
     * Create a new Rating instance
     */
    public function __construct(
        int $id,
        int $userId,
        int $mangaId,
        float $score,
        ?string $comment = null,
        ?\DateTimeInterface $createdAt = null,
        ?\DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->mangaId = $mangaId;
        $this->score = $this->validateScore($score);
        $this->comment = $comment;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
    }

    /**
     * Get rating ID
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
     * Get score
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * Set score
     */
    public function setScore(float $score): self
    {
        $this->score = $this->validateScore($score);
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Get comment
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set comment
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
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

    /**
     * Validate score (must be between 1 and 5)
     */
    private function validateScore(float $score): float
    {
        if ($score < 1) {
            return 1.0;
        }
        
        if ($score > 5) {
            return 5.0;
        }
        
        return $score;
    }
}
