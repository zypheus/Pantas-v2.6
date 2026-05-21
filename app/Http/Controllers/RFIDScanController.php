<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookLog;

class RFIDScanController extends Controller
{
    public function index()
    {
        return view('rfid.scanner');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'rfid' => 'required|exists:books,rfid',
        ]);

        $book = Book::where('rfid', $request->rfid)->first();

        if (!$book) {
            return response()->json(['error' => 'Book not found.'], 404);
        }

        // Check the last status of the book
        $lastLog = BookLog::where('book_id', $book->id)->latest()->first();
        if (!$lastLog || $lastLog->status === 'Checked In') {
            return response()->json(['alert' => 'Book is not yet checked out!'], 200);
        }

        return response()->json(['success' => 'Book is properly checked out.'], 200);
    }
}
