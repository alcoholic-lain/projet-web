<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        /* Body */
        body.login-body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: url('/tunispace/assets/background.jpg') center/cover fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow-x: hidden;
            color: white;
        }

```
    /* Login container */
    .login-container {
        background: rgba(44, 44, 68, 0.95); /* slightly transparent dark */
        padding: 40px 30px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        width: 350px;
        text-align: center;
    }

    /* Title */
    .login-title {
        color: #fff;
        margin-bottom: 30px;
        font-size: 28px;
    }

    /* Form styling */
    .login-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .login-label {
        color: #ccc;
        text-align: left;
        font-size: 14px;
    }

    .login-input {
        padding: 12px 15px;
        border-radius: 8px;
        border: 2px solid #6c63ff;
        font-size: 16px;
        outline: none;
        transition: 0.3s;
    }

    .login-input:focus {
        border-color: #9a7fff;
        box-shadow: 0 0 8px rgba(106, 99, 255, 0.5);
    }

    /* Buttons */
    .login-btn, .home-btn {
        padding: 12px;
        border-radius: 8px;
        border: none;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    .login-btn {
        background-color: #6c63ff;
        color: white;
    }

    .login-btn:hover {
        background-color: #5751d9;
    }

    .home-btn {
        background-color: #444466;
        color: #fff;
    }

    .home-btn:hover {
        background-color: #5a5a80;
    }

    /* Error message */
    .error-msg {
        margin-top: 15px;
        color: #ff6b6b;
        font-weight: bold;
    }
</style>
```

</head>
<body class="login-body">

<div class="login-container">
    <h2 class="login-title">Admin Login</h2>

```
<form class="login-form" action="../controller/admin_login.php" method="POST">
    <label class="login-label">Password:</label>
    <input class="login-input" type="password" name="password" required>

    <button class="login-btn" type="submit">Login</button>
    <button class="home-btn" type="button" onclick="window.location.href='/tunispace/view/index.php'">Go Home</button>
</form>

<?php if (isset($_GET['error'])): ?>
    <p class="error-msg">Incorrect Password</p>
<?php endif; ?>
```

</div>

</body>
</html>
