<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verification Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            direction: ltr;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello!</h2>
        <p>Thank you for signing up. Your verification code is:</p>
        <div class="code">{{ $code }}</div>
        <p>Please enter this code to complete your email verification.</p>
        <div class="footer">
            If you did not request this email, you can safely ignore it.
        </div>
    </div>
</body>
</html>
