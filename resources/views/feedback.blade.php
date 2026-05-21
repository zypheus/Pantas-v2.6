<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UA&P Feedback</title>

    {{-- Use Laravel asset() helper for CSS --}}
    <link rel="stylesheet" href="{{ asset('css/feedback.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/uap.png') }}">

    <style>
        /* ✅ Success Popup Styles */
        .feedback-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            z-index: 9999;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .feedback-popup.show {
            opacity: 1;
            transform: translateY(0);
        }

        .feedback-popup.fade-out {
            opacity: 0;
            transform: translateY(-10px);
        }

        .feedback-popup i {
            color: white;
            font-size: 1.3em;
        }
    </style>
</head>
<body>

<header>
    <div class="main-nav-bar">
        

        <div class="page-wrapper">
            <div class="feedback-container">
                <h2>Your Feedback Matters!</h2>
                <p>We'd love to hear about your experience.</p>

                <form method="POST" action="{{ route('feedback.store') }}">
                    @csrf
                    <label>Name (Optional):</label>
                    <input type="text" name="name" placeholder="Enter your name">

                    <label>Email (Optional):</label>
                    <input type="email" name="email" placeholder="Enter your email">

                    <label>Comments:</label>
                    <textarea name="comments" placeholder="Share your detailed feedback here..." required></textarea>

                    <button type="submit">Submit Feedback</button>
                </form>
            </div>
        </div>
    </div>
</header>

@if(session('success'))
    <div class="feedback-popup" id="feedbackPopup">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const popup = document.getElementById('feedbackPopup');
        if (popup) {
            // Show popup
            setTimeout(() => popup.classList.add('show'), 100);

            // Fade out after 3 seconds
            setTimeout(() => {
                popup.classList.add('fade-out');
                // Remove it from DOM after fade-out
                setTimeout(() => popup.remove(), 500);
            }, 3000);
        }
    });
</script>

</body>
</html>
