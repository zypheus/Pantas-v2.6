<!DOCTYPE html>
<html>
<head>
    <title>Edit Prospectus</title>
    <link rel="stylesheet" href="{{ asset('css/prospectus/edit.css') }}">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <h3>Edit Subject</h3>
    <form method="POST" action="{{ route('prospectus.update', $entry->id) }}" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Course</label>
            <input type="text" name="course" value="{{ $entry->course }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Year</label>
            <input type="text" name="year" value="{{ $entry->year }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" value="{{ $entry->subject }}" class="form-control" required>
        </div>
        <button class="btn btn-success">Save Changes</button>
        <a href="{{ route('prospectus.index', ['course' => $entry->course]) }}" class="btn btn-danger">Cancel</a>
    </form>
</div>
</body>
</html>
