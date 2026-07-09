<?php

namespace App\Http\Controllers;

use App\Models\BookLog;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FineClearanceController extends Controller
{
    public function index(Request $request)
    {
        $base = $this->outstandingQuery($request);

        $logs = (clone $base)
            ->with(['book', 'student', 'clearedBy'])
            ->orderByDesc('returned_date')
            ->paginate(20)
            ->withQueryString();

        $totalOutstanding = round((float) (clone $base)->sum(DB::raw('COALESCE(fine_balance, fine_incurred)')), 2);

        return view('admin.fines_outstanding', compact('logs', 'totalOutstanding'));
    }

    public function clear(Request $request, BookLog $bookLog, AdminActivityLogger $activities)
    {
        $request->validate([
            'fine_clearance_type' => 'required|string|in:paid,waived',
            'fine_clearance_amount' => 'required|numeric|min:0.01',
            'fine_clearance_note' => 'nullable|string|max:2000',
        ]);

        if ($bookLog->status !== 'Checked In'
            || (float) $bookLog->fine_incurred <= 0
            || $bookLog->fine_cleared_at !== null) {
            return back()->with('error', 'This fine cannot be cleared (invalid status or already cleared).');
        }

        $remaining = $bookLog->fine_balance !== null ? (float) $bookLog->fine_balance : (float) $bookLog->fine_incurred;
        $amount = round((float) $request->fine_clearance_amount, 2);

        if ($amount <= 0 || $amount > $remaining) {
            return back()->with('error', 'Invalid amount. It must be greater than 0 and not exceed the remaining fine.');
        }

        // Ensure original/balance are initialized for legacy rows
        if ($bookLog->fine_original === null) {
            $bookLog->fine_original = $bookLog->fine_incurred;
        }
        if ($bookLog->fine_balance === null) {
            $bookLog->fine_balance = $bookLog->fine_incurred;
        }
        if ($bookLog->fine_paid_total === null) {
            $bookLog->fine_paid_total = 0;
        }
        if ($bookLog->fine_waived_total === null) {
            $bookLog->fine_waived_total = 0;
        }

        $newBalance = round(((float) $bookLog->fine_balance) - $amount, 2);
        if ($newBalance < 0) {
            $newBalance = 0.0;
        }

        if ($request->fine_clearance_type === 'paid') {
            $bookLog->fine_paid_total = round(((float) $bookLog->fine_paid_total) + $amount, 2);
        } else {
            $bookLog->fine_waived_total = round(((float) $bookLog->fine_waived_total) + $amount, 2);
        }

        $bookLog->fine_balance = $newBalance;
        $bookLog->fine_clearance_type = $request->fine_clearance_type;
        $bookLog->fine_clearance_note = $request->fine_clearance_note;
        $bookLog->fine_cleared_by = $request->user()->id;

        if ($newBalance <= 0) {
            $bookLog->fine_cleared_at = now();
        }

        $bookLog->save();
        $activities->log('library', 'fine.cleared', 'Fine clearance recorded', (string) $amount, $bookLog);

        if ($newBalance <= 0) {
            return back()->with('success', 'Fine fully cleared as '.($request->fine_clearance_type === 'paid' ? 'paid' : 'waived').'.');
        }

        return back()->with('success', 'Partial '.($request->fine_clearance_type === 'paid' ? 'payment' : 'waiver').' recorded. Remaining: ₱'.number_format($newBalance, 2).'.');
    }

    protected function outstandingQuery(Request $request)
    {
        $q = BookLog::query()
            ->where('status', 'Checked In')
            ->where(function ($w) {
                $w->where('fine_balance', '>', 0)
                    ->orWhere(function ($w2) {
                        $w2->whereNull('fine_balance')->where('fine_incurred', '>', 0);
                    });
            })
            ->whereNull('fine_cleared_at');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $q->where(function ($outer) use ($s) {
                $outer->where('patron_name', 'like', '%'.$s.'%')
                    ->orWhereHas('student', function ($q2) use ($s) {
                        $q2->where('id_number', 'like', '%'.$s.'%')
                            ->orWhere('firstname', 'like', '%'.$s.'%')
                            ->orWhere('lastname', 'like', '%'.$s.'%');
                    })
                    ->orWhereHas('book', function ($q3) use ($s) {
                        $q3->where('title_statement', 'like', '%'.$s.'%')
                            ->orWhere('barcode', 'like', '%'.$s.'%');
                    });
            });
        }

        return $q;
    }
}
