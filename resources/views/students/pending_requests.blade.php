@extends('layouts.sec')

@section('content')
<div class="container">
    <h3>Pending Edit Requests</h3>

    <ul class="nav nav-tabs mb-3" id="requestTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" >Pending ({{ $pending->total() }})</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab">Logs ({{ $logs->total() }})</button>
        </li>
    </ul>

    <div class="tab-content" id="requestTabsContent">
        <!-- Pending Requests -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            @include('students.partials.requests_table', ['requests' => $pending, 'showActions' => true])
            {{ $pending->links() }} 
        </div>

        <!-- Logs -->
        <div class="tab-pane fade" id="logs" role="tabpanel">
            @include('students.partials.requests_table', ['requests' => $logs, 'showActions' => false])
            {{ $logs->links() }}
        </div>
    </div>
</div>

<!-- Modal Preview -->
@foreach($pending as $req)
<div class="modal fade" id="previewModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Request Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Name:</strong> {{ $req->lastname }}, {{ $req->firstname }} {{ $req->middle_initial }}</p>
        <p><strong>Birthday:</strong> {{ $req->birthday ?? '-' }}</p>
        <p><strong>Program:</strong> {{ $req->program_id ?? '-'}}</p>
        <p><strong>Year:</strong> {{ $req->year ?? '-' }}</p>
        <p><strong>Mobile:</strong> {{ $req->mobile_number ?? '-' }}</p>
        <p><strong>Address:</strong> {{ $req->address ?? '-' }}</p>
        <p><strong>Emergency Contact:</strong> {{ $req->emergency_person ?? '-' }} ({{ $req->emergency_relationship ?? '-' }})</p>
        <p><strong>Emergency Number:</strong> {{ $req->emergency_number ?? '-' }}</p>
        <p><strong>Profile Picture:</strong></p>
        @if($req->profile_picture)
            @if($req->profile_picture)
                <div class="text-center">
                    <img src="{{ asset($req->profile_picture) }}"
                         alt="Profile Pic"
                         class="img-thumbnail"
                         style="max-width: 200px; max-height: 200px; object-fit: cover;">
                </div>
            @else
                <p>None</p>
            @endif
        @else
            <p>None</p>
        @endif
      </div>
      <div class="modal-footer">
        <form action="{{ route('admin.requests.approve', $req->id) }}" method="POST" class="me-2">
            @csrf
            <button class="btn btn-success btn-sm">Approve</button>
        </form>
        <form action="{{ route('admin.requests.reject', $req->id) }}" method="POST">
            @csrf
            <button class="btn btn-danger btn-sm">Reject</button>
        </form>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endsection
