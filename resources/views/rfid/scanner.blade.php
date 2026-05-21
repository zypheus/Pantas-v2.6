<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="{{ asset('css/rfid/scanner.css') }}">
  <title>RFID Scanner</title>
  <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"/>
</head>

<body>

  <h1 style="color: white;"> RFID Scanner</h1>

  <div class="scanner-container">
    <form action="{{ route('sms.send') }}" method="POST">
        @csrf
        <input type="text" name="number" placeholder="09xxxxxxxxx" class="form-control mb-2">
        <textarea name="message" placeholder="Your message" class="form-control mb-2"></textarea>
        <button class="btn btn-primary">Send SMS</button>
    </form>


    <div class="alert d-none text-center" id="alertMessage"></div>

    <input type="hidden" id="rfidInput" class="form-control text-center mt-4" placeholder="Scan RFID Tag" autofocus>

    <audio id="alertSound" src="{{ asset('sounds/alert.wav') }}" type="audio/wav"></audio>

    <div class="scanner-box">
      <div class="corner top-left"></div>
      <div class="corner top-right"></div>
      <div class="corner bottom-left"></div>
      <div class="corner bottom-right"></div>
      <div class="scanner-line"></div>
      <div class="scanner-text">Waiting for RFID</div>
    </div>

    <div class="d-flex justify-content-center flex-wrap">
      <a href="{{ route('book.index') }}"class="btn"  >⬅ Back to List </a>
    </div>
     

    <div id="message"></div>
  </div>
  
  
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      let inputField = document.getElementById('rfidInput');
      let alertBox = document.getElementById('alertMessage');
      let alertSound = document.getElementById('alertSound');
      let rfidBuffer = "";

      document.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
          event.preventDefault();
          if (rfidBuffer.trim() !== "") {
            sendRFID(rfidBuffer);
            rfidBuffer = "";
          }
        } else {
          rfidBuffer += event.key;
        }
      });

      function sendRFID(rfid) {
        fetch("{{ route('rfid.scan') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ rfid: rfid })
        })
        .then(response => response.json())
        
        .then(data => {
          if (data.alert) {
            alertBox.classList.remove('d-none');
            alertBox.textContent = data.alert;
            alertSound.play();
          } else {
            alertBox.classList.add('d-none');
          }
        })
        .catch(error => console.error('Error:', error));
      }
    });

    function showAlert() {
      document.getElementById('message').innerText = 'Alert message';
    }

    function clearMessage() {
      document.getElementById('message').innerText = '';
    }
    
    console.log("{{ asset('sounds/alert.wav') }}");
  </script>
  


</body>
</html>
