<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';



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
      <h1>Thank You for Your Order!</h1>
    </div>
    <div class="content">
      <p>Hi <strong>{{CustomerName}}</strong>,</p>
      <p>We're happy to let you know that your order <strong>#{{OrderID}}</strong> has been received and is now being processed.</p>
      <p>We’ll notify you again once your items are shipped.</p>
      <p>
        <a href="{{OrderID}}order-details/{{OrderID}}" class="button">View Your Order</a>
      </p>
      <p>Thanks again for shopping with us!</p>
      <p>— The Team</p>
    </div>
    <div class="footer">
      &copy; {{currentyear}} Rozeyana.com. All rights reserved.
    </div>
  </div>
</body>
</html>