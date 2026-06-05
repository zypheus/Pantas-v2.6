<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ComposesLibraryIdCard;
use App\Models\Employee;
use ZipArchive;

/**
 * Faculty & staff ID cards — same layout and templates as student IDs.
 */
class EmployeeIdCardController extends Controller
{
    use ComposesLibraryIdCard;

    public function front($id)
    {
        $employee = Employee::findOrFail($id);
        $img = $this->idCardTemplate('front');

        $subtitle = $employee->department
            ?: $employee->program
            ?: $employee->designation
            ?: $employee->position;

        $this->composeIdCardFront($img, [
            'photo' => $employee->formal_picture,
            'full_name' => trim("{$employee->firstname} {$employee->lastname}"),
            'subtitle' => $subtitle,
            'id_number' => $employee->employee_id ?: $employee->employee_number,
        ]);

        return $img->response('png');
    }

    public function back($id)
    {
        $employee = Employee::findOrFail($id);
        $img = $this->idCardTemplate('back');

        $this->composeIdCardBack($img, [
            'qrcode' => $employee->qrcode ?: ('E-'.$employee->id),
            'signature' => $employee->employee_signature,
            'emergency_person' => $employee->emergency_contact_name,
            'emergency_relationship' => $employee->emergency_contact_relationship,
            'emergency_number' => $employee->emergency_contact_number,
            'birth_date' => $employee->birth_date,
        ]);

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
