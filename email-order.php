<?php
// email-order.php

function getEmailTemplate($data) {
    ob_start(); // Start output buffering
    ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Email</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }
    .email-container {
      max-width: 600px;
      margin: auto;
      background-color: #ffffff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .header {
      background-color: #3b82f6;
      color: #ffffff;
      padding: 20px;
      text-align: center;
    }

    .logo{
        display: block;
        max-width: 150px;
        margin-left: auto;
        margin-right: auto;
    }
    .content {
      padding: 30px 20px;
      color: #333333;
      line-height: 1.6;
    }
    .button {
      display: inline-block;
      padding: 12px 24px;
      margin: 20px 0;
      background-color: #3b82f6;
      color: #ffffff;
      text-decoration: none;
      border-radius: 5px;
    }
    .footer {
      background-color: #f4f4f4;
      padding: 20px;
      text-align: center;
      font-size: 12px;
      color: #888888;
    }
    @media screen and (max-width: 600px) {
      .content {
        padding: 20px 15px;
      }
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
        <img src="https://rozeyana.com/assets/images/LOGO-ROZYANA-06.png" class="logo">
      <h1>Thank You for Your Order!</h1>
    </div>
    <div class="content">
      <p>Hi <strong><?= htmlspecialchars($data['CustomerName']) ?></strong>,</p>
      <p>We're happy to let you know that your order <strong>#<?= htmlspecialchars($data['OrderID']) ?></strong> has been received and is now being processed.</p>
      <p>We'll notify you again once your items are shipped.</p>
      <p>
        <a href="<?= htmlspecialchars($data['OrderLink']) ?>" class="button">View Your Order</a>
      </p>
      <p>Thanks again for shopping with us!</p>
      <p>Rozeyana.com Team</p>
    </div>
    <div class="footer">
      &copy; <?= date('Y') ?> Rozeyana.com. All rights reserved.
    </div>
  </div>
</body>
</html>
<?php
    return ob_get_clean(); // Return buffer contents as string
}
?>
