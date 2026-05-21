<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Subject</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
</head>
<body>

<div class="container mt-5" style="max-width: 600px;">
    <h3 class="text-center" style="color: #932c27;">Add Subject to {{ $course }} - {{ $year }}</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prospectus.storeSubject') }}" method="POST">
        @csrf
        <input type="hidden" name="course" value="{{ $course }}">
        <input type="hidden" name="year" value="{{ $year }}">

        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="Enter Subject Name" required>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('prospectus.index', ['course' => $course]) }}" class="btn btn-secondary">⬅ Back</a>
            <button type="submit" class="btn btn-primary">Add Subject</button>
        </div>
    </form>
</div>

</body>
</html>
