<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up · ANNA HOMES</title>
    <style>
        :root { --navy: #0A2F44; --ivory: #FDF8F2; --terracotta: #C96E5D; }
        body { background: linear-gradient(135deg, var(--navy) 0%, #1a455f 100%); font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0; }
        .card { background: var(--ivory); padding: 40px; border-radius: 15px; width: 350px; text-align: center; }
        h1 { color: var(--navy); margin-bottom: 5px; }
        .sub { color: var(--terracotta); font-style: italic; margin-bottom: 30px; display: block; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: var(--terracotta); color: white; border: none; border-radius: 25px; cursor: pointer; font-weight: bold; }
        a { color: var(--navy); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h1>ANNA HOMES</h1>
        <span class="sub">Join our community</span>
        <form action="signup_process.php" method="POST">
            <input type="text" name="username" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">SIGN UP</button>
        </form>
        <p style="margin-top:20px;">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>