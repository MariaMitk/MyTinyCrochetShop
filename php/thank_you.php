<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thank You - My Tiny Crochet Shop</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8f6f2; }
    .thank-you-container {
      max-width: 600px;
      margin: 100px auto;
      padding: 3rem;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .message { font-size: 1.2rem; color: #333; margin: 1rem 0; }
    .btn { 
      display: inline-block;
      background: #A67A6C;
      color: white;
      padding: 0.8rem 1.5rem;
      border-radius: 6px;
      text-decoration: none;
      margin-top: 1.5rem;
    }
    .btn:hover { opacity: 0.9; }
  </style>
</head>
<body>
  <div class="thank-you-container">
    <?php
    if (isset($_SESSION['form_message'])) {
        echo '<div class="message">' . $_SESSION['form_message'] . '</div>';
        unset($_SESSION['form_message']);
        unset($_SESSION['form_status']);
    } else {
        echo '<h2>Thank You!</h2>';
        echo '<p class="message">Your subscription has been processed successfully.</p>';
    }
    ?>
    <a href="index.html" class="btn">Return to Home</a>
  </div>
</body>
</html>