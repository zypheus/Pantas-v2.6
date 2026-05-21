<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Program;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BookImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx|max:2048',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(storage_path('app/uploads'), $fileName);
        $fullPath = storage_path("app/uploads/{$fileName}");

        $extension = $file->getClientOriginalExtension();

        if ($extension === 'csv') {
            return $this->importCSV($fullPath);
        } elseif ($extension === 'xlsx') {
            return $this->importExcel($fullPath);
        } else {
            return back()->with('error', 'Invalid file format.');
        }
    }

    // ✅ Import CSV
    private function importCSV($filePath)
    {
        if (!file_exists($filePath)) {
            return back()->with('error', 'File does not exist.');
        }

        if (($handle = fopen($filePath, "r")) !== false) {
            $header = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);

                $book = Book::create([
                    'control_no'          => $data['Control No'] ?? null,
                    'date_time_stamp'     => $data['Date Time Stamp'] ?? null,
                    'fixed_length_data'   => $data['Fixed Length Data'] ?? null,
                    'isbn'                => $data['ISBN'] ?? null,
                    'price'               => $data['Price'] ?? null,
                    'cataloging_source_a' => $data['Cataloging Source A'] ?? null,
                    'cataloging_source_b' => $data['Cataloging Source B'] ?? null,
                    'cataloging_source_e' => $data['Cataloging Source E'] ?? null,
                    'main_author'         => $data['Main Author'] ?? null,
                    'title_statement'     => $data['Title Statement'] ?? null,
                    'title_author'        => $data['Title Author'] ?? null,
                    'edition'             => $data['Edition'] ?? null,
                    'pub_place'           => $data['Pub Place'] ?? null,
                    'publisher'           => $data['Publisher'] ?? null,
                    'pub_year'            => $data['Pub Year'] ?? null,
                    'pages'               => $data['Pages'] ?? null,
                    'illustrations'       => $data['Illustrations'] ?? null,
                    'size'                => $data['Size'] ?? null,
                    'volume'              => $data['Volume'] ?? null,
                    'content_type'        => $data['Content Type'] ?? null,
                    'content_code'        => $data['Content Code'] ?? null,
                    'media_type'          => $data['Media Type'] ?? null,
                    'media_code'          => $data['Media Code'] ?? null,
                    'carrier_type'        => $data['Carrier Type'] ?? null,
                    'carrier_code'        => $data['Carrier Code'] ?? null,
                    'series_title'        => $data['Series Title'] ?? null,
                    'general_note'        => $data['General Note'] ?? null,
                    'bibliography_note'   => $data['Bibliography Note'] ?? null,
                    'source_vendor'       => $data['Source Vendor'] ?? null,
                    'source_date'         => $data['Source Date'] ?? null,
                    'subject_topic'       => $data['Subject Topic'] ?? null,
                    'subject_form'        => $data['Subject Form'] ?? null,
                    'genre'               => $data['Genre'] ?? null,
                    'library_name'        => $data['Library Name'] ?? null,
                    'section'             => $data['Section'] ?? null,
                    'call_number'         => $data['Call Number'] ?? null,
                    'accession_no'        => $data['Accession No'] ?? null,
                    'created_at'          => $data['Created At'] ?? now(),
                    'updated_at'          => $data['Updated At'] ?? now(),
                    'barcode'             => $data['Barcode'] ?? null,
                    'rfid'                => $data['RFID'] ?? null,
                    'availability'        => 'Available',
                    'year'                => $data['Year'] ?? null,
                    'course'              => $data['Course'] ?? null,
                    'cover_image'         => $data['Cover Image'] ?? null,
                ]);

                // ✅ Handle programs (comma-separated values: e.g. "BSIT,BSCS")
                if (!empty($data['Program'])) {
                    $programs = array_map('trim', explode(',', $data['Program']));
                    $programIds = Program::whereIn('program_code', $programs)->pluck('id')->toArray();
                    $book->programs()->attach($programIds);
                }
            }
            fclose($handle);
        }

        return redirect()->back()->with('success', 'CSV file imported successfully!');
    }

    // ✅ Import Excel
    private function importExcel($filePath)
    {
        if (!file_exists($filePath)) {
            return back()->with('error', 'File does not exist.');
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $header = array_shift($rows);

        foreach ($rows as $row) {
            $book = Book::create([
                'control_no'          => $row['A'] ?? null,
                'date_time_stamp'     => $row['B'] ?? null,
                'fixed_length_data'   => $row['C'] ?? null,
                'isbn'                => $row['D'] ?? null,
                'price'               => $row['E'] ?? null,
                'cataloging_source_a' => $row['F'] ?? null,
                'cataloging_source_b' => $row['G'] ?? null,
                'cataloging_source_e' => $row['H'] ?? null,
                'main_author'         => $row['I'] ?? null,
                'title_statement'     => $row['J'] ?? null,
                'title_author'        => $row['K'] ?? null,
                'edition'             => $row['L'] ?? null,
                'pub_place'           => $row['M'] ?? null,
                'publisher'           => $row['N'] ?? null,
                'pub_year'            => $row['O'] ?? null,
                'pages'               => $row['P'] ?? null,
                'illustrations'       => $row['Q'] ?? null,
                'size'                => $row['R'] ?? null,
                'volume'              => $row['S'] ?? null,
                'content_type'        => $row['T'] ?? null,
                'content_code'        => $row['U'] ?? null,
                'media_type'          => $row['V'] ?? null,
                'media_code'          => $row['W'] ?? null,
                'carrier_type'        => $row['X'] ?? null,
                'carrier_code'        => $row['Y'] ?? null,
                'series_title'        => $row['Z'] ?? null,
                'general_note'        => $row['AA'] ?? null,
                'bibliography_note'   => $row['AB'] ?? null,
                'source_vendor'       => $row['AC'] ?? null,
                'source_date'         => $row['AD'] ?? null,
                'subject_topic'       => $row['AE'] ?? null,
                'subject_form'        => $row['AF'] ?? null,
                'genre'               => $row['AG'] ?? null,
                'library_name'        => $row['AH'] ?? null,
                'section'             => $row['AI'] ?? null,
                'call_number'         => $row['AJ'] ?? null,
                'accession_no'        => $row['AK'] ?? null,
                'created_at'          => $row['AL'] ?? now(),
                'updated_at'          => $row['AM'] ?? now(),
                'barcode'             => $row['AN'] ?? null,
                'rfid'                => $row['AO'] ?? null,
                'availability'        => 'Available',
                'year'                => $row['AP'] ?? null,
                'course'              => $row['AQ'] ?? null,
                'cover_image'         => $row['AS'] ?? null,
            ]);

            // ✅ Handle programs (column AR in Excel)
            if (!empty($row['AR'])) {
                $programs = array_map('trim', explode(',', $row['AR']));
                $programIds = Program::whereIn('program_code', $programs)->pluck('id')->toArray();
                $book->programs()->attach($programIds);
            }
        }

        return redirect()->back()->with('success', 'Excel file imported successfully!');
    }
}
