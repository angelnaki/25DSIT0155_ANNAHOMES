
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login · ANNA HOMES</title>
    <style>
        :root { --navy: #0A2F44; --ivory: #FDF8F2; --terracotta: #C96E5D; }
        body { background: linear-gradient(135deg, var(--navy) 0%, #1a455f 100%); font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0; }
        .card { background: var(--ivory); padding: 40px; border-radius: 15px; width: 350px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: var(--navy); margin-bottom: 5px; }
        .sub { color: var(--terracotta); font-style: italic; margin-bottom: 30px; display: block; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: var(--navy); color: white; border: none; border-radius: 25px; cursor: pointer; font-weight: bold; }
        button:hover { background: var(--terracotta); }
        a { color: var(--terracotta); text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>ANNA HOMES</h1>
        <span class="sub">Welcome Back</span>
        <form action="login_process.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN</button>
        </form>
        <p style="margin-top:20px;">New? <a href="signup.php">Create Account</a></p>
    </div>
</body>
</html>