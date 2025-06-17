<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dart Sector Div</title>
  <style>
    body {
      background: #f0f0f0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .sector {
      width: 300px;
      height: 300px;
      background-color: white;
      position: relative;
      clip-path: polygon(50% 50%, 100% 0%, 100% 100%);
      transform: rotate(0deg); /* Change for different sectors */
      box-shadow: 0 0 10px rgba(0, 0, 255, 0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }

    .sector:hover {
      transform: scale(1.05) rotate(0deg);
      box-shadow: 0 0 20px rgba(0, 0, 255, 0.6);
    }

    .sector-content {
      position: absolute;
      top: 30%;
      left: 70%;
      transform: translate(-50%, -50%);
      text-align: center;
      font-weight: bold;
    }

    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #FDB813; /* Dart color */
      margin: 0 auto 5px;
    }
  </style>
</head>
<body>
  <div class="sector">
    <div class="sector-content">
      <div class="dot"></div>
      🟡<br>
      Dart
    </div>
  </div>
</body>
</html>

<?php
?>