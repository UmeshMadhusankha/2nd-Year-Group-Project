<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repairer Dashboard - FixLanka</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 600px;
        }
        
        h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
        }
        
        .icon {
            font-size: 80px;
            margin-bottom: 30px;
        }
        
        p {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-top: 30px;
            text-align: left;
        }
        
        .info strong {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔧</div>
        <h1>Welcome, Repairer!</h1>
        
        <?php
        session_start();
        if (isset($_SESSION['success'])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        
        if (isset($_SESSION['user_name'])) {
            echo '<p>Hello, <strong>' . htmlspecialchars($_SESSION['user_name']) . '</strong>!</p>';
        }
        ?>
        
        <p>Your repairer account has been successfully created and verified.</p>
        
        <p>The full Repairer Dashboard is currently under development and will include:</p>
        
        <div class="info">
            <ul style="list-style-position: inside; color: #666;">
                <li><strong>Job Management:</strong> View and accept available jobs</li>
                <li><strong>Profile Management:</strong> Update your skills and availability</li>
                <li><strong>Earnings Tracker:</strong> Monitor your income and payments</li>
                <li><strong>Customer Reviews:</strong> View ratings and feedback</li>
                <li><strong>Support System:</strong> Get help when you need it</li>
            </ul>
        </div>
        
        <p style="margin-top: 30px;">
            <a href="/2nd-Year-Group-Project/FixLanka/" class="btn">Back to Home</a>
        </p>
    </div>
</body>
</html>
