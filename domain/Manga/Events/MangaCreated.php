<?php

namespace Domain\Manga\Events;

use Domain\Manga\Models\Manga;

/**
 * Event triggered when a manga is created
 */
class MangaCreated
{
    /**
     * @param Manga $manga
     */
    public function __construct(
        public readonly Manga $manga
    ) {
    }
}
