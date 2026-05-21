<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;

/**
 * Faculty ID cards — same templates as student IDs, mapped to Employee fields.
 */
class EmployeeIdCardController extends Controller
{
    private function drawText($img, $text, $x, $y, $size, $color = '#000', $align = 'center', $valign = 'top')
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
        } else {
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
    }

    public function front($id)
    {
        $employee = Employee::findOrFail($id);
        $img = Image::make(base_path('images/id_templates/front.png'));

        $picPath = $employee->formal_picture;
        if ($picPath && file_exists(base_path($picPath))) {
            $profile = Image::make(base_path($picPath))->resize(260, 260);
            $img->insert($profile, 'center', 120, -195);
        }

        $fullName = trim("{$employee->firstname} {$employee->lastname}");
        $fontSize = 35;
        $img->text($fullName, 280, 655, function ($font) use ($fontSize) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size($fontSize);
            $font->color('#05014a');
            $font->align('center');
            $font->valign('top');
        });

        if ($employee->department) {
            $courseFontSize = 25;
            $img->text(trim($employee->department), 280, 705, function ($font) use ($courseFontSize) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size($courseFontSize);
                $font->color('#05014a');
                $font->align('center');
                $font->valign('top');
            });
        }

        $idNum = $employee->employee_number ?: $employee->employee_id;
        if ($idNum) {
            $idFontSize = 35;
            $img->text(trim($idNum), 135, 345, function ($font) use ($idFontSize) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size($idFontSize);
                $font->color('#fff');
                $font->align('center');
                $font->valign('top');
            });
        }

        return $img->response('png');
    }

    public function back($id)
    {
        $employee = Employee::findOrFail($id);
        $img = Image::make(base_path('images/id_templates/back.png'));

        $qrValue = $employee->qrcode ?: ('E-'.$employee->id);
        $qrPng = QrCode::format('png')->size(935)->margin(0)->generate($qrValue);
        $qrImage = Image::make((string) $qrPng);

        if ($employee->birth_date) {
            $formattedDate = Carbon::parse($employee->birth_date)->format('m-d-Y');
            $this->drawText($img, $formattedDate, 3000, 800, 300, '#000');
        }

        if ($employee->employee_signature && file_exists(base_path($employee->employee_signature))) {
            $signature = Image::make(base_path($employee->employee_signature))->resize(1300, 600);
            $img->insert($signature, 'center', 0, 1150);
        }

        $img->insert($qrImage, 'top-left', 630, 400);

        return $img->response('png');
    }

    public function download($id)
    {
        $employee = Employee::findOrFail($id);
        $front = $this->front($id)->getContent();
        $back = $this->back($id)->getContent();

        $zipPath = storage_path("app/temp_emp_id_{$id}.zip");
        $frontPath = storage_path("app/emp_front_{$id}.png");
        $backPath = storage_path("app/emp_back_{$id}.png");

        file_put_contents($frontPath, $front);
        file_put_contents($backPath, $back);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($frontPath, "{$employee->lastname}_{$employee->firstname}_front.png");
            $zip->addFile($backPath, "{$employee->lastname}_{$employee->firstname}_back.png");
            $zip->close();
        }

        unlink($frontPath);
        unlink($backPath);

        return response()->download($zipPath, "{$employee->lastname}_{$employee->firstname}_ID.zip")
            ->deleteFileAfterSend(true);
    }
}
