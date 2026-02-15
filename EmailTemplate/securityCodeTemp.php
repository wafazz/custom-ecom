<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Code</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            font-family: Arial, Helvetica, sans-serif;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            padding: 20px 0;
        }
        .container {
            max-width: 480px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        }
        .header {
            padding: 24px;
            text-align: center;
            background: #0d6efd;
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
        }
        .content {
            padding: 28px;
            text-align: center;
            color: #333333;
        }
        .content p {
            margin: 0 0 16px;
            font-size: 15px;
            line-height: 1.6;
        }
        .code {
            display: inline-block;
            margin: 20px 0;
            padding: 14px 28px;
            font-size: 26px;
            letter-spacing: 4px;
            font-weight: bold;
            color: #0d6efd;
            background: #f1f5ff;
            border-radius: 8px;
        }
        .footer {
            padding: 18px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            background: #fafafa;
        }
        @media (max-width: 480px) {
            .content {
                padding: 22px;
            }
            .code {
                font-size: 22px;
                padding: 12px 22px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                Rozeyana Security
            </div>

            <div class="content">
                <p>Hello,</p>
                <p>Use the security code below to complete your verification.</p>

                <div class="code">
                    {{CODE}}
                </div>

                <p>This code will expire in <strong>5 minutes</strong>.</p>
                <p>If you didn’t request this, please ignore this email.</p>
            </div>

            <div class="footer">
                © <?= date('Y') ?> Rozeyana. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>