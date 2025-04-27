<?php
require '../_base.php';

$_title = 'BeenChilling';
include '../_head.php';
?>

<div class="webcam-container">
    <div class="booth">
        <h2>Your video will be displayed here.</h2>
        <video id="video" width="100%" height="100%" autoplay></video>
        <div class="scan-overlay"></div>
    </div>

    <div class="button-group">
        <button id="startButton" class="button">Start Cam</button>
        <button id="stopButton" class="button">Stop Cam</button>
        <button id="scanQRBtn" class="button">Scan QR Code</button>
        <button id="captureBtn" class="button">Capture Image</button>
    </div>

    <div id="qr-result">
        <p>QR Code result: <span id="qrResult">No QR code detected yet</span></p>
    </div>

    <div class="qr-container">
        <h2>Generate QR Code</h2>
        <div class="qr-input">
            <input type="text" id="qrData" placeholder="Enter data for QR code">
            <button id="generateQRBtn" class="button">Generate QR</button>
        </div>
        <div id="qrCanvas"></div>
    </div>

    <div class="captured-image-container">
        <h2>Captured Image</h2>
        <canvas id="canvas" width="320" height="240"></canvas>
        <img id="capturedImage" alt="Captured Image" style="display: none;">
    </div>
</div>

<?php
include '../_foot.php';