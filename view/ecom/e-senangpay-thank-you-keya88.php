<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #ffffff;
            max-width: 480px;
            width: 100%;
            border-radius: 16px;
            padding: 32px 28px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #e7f9ef;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon svg {
            width: 40px;
            height: 40px;
            color: #28a745;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 26px;
            color: #222;
        }

        p {
            margin: 0 0 24px;
            color: #555;
            font-size: 16px;
            line-height: 1.6;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 15px;
            color: #444;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 14px 26px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-outline {
            border: 2px solid #dc3545;
            color: #dc3545;
            background: transparent;
        }

        .btn-outline:hover {
            background: #dc3545;
            color: #fff;
        }

        .btn-primary {
            background: blue;
            color: #fff;
        }

        .btn-primary:hover {
            background: #bb2d3b;
            transform: translateY(-1px);
        }

        .btn:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
        }

        .footer {
            margin-top: 24px;
            font-size: 13px;
            color: #888;
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 22px;
            }

            p {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1>Thank You!</h1>
        <p>Your payment has been received successfully.</p>

        <div class="info-box">
            We’re processing your order.<br>
            A confirmation email will be sent to you shortly.
        </div>

        <div class="btn-group">
            <!-- Replace with your retry URL -->
            <a href="<?= $domainURL ?>order-details/<?= $getOrder["hash_code"] ?>" class="btn btn-primary" target="_blank">
                Order Details
            </a>

            <a href="<?= $domainURL ?>main" class="btn btn-outline">
                Back to Home
            </a>
        </div>

        <div class="footer">
            © 2026 Your Company Name
        </div>
    </div>

</body>

</html>