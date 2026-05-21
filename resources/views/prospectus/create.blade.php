<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course to Prospectus</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/prospectus/create.css') }}">
</head>
<body>

<div class="custom-modal">
    <div class="custom-title text-center" style="color:#932c27;">Add Course to Prospectus</div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger mt-2">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prospectus.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <input type="text" name="course" class="form-control custom-input" placeholder="Course" required>
        </div>

        <div class="mb-4">
            <label class="form-label">Select Year Levels:</label>
            @foreach(['1st Year', '2nd Year', '3rd Year', '4th Year'] as $yr)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="years[]" value="{{ $yr }}" id="year_{{ $loop->index }}">
                    <label class="form-check-label" for="year_{{ $loop->index }}">{{ $yr }}</label>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('prospectus.index') }}" class="btn btn-secondary">⬅ Back</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

</body>
</html>
