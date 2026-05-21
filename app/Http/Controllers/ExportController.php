<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookLog;
use Carbon\Carbon;
use App\Models\Program;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ExportController extends Controller
{
    public function exportBooks(Request $request)
    {
        $fileName = 'books_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);
        $writer = SimpleExcelWriter::create($filePath);
    
        $writer->addRow(['Downloaded At' => Carbon::now()->format('Y-m-d H:i:s')]);
        $writer->addHeader(['Title', 'Author', 'Program', 'Course', 'Publication Year', 'Status']);
    
        $query = Book::query();
    
        if ($request->filled('program')) {
            $query->whereHas('programs', function ($q) use ($request) {
                $q->where('programs.id', $request->program);
            });
        }


        // ✅ pub_year year filter
        if ($request->filled('year_filter') && $request->filled('year1')) {
            $year1 = (int) $request->year1;
            $year2 = (int) $request->year2;
    
            switch ($request->year_filter) {
                case 'exact':
                    $query->where('pub_year', $year1);
                    break;
                case 'before':
                    $query->where('pub_year', '<=', $year1);
                    break;
                case 'after':
                    $query->where('pub_year', '>=', $year1);
                    break;
                case 'between':
                    if ($request->filled('year2')) {
                        $query->whereBetween('pub_year', [$year1, $year2]);
                    }
                    break;
            }
        }
    
        // ✅ Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title_statement', 'like', "%$search%")
                  ->orWhere('rfid', 'like', "%$search%")
                  ->orWhere('main_author', 'like', "%$search%")
                  ->orWhere('course', 'like', "%$search%")
                  ->orWhere('year', 'like', "%$search%");
            });
        }

        // ✅ Export only filtered data
        $query->orderBy('title_statement')
          ->chunk(500, function ($books) use ($writer) {
              foreach ($books as $book) {
                  $writer->addRow([
                      'Title' => $book->title_statement,
                      'Author' => $book->main_author,
                      'Program' => $book->programs->pluck('program_code')->join(', ') ?: 'N/A',
                      'Course' => $book->course,
                      'pub_year' => $book->pub_year,
                      'Status' => $book->availability,
                  ]);
              }
          });

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function exportTransactions()
    {
        $fileName = 'transactions_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);

        $writer = SimpleExcelWriter::create($filePath);
        $writer->addRow(['Downloaded At' => Carbon::now()->format('Y-m-d H:i:s')]);
        $writer->addHeader(['Patron Name', 'Barcode', 'Book Title', 'Author', 'RFID', 'Status', 'Loan type', 'Timestamp']);

        BookLog::with(['book', 'student'])->chunk(500, fn($logs) =>
            collect($logs)->each(fn($log) =>
                $writer->addRow([
                    'Patron Name' => $log->patronLabel(),
                    'Barcode' => $log->book->barcode ?? 'N/A',
                    'Book Title' => $log->book->title_statement ?? 'Unknown',
                    'Author' => $log->book->main_author ?? 'Unknown',
                    'RFID' => $log->book->rfid ?? 'N/A',
                    'Status' => $log->status,
                    'Loan type' => $log->circulationLabel(),
                    'Timestamp' => $log->timestamp
                        ? Carbon::parse($log->timestamp)->format('Y-m-d H:i:s')
                        : '',
                ])
            )
        );

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
