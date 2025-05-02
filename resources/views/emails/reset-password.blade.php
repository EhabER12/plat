<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 30px 20px;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .btn:hover {
            background-color: #004494;
        }
        .note {
            font-size: 12px;
            color: #666;
            margin-top: 30px;
        }
        .action-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" onerror="this.src='https://via.placeholder.com/150x50?text=LOGO'">
            <h2>إعادة تعيين كلمة المرور | Reset Your Password</h2>
        </div>
        
        <div class="content">
            <p>مرحباً {{ $user->name }}،</p>
            <p>لقد تلقينا طلبًا لإعادة تعيين كلمة المرور الخاصة بحسابك. يرجى النقر على الزر أدناه لإعادة تعيين كلمة المرور الخاصة بك.</p>
            
            <p>Hello {{ $user->name }},</p>
            <p>We received a request to reset the password for your account. Please click the button below to reset your password.</p>
            
            <div class="action-container">
                <a href="{{ $resetUrl }}" class="btn">إعادة تعيين كلمة المرور | Reset Password</a>
            </div>
            
            <p>إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد الإلكتروني.</p>
            
            <p>If you did not request a password reset, you can ignore this email.</p>
            
            <div class="note">
                <p>هذا الرابط سيكون صالحاً لمدة 60 دقيقة فقط.</p>
                <p>This password reset link will expire in 60 minutes.</p>
                
                <p>إذا كنت تواجه مشكلة في النقر على زر "إعادة تعيين كلمة المرور"، فيمكنك نسخ ولصق عنوان URL أدناه في متصفح الويب الخاص بك:</p>
                <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
                
                <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة | All rights reserved.</p>
        </div>
    </div>
</body>
</html> 