@extends('layouts.sec')
<link rel="stylesheet" href="{{ asset('css/books/index.css') }}">
@section('content')
<div class="container mt-5">
    <h2>Search Library of Congress</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('catalog.copy.loc-search') }}" method="POST" class="mt-4">
        @csrf

        <div class="mb-3">
            <label>ISBN</label>
            <input type="text" name="isbn" class="form-control" placeholder="Enter ISBN" value="{{ old('isbn') }}">
        </div>

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" placeholder="Enter title" value="{{ old('title') }}">
        </div>

        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>
@endsection
