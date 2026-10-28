<?php

namespace App\Enums;

enum PostType: string
{
    case Announcement = 'announcement';
    case Event = 'event';

    /**
     * Get a human-readable label for the post type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Announcement => 'Announcement',
            self::Event => 'Event',
        };
    }
}
