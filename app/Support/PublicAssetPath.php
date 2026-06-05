<?php

namespace App\Support;

/**
 * Resolve DB-stored relative paths (e.g. images/profile_pictures/foo.jpg)
 * to an absolute file path. Uploads live under public/; legacy copies may sit at project root.
 */
class PublicAssetPath
{
    /**
     * @return list<string>
     */
    public static function candidates(string $relative): array
    {
        $relative = ltrim(str_replace('\\', '/', trim($relative)), '/');

        return array_values(array_unique([
            public_path($relative),
            base_path('public/'.$relative),
            base_path($relative),
        ]));
    }

    public static function resolve(?string $relative): ?string
    {
        if ($relative === null || trim($relative) === '') {
            return null;
        }

        foreach (self::candidates($relative) as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}
