@extends('layouts.sidebar')

@section('title', 'Change Attendance Video')

@section('header')
    <div class="change-video-header">
        <div>
            <span class="change-video-kicker">Attendance scanner</span>
            <h1>Change Attendance Video</h1>
            <p>Update the MP4 video shown beside the attendance scanner kiosk.</p>
        </div>

        <a href="{{ route('attendance.scan') }}" class="change-video-open-scanner" target="_blank" rel="noopener">
            <i class="bi bi-upc-scan" aria-hidden="true"></i>
            <span>Open scanner</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="change-video-shell">
        @if (session('success'))
            <div class="alert alert-success change-video-alert" role="alert">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger change-video-alert" role="alert">
                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                <div>
                    <strong>Upload failed.</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="change-video-grid">
            <section class="change-video-card change-video-preview-card">
                <div class="change-video-card-header">
                    <div>
                        <span class="change-video-card-kicker">Current media</span>
                        <h2>Scanner video preview</h2>
                    </div>
                    <span class="change-video-status">
                        <i class="bi bi-play-circle-fill" aria-hidden="true"></i>
                        Active
                    </span>
                </div>

                <div class="change-video-frame">
                    <video id="currentVideo" muted autoplay loop controls playsinline>
                        <source src="{{ asset('videos/area51_product_slideshow.mp4') }}?v={{ now()->timestamp }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <div class="change-video-meta">
                    <div>
                        <span>File name</span>
                        <strong>area51_product_slideshow.mp4</strong>
                    </div>
                    <div>
                        <span>Format</span>
                        <strong>MP4 only</strong>
                    </div>
                </div>
            </section>

            <section class="change-video-card">
                <div class="change-video-card-header">
                    <div>
                        <span class="change-video-card-kicker">Replacement</span>
                        <h2>Upload new video</h2>
                    </div>
                    <span class="change-video-limit">Max 500 MB</span>
                </div>

                <p class="change-video-copy">
                    Choose one MP4 file. Uploading replaces the current scanner video immediately after the request completes.
                </p>

                <form id="uploadForm" method="POST" action="{{ route('attendance.uploadVideo') }}" enctype="multipart/form-data">
                    @csrf

                    <label class="change-video-dropzone" for="videoUpload">
                        <span class="change-video-drop-icon">
                            <i class="bi bi-cloud-arrow-up" aria-hidden="true"></i>
                        </span>
                        <span class="change-video-drop-title">Select an MP4 video</span>
                        <span class="change-video-drop-help">Click to browse. Large files may take a while to upload.</span>
                        <input type="file" name="video" id="videoUpload" accept="video/mp4" required>
                    </label>

                    <div id="selectedVideo" class="change-video-selected" hidden>
                        <i class="bi bi-file-earmark-play" aria-hidden="true"></i>
                        <div>
                            <strong id="selectedVideoName">No file selected</strong>
                            <span id="selectedVideoSize"></span>
                        </div>
                    </div>

                    <div class="change-video-actions">
                        <button type="submit" class="btn change-video-submit">
                            <i class="bi bi-upload" aria-hidden="true"></i>
                            Upload video
                        </button>
                        <button type="button" id="clearVideoUpload" class="btn change-video-clear">
                            Clear
                        </button>
                    </div>
                </form>

                <div class="change-video-note">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                    <span>Use an optimized MP4 for faster kiosk loading and smoother playback.</span>
                </div>
            </section>
        </div>
    </div>

    <div id="loadingOverlay" class="change-video-overlay" hidden>
        <div class="change-video-uploading" role="status" aria-live="polite">
            <div class="change-video-spinner" aria-hidden="true"></div>
            <strong>Uploading video</strong>
            <span>Please keep this page open.</span>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .change-video-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .change-video-kicker,
        .change-video-card-kicker {
            display: inline-flex;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            line-height: 1rem;
            text-transform: uppercase;
        }

        .change-video-header h1 {
            margin: 0.2rem 0 0;
            color: #0f172a;
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 2rem;
            letter-spacing: 0;
        }

        .change-video-header p,
        .change-video-copy {
            margin: 0.35rem 0 0;
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .change-video-open-scanner {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            min-height: 2.6rem;
            padding: 0.65rem 0.95rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            background: #ffffff;
            color: #0f172a;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .change-video-open-scanner:hover,
        .change-video-open-scanner:focus {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1e3a8a;
            text-decoration: none;
        }

        .change-video-shell {
            max-width: 1180px;
            margin: 0 auto;
        }

        .change-video-alert {
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
            border-radius: 0.9rem;
        }

        .change-video-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.75fr);
            gap: 1rem;
            align-items: start;
        }

        .change-video-card {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .change-video-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.1rem 1.15rem;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .change-video-card-header h2 {
            margin: 0.2rem 0 0;
            color: #0f172a;
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1.35rem;
        }

        .change-video-status,
        .change-video-limit {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.55rem;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: 0.76rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .change-video-limit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .change-video-frame {
            background: #020617;
            padding: 0.75rem;
        }

        .change-video-frame video {
            width: 100%;
            aspect-ratio: 16 / 9;
            border-radius: 0.75rem;
            background: #000;
            object-fit: contain;
        }

        .change-video-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            padding: 1rem 1.15rem;
        }

        .change-video-meta div {
            border: 1px solid #e2e8f0;
            border-radius: 0.8rem;
            background: #f8fafc;
            padding: 0.8rem;
        }

        .change-video-meta span,
        .change-video-selected span {
            display: block;
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .change-video-meta strong,
        .change-video-selected strong {
            display: block;
            margin-top: 0.15rem;
            color: #0f172a;
            font-size: 0.9rem;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .change-video-card form,
        .change-video-copy,
        .change-video-note {
            margin: 1rem 1.15rem 0;
        }

        .change-video-dropzone {
            display: grid;
            place-items: center;
            min-height: 14rem;
            padding: 1.2rem;
            border: 2px dashed #93c5fd;
            border-radius: 1rem;
            background: #eff6ff;
            color: #1e3a8a;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.15s ease, background-color 0.15s ease, transform 0.15s ease;
        }

        .change-video-dropzone:hover,
        .change-video-dropzone:focus-within {
            border-color: #2563eb;
            background: #dbeafe;
            transform: translateY(-1px);
        }

        .change-video-dropzone input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .change-video-drop-icon {
            display: grid;
            place-items: center;
            width: 3.4rem;
            height: 3.4rem;
            margin-bottom: 0.75rem;
            border-radius: 1rem;
            background: #1e3a8a;
            color: #ffffff;
            font-size: 1.6rem;
            box-shadow: 0 12px 24px rgba(30, 58, 138, 0.22);
        }

        .change-video-drop-title {
            display: block;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 800;
        }

        .change-video-drop-help {
            display: block;
            max-width: 18rem;
            margin-top: 0.35rem;
            color: #475569;
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .change-video-selected {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-top: 0.85rem;
            padding: 0.8rem;
            border: 1px solid #bfdbfe;
            border-radius: 0.8rem;
            background: #eff6ff;
        }

        .change-video-selected i {
            display: grid;
            place-items: center;
            width: 2.3rem;
            height: 2.3rem;
            border-radius: 0.7rem;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 1.2rem;
            flex: 0 0 auto;
        }

        .change-video-actions {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .change-video-submit,
        .change-video-clear {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 2.6rem;
            border-radius: 0.75rem;
            font-weight: 800;
        }

        .change-video-submit {
            border-color: #1e3a8a;
            background: #1e3a8a;
            color: #ffffff;
            flex: 1 1 12rem;
        }

        .change-video-submit:hover,
        .change-video-submit:focus {
            background: #172554;
            color: #ffffff;
        }

        .change-video-clear {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .change-video-note {
            display: flex;
            gap: 0.55rem;
            align-items: flex-start;
            margin-bottom: 1.15rem;
            padding: 0.85rem;
            border-radius: 0.85rem;
            background: #fffbeb;
            color: #92400e;
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .change-video-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            background: rgba(15, 23, 42, 0.68);
            backdrop-filter: blur(4px);
        }

        .change-video-overlay[hidden] {
            display: none;
        }

        .change-video-uploading {
            display: grid;
            justify-items: center;
            gap: 0.55rem;
            min-width: min(100%, 20rem);
            padding: 1.4rem;
            border-radius: 1rem;
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.24);
        }

        .change-video-uploading span {
            color: #64748b;
            font-size: 0.9rem;
        }

        .change-video-spinner {
            width: 2.3rem;
            height: 2.3rem;
            border: 4px solid #dbeafe;
            border-top-color: #1e3a8a;
            border-radius: 999px;
            animation: change-video-spin 0.8s linear infinite;
        }

        @keyframes change-video-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 992px) {
            .change-video-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .change-video-meta {
                grid-template-columns: 1fr;
            }

            .change-video-card-header {
                flex-direction: column;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const maxBytes = 500 * 1024 * 1024;
            const videoInput = document.getElementById('videoUpload');
            const uploadForm = document.getElementById('uploadForm');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const selectedVideo = document.getElementById('selectedVideo');
            const selectedVideoName = document.getElementById('selectedVideoName');
            const selectedVideoSize = document.getElementById('selectedVideoSize');
            const clearButton = document.getElementById('clearVideoUpload');

            if (!videoInput || !uploadForm || !loadingOverlay || !selectedVideo || !selectedVideoName || !selectedVideoSize || !clearButton) return;

            function formatBytes(bytes) {
                if (!bytes) return '0 MB';
                const mb = bytes / (1024 * 1024);
                return `${mb.toFixed(mb >= 10 ? 0 : 1)} MB`;
            }

            function clearSelection() {
                videoInput.value = '';
                selectedVideo.hidden = true;
                selectedVideoName.textContent = 'No file selected';
                selectedVideoSize.textContent = '';
            }

            videoInput.addEventListener('change', function () {
                const file = this.files[0];

                if (!file) {
                    clearSelection();
                    return;
                }

                if (file.type !== 'video/mp4' && !file.name.toLowerCase().endsWith('.mp4')) {
                    alert('Only MP4 video files are allowed.');
                    clearSelection();
                    return;
                }

                if (file.size > maxBytes) {
                    alert('File is too large. Maximum allowed size is 500 MB.');
                    clearSelection();
                    return;
                }

                selectedVideoName.textContent = file.name;
                selectedVideoSize.textContent = formatBytes(file.size);
                selectedVideo.hidden = false;
            });

            clearButton.addEventListener('click', clearSelection);

            uploadForm.addEventListener('submit', function (event) {
                if (videoInput.files.length === 0) {
                    event.preventDefault();
                    return;
                }

                loadingOverlay.hidden = false;
            });
        });
    </script>
@endpush
