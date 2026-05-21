<?php
session_start();
include 'config/db.php';

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = MD5($_POST['password']);

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: dashboard.php');
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Zithex Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.3);
        }
        .login-card h2 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .login-card p {
            color: rgba(255,255,255,0.5);
            margin-bottom: 30px;
        }
        .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: #fff;
            padding: 12px 15px;
            border-radius: 10px;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.12);
            border-color: #e94560;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(233,69,96,0.2);
        }
        .form-control::placeholder {
            color: rgba(255,255,255,0.3);
        }
        .form-label {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            margin-bottom: 6px;
        }
        .input-group-text {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.5);
            border-radius: 10px 0 0 10px;
        }
        .btn-login {
            background: linear-gradient(135deg, #e94560, #c23152);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(233,69,96,0.4);
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #e94560, #c23152);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .brand-icon i {
            font-size: 28px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-icon">
            <i class="fas fa-store"></i>
        </div>
        <h2>Welcome Back</h2>
        <p>Sign in to Zithex Manager</p>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger rounded-3 py-2">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       placeholder="admin@business.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login" class="btn btn-login btn-primary w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>
    </div>
    
<div style="position:fixed; bottom:20px; left:0; right:0; text-align:center; color:rgba(255,255,255,0.4); font-size:13px;">
    Developed by <strong style="color:rgba(255,255,255,0.7);">Abdul Bazith (Zithex)</strong> | Full Stack Developer
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>