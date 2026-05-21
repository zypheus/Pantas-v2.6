@extends('layouts.sec')

<link rel="stylesheet" href="{{ asset('css/books/index.css') }}">
@section('content')
<div class="container mt-5">
    <h2>Review LoC Record</h2>

    <form action="{{ route('catalog.copy.loc-store') }}" method="POST" class="mt-4">
        @csrf

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="{{ $record['title'] }}" required>
        </div>

        <div class="mb-3">
            <label>Author</label>
            <input type="text" name="author" class="form-control" value="{{ $record['author'] }}">
        </div>

        <div class="mb-3">
            <label>Publisher</label>
            <input type="text" name="publisher" class="form-control" value="{{ $record['publisher'] }}">
        </div>

        <div class="mb-3">
            <label>Year</label>
            <input type="text" name="year" class="form-control" value="{{ $record['year'] }}">
        </div>

        <div class="mb-3">
            <label>ISBN</label>
            <input type="text" name="isbn" class="form-control" value="{{ $record['isbn'] }}">
        </div>

        <div class="mb-3">
            <label>Call Number</label>
            <input type="text" name="call_number" class="form-control" value="{{ $record['call_number'] }}">
        </div>

        <button type="submit" class="btn btn-success">Copy to Library</button>
    </form>
</div>
@endsection
