<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Program</th>
            <th>Year</th>
            <th>Status</th>
            <th>Requested At</th>
            @if($showActions ?? false)
                <th>Actions</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($requests as $req)
            <tr>
                <td>{{ $req->student->lastname }}, {{ $req->student->firstname }}</td>
                <td>{{ $req->student->course ?? '-' }}</td>
                <td>{{ $req->student->year ?? '-' }}</td>
                <td>
                    <span class="badge 
                        @if($req->status == 'pending') bg-warning
                        @elseif($req->status == 'approved') bg-success
                        @else bg-danger
                        @endif">{{ ucfirst($req->status) }}</span>
                </td>
                <td>{{ $req->created_at->format('M d, Y H:i') }}</td>
                @if($showActions ?? false)
                    <td>
                        <button class="btn btn-sm btn-info mb-1"
                            data-bs-toggle="modal"
                            data-bs-target="#previewModal{{ $req->id }}">
                            View
                        </button>
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="{{ ($showActions ?? false) ? 6 : 5 }}" class="text-center">No requests found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
