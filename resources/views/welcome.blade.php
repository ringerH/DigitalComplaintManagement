<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Complaint Manager | Complaint Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; }
        header { background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); text-align: center; }
        h1 { color: #333; text-align: center; }
        .auth-buttons { margin-top: 20px; }
        .auth-buttons a { display: inline-block; padding: 10px 20px; margin: 0 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; font-size: 16px; }
        .auth-buttons a.admin-link { background-color: #007BFF; }
        .auth-buttons a:hover { background-color: #45a049; }
        .auth-buttons a.admin-link:hover { background-color: #0056b3; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #777; }
        @media (max-width: 576px) {
            .container { padding: 15px; }
            .auth-buttons a { display: block; margin: 10px auto; width: 80%; }
        }
    </style>
</head>
<body onload="openAdminLogin()">
    <header>
        <h1>Healthcare Grievance Resolution</h1>
    </header>

    <div class="container">
        @guest('web')
            <h1>Welcome to the Complaint Portal</h1>
            <p>Register or log in to submit and track your complaints.</p>
            <div class="auth-buttons">
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                <a href="{{ route('admin.login') }}" class="admin-link" target="_blank">Admin Login</a>
            </div>
        @else
            <h1>Welcome Back, {{ Auth::guard('web')->user()->name }}</h1>
            <p>Head to your dashboard to manage your complaints.</p>
            <div class="auth-buttons">
                <a href="{{ route('home') }}">Go to Dashboard</a>
            </div>
        @endguest
    </div>

    <div class="footer">
        © 2025 Centralized Complaint System. All rights reserved.
    </div>

    @if(app()->environment('local'))
    <script>
        if (!sessionStorage.getItem('adminTabOpened')) {
            window.open("{{ route('admin.login') }}", "_blank");
            sessionStorage.setItem('adminTabOpened', 'true');
        }
    </script>
@endif

</body>

</html>