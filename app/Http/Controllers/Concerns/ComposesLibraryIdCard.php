<?php

namespace App\Http\Controllers\Concerns;

use App\Support\PublicAssetPath;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait ComposesLibraryIdCard
{
    protected function idCardTemplate(string $side)
    {
        $path = PublicAssetPath::resolve("images/id_templates/{$side}.png")
            ?? base_path("images/id_templates/{$side}.png");

        return Image::make($path);
    }

    protected function drawIdCardText($img, $text, $x, $y, $size, $color = '#000', $align = 'center', $valign = 'top'): void
    {
        $fontPathBold = public_path('fonts/arialbd.ttf');
        $fontPathRegular = public_path('fonts/arial.ttf');

        if (file_exists($fontPathBold)) {
            $img->text($text, $x, $y, function ($font) use ($fontPathBold, $size, $color, $align, $valign) {
                $font->file($fontPathBold);
                $font->size($size);
                $font->color($color);
                $font->align($align);
                $font->valign($valign);
            });

            return;
        }

        foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$ox, $oy]) {
            $img->text($text, $x + $ox, $y + $oy, function ($font) use ($fontPathRegular, $size, $color, $align, $valign) {
                $font->file($fontPathRegular);
                $font->size($size);
                $font->color($color);
                $font->align($align);
                $font->valign($valign);
            });
        }

        $img->text($text, $x, $y, function ($font) use ($fontPathRegular, $size, $color, $align, $valign) {
            $font->file($fontPathRegular);
            $font->size($size);
            $font->color($color);
            $font->align($align);
            $font->valign($valign);
        });
    }

    /**
     * @param  array{photo:?string,full_name:string,subtitle:?string,id_number:?string}  $data
     */
    protected function composeIdCardFront($img, array $data)
    {
        $photoPath = PublicAssetPath::resolve($data['photo'] ?? null);
        if ($photoPath) {
            $profile = Image::make($photoPath)->resize(1045, 1045);
            $img->insert($profile, 'center', 5, -390);
        }

        $fontPath = public_path('fonts/arial.ttf');

        $img->text($data['full_name'], 1100, 2090, function ($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(150);
            $font->color('#000');
            $font->align('center');
            $font->valign('top');
        });

        if (! empty($data['subtitle'])) {
            $img->text(trim($data['subtitle']), 1100, 2355, function ($font) use ($fontPath) {
                $font->file($fontPath);
                $font->size(150);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
        }

        if (! empty($data['id_number'])) {
            $idNumber = trim($data['id_number']);
            $idFontSize = 100;
            foreach ([[-2, 0], [2, 0], [0, -2], [0, 2], [-2, -2], [-2, 2], [2, -2], [2, 2]] as [$ox, $oy]) {
                $img->text($idNumber, 1090 + $ox, 1890 + $oy, function ($font) use ($fontPath, $idFontSize) {
                    $font->file($fontPath);
                    $font->size($idFontSize);
                    $font->color('#000');
                    $font->align('center');
                    $font->valign('top');
                });
            }
        }

        return $img;
    }

    /**
     * @param  array{
     *     qrcode:string,
     *     signature:?string,
     *     emergency_person:?string,
     *     emergency_relationship:?string,
     *     emergency_number:?string,
     *     birth_date:?string
     * }  $data
     */
    protected function composeIdCardBack($img, array $data)
    {
        $qrPng = QrCode::format('png')
            ->size(900)
            ->margin(0)
            ->generate($data['qrcode']);
        $qrImage = Image::make((string) $qrPng);
        $img->insert($qrImage, 'top-left', 655, 435);

        $signaturePath = PublicAssetPath::resolve($data['signature'] ?? null);
        if ($signaturePath) {
            $signature = Image::make($signaturePath)->resize(500, 600);
            $img->insert($signature, 'center', -30, 1200);
        }

        if (! empty($data['emergency_person'])) {
            $this->drawIdCardText($img, $data['emergency_person'], 1100, 1650, 100, '#000');
        }
        if (! empty($data['emergency_relationship'])) {
            $this->drawIdCardText($img, $data['emergency_relationship'], 1100, 1750, 100, '#000');
        }
        if (! empty($data['emergency_number'])) {
            $this->drawIdCardText($img, $data['emergency_number'], 1100, 1850, 100, '#000');
        }

        if (! empty($data['birth_date'])) {
            $formattedDate = Carbon::parse($data['birth_date'])->format('m-d-Y');
            $this->drawIdCardText($img, $formattedDate, 3000, 800, 300, '#000');
        }

        return $img;
    }
}
