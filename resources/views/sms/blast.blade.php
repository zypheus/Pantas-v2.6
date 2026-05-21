@extends('layouts.sec')

@section('content')

<div class="container mt-4">

    <h3>SMS Blast</h3>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('sms.send') }}">
        @csrf

        <div class="row mb-3">

            <div class="col-md-6">
                <label for="yearFilter">Filter by Year</label>
                <select name="year" id="yearFilter" class="form-control">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="courseFilter">Filter by Course</label>
                <select name="course" id="courseFilter" class="form-control">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course }}">{{ $course }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="alert alert-info">
            Recipients: <b id="recipientCount">Loading...</b> students (with mobile number on file)
        </div>

        <div class="mb-3">
            <label for="blastMessage">Message</label>
            <textarea
                id="blastMessage"
                name="message"
                class="form-control"
                rows="5"
                placeholder="Example: Hello {name}, please visit the library today."
                required
            ></textarea>
            <small class="text-muted">
                Available variables:<br><b>{name}</b> = Student full name
            </small>
        </div>

        <button type="submit" class="btn btn-primary">
            Send SMS
        </button>
    </form>

    <div class="mt-2">
        <a href="{{ route('sms.scan-message') }}" class="btn btn-secondary">
            Scan template &amp; targeted SMS
        </a>
    </div>

</div>

<script>
function updateRecipientCount() {
    const year = document.getElementById('yearFilter').value;
    const course = document.getElementById('courseFilter').value;
    const params = new URLSearchParams();
    if (year) params.append('year', year);
    if (course) params.append('course', course);

    fetch("{{ route('sms.count') }}?" + params.toString())
        .then(res => res.json())
        .then(data => {
            document.getElementById('recipientCount').innerText = data.count;
        })
        .catch(() => {
            document.getElementById('recipientCount').innerText = '?';
        });
}

document.getElementById('yearFilter').addEventListener('change', updateRecipientCount);
document.getElementById('courseFilter').addEventListener('change', updateRecipientCount);
window.addEventListener('load', updateRecipientCount);
</script>

@endsection
