from flask import Flask, request, jsonify
import serial, time, threading

# ====== CONFIG ======
PORT = "COM10"        # Your GSM modem port
BAUD = 9600
API_KEY = "library123"  # Secret key so only Laravel can post
# ====================

app = Flask(__name__)

def send_command(ser, cmd, delay=1):
    ser.write((cmd + "\r").encode())
    time.sleep(delay)

def send_sms(ser, number, text):
    send_command(ser, "AT")
    send_command(ser, "AT+CMGF=1")
    ser.write(f'AT+CMGS="{number}"\r'.encode())
    time.sleep(1)
    ser.write(text.encode())
    ser.write(b"\x1A")
    time.sleep(5)

def send_batch(data):
    """Send SMS messages in a background thread"""
    ser = serial.Serial(PORT, BAUD, timeout=1)
    time.sleep(2)

    for item in data:
        number = item.get("number")
        message = item.get("message")
        if number and message:
            print(f"Sending to {number}: {message}")
            try:
                send_sms(ser, number, message)
            except Exception as e:
                print(f"Error sending to {number}: {e}")
            time.sleep(3)  # small delay to avoid modem issues

    ser.close()
    print("All messages sent.")

@app.route("/send-sms", methods=["POST"])
def sms():
    auth = request.headers.get("X-API-KEY")
    if auth != API_KEY:
        return jsonify({"error": "Unauthorized"}), 401

    data = request.get_json()
    if not data or not isinstance(data, list):
        return jsonify({"error": "Invalid payload"}), 400

    # Run SMS sending in a background thread
    threading.Thread(target=send_batch, args=(data,)).start()

    # Immediately respond to Laravel to avoid timeout
    return jsonify({"status": "accepted"}), 202

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)