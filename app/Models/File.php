<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class File extends Model
{
    protected $fillable = [
        'folder',
        'filename',
        'filepath',
    ];

    /**
     * Path relative to the public disk root (storage/app/public).
     * Supports legacy rows where filepath was stored as "public/files/...".
     */
    public function publicDiskPath(): string
    {
        $p = (string) $this->filepath;
        if (str_starts_with($p, 'public/')) {
            return substr($p, strlen('public/'));
        }

        return $p;
    }

    public function absolutePath(): string
    {
        return storage_path('app/public/' . $this->publicDiskPath());
    }

    public function folderLabel(): string
    {
        $key = $this->folder ?: 'general';
        $labels = config('repository.folder_presets', []);

        return $labels[$key]
            ?? Str::headline(str_replace(['-', '_'], ' ', $key));
    }
}
