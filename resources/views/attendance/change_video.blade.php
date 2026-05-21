@extends('layouts.sec')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/books/index.css') }}">

@endsection
@section('content')

    <div class="container py-4">
        <h2 class="mb-4">Change Attendance Video</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Current Video -->
        <div class="mb-4">
            <video id="currentVideo" muted autoplay loop controls style="width:100%; max-width:700px;">
                <source src="{{ asset('videos/area51_product_slideshow.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <!-- Upload Form -->
        <form id="uploadForm" method="POST" action="{{ route('attendance.uploadVideo') }}" enctype="multipart/form-data" class="d-flex flex-column gap-3" style="max-width:500px;">
            @csrf
            <input type="file" name="video" id="videoUpload" accept="video/mp4" required>
            <button type="submit" class="btn btn-primary">Upload Video (Max 500MB)</button>
        </form>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index:9999; text-align:center;">
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:white; font-size:24px; font-weight:bold;">
            Uploading...
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Hamburger Toggle
        const toggleBtn = document.getElementById('customMenuToggle');
        const closeBtn = document.getElementById('customMenuClose');
        const routeWrapper = document.getElementById('routeWrapper');

        toggleBtn.addEventListener('click', () => { routeWrapper.classList.add('open'); });
        closeBtn.addEventListener('click', () => { routeWrapper.classList.remove('open'); });
        window.addEventListener('resize', () => { if(window.innerWidth >= 768){ routeWrapper.classList.remove('open'); } });

        // Video size check
        const videoInput = document.getElementById('videoUpload');
        videoInput.addEventListener('change', function() {
            if(this.files[0]){
                if(this.files[0].size > 500 * 1024 * 1024){
                    alert("File is too large! Maximum allowed size is 500MB.");
                    this.value = "";
                }
            }
        });

        // Show loading overlay on submit
        const uploadForm = document.getElementById('uploadForm');
        const loadingOverlay = document.getElementById('loadingOverlay');

        uploadForm.addEventListener('submit', function() {
            if(videoInput.files.length === 0) return false; // prevent submit if no file
            loadingOverlay.style.display = 'block';
        });
    </script>
@endsection