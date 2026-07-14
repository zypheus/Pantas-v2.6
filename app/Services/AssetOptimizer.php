<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;

final class AssetOptimizer
{
    private const JPEG_QUALITY = 85;
    private const PNG_COMPRESSION = 7;
    private const WEBP_QUALITY = 80;

    /** @var array<string, array{max_width: int, max_height: int}> */
    private const TYPE_DIMENSIONS = [
        'banner' => ['max_width' => 4000, 'max_height' => 2000],
        'logo' => ['max_width' => 1000, 'max_height' => 1000],
    ];

    /**
     * Optimize an uploaded image: resize if needed, compress, strip EXIF.
     *
     * @param UploadedFile $file  The uploaded file
     * @param string       $type  One of 'banner' or 'logo'
     */
    public function optimize(UploadedFile $file, string $type = 'banner'): void
    {
        $image = Image::make($file->getRealPath());

        // Resize if larger than recommended dimensions
        $dims = self::TYPE_DIMENSIONS[$type] ?? self::TYPE_DIMENSIONS['banner'];
        if ($image->width() > $dims['max_width'] || $image->height() > $dims['max_height']) {
            $image->resize($dims['max_width'], $dims['max_height'], fn ($constraint) => $constraint->aspectRatio());
        }

        // Strip EXIF metadata and auto-orient
        $image->orientate();

        // Encode to the appropriate format based on MIME type.
        // We use encode() then stream the encoded data to file_put_contents
        // to avoid Intervention's save() which guesses format from file extension.
        $mime = $file->getMimeType();

        if ($mime === 'image/jpeg') {
            $encoded = $image->encode('jpg', self::JPEG_QUALITY);
        } elseif ($mime === 'image/png') {
            $encoded = $image->encode('png', self::PNG_COMPRESSION);
        } elseif ($mime === 'image/webp') {
            $encoded = $image->encode('webp', self::WEBP_QUALITY);
        } else {
            $encoded = $image->encode();
        }

        file_put_contents($file->getRealPath(), (string) $encoded);
    }

    /**
     * Optimize and store an uploaded file, returning the storage path.
     */
    public function optimizeAndStore(UploadedFile $file, string $directory, string $type = 'banner'): string
    {
        $this->optimize($file, $type);

        return $file->store($directory, 'public');
    }
}