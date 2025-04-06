<?php

namespace Domain\Chapter\Events;

use Domain\Chapter\Models\Chapter;

/**
 * Event triggered when a chapter is created
 */
class ChapterCreated
{
    /**
     * @param Chapter $chapter
     */
    public function __construct(
        public readonly Chapter $chapter
    ) {
    }
}
