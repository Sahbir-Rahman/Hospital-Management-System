<?php
require_once 'db.php';
session_start();

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: patient.php");
    exit;
}

// Handle Signup
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $name     = trim($_POST['signup_name']);
    $email    = trim($_POST['signup_email']);
    $username = trim($_POST['signup_username']);
    $password = password_hash($_POST['signup_password'], PASSWORD_DEFAULT);

    if (empty($name) || empty($email) || empty($username) || empty($_POST['signup_password'])) {
        $signup_error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = "Invalid email format.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $signup_error = "Username or email already exists!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, username, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $username, $password]);
            $_SESSION['user_id']   = $pdo->lastInsertId();
            $_SESSION['username']  = $username;
            header("Location: login.php");
            exit;
        }
    }
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['login_username']);
    $password = $_POST['login_password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: login.php");
        exit;
    } else {
        $login_error = "Invalid username or password.";
    }
}

// Handle Inquiry Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_inquiry'])) {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $type    = $_POST['type'];
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($type) || empty($message)) {
        $inquiry_error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $inquiry_error = "Invalid email address.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, type, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $type, $message]);
        $inquiry_success = "Thank you! Your inquiry has been submitted successfully.";
        $_POST = []; // Clear form
    }
}

// Get current logged-in user
$user_info = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_info = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient & Visitor's Portal - UU Hospital</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f8ff; }
        header { 
            background: linear-gradient(rgba(0,51,102,0.9), rgba(0,51,102,0.95)), url('img/18691897.jpg'); 
            background-size: cover; color: white; padding: 40px 20px; text-align: center;
        }
        .container { max-width: 1100px; margin: 30px auto; padding: 20px; }
        .card { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #003366; }
        input, select, textarea, button {
            width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #003366; border-radius: 8px; font-size: 16px;
        }
        button { background: #003366; color: white; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #005599; }
        .message { padding: 15px; border-radius: 8px; margin: 15px 0; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .auth-forms { display: flex; gap: 30px; flex-wrap: wrap; }
        .auth-form { flex: 1; min-width: 300px; }
        .info-section { display: flex; gap: 20px; flex-wrap: wrap; margin: 30px 0; }
        .info-card { flex: 1; min-width: 280px; background: #e6f2ff; padding: 20px; border-radius: 10px; }
        .logout-btn { display: inline-block; background: #dc3545; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold; }
        .logout-btn:hover { background: #c82333; }
        footer { background: #003366; color: white; text-align: center; padding: 20px; margin-top: 50px; }
    </style>
</head>
<body>

<header>
    <h1>UU Hospital Management System</h1>
    <p>Patient & Visitor's Portal</p>
</header>

<div class="container">

    <?php if ($user_info): ?>
        <div class="card" style="text-align:center;">
            <h2>Welcome back, <?php echo htmlspecialchars($user_info['name']); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($user_info['email']); ?> | Username: <?php echo htmlspecialchars($user_info['username']); ?></p>
            <a href="?logout=1" class="logout-btn">Logout</a>
        </div>
    <?php endif; ?>

    <?php if (!$user_info): ?>
        <div class="card">
            <h2 style="text-align:center;">Login or Create Account</h2>
            <div class="auth-forms">
                <div class="auth-form">
                    <h3>Login</h3>
                    <form method="POST">
                        <input type="text" name="login_username" placeholder="Username" required>
                        <input type="password" name="login_password" placeholder="Password" required>
                        <button type="submit" name="login">Login</button>
                    </form>
                </div>
                <div class="auth-form">
                    <h3>Sign Up</h3>
                    <form method="POST">
                        <input type="text" name="signup_name" placeholder="Full Name" required>
                        <input type="email" name="signup_email" placeholder="Email" required>
                        <input type="text" name="signup_username" placeholder="Username" required>
                        <input type="password" name="signup_password" placeholder="Password" required>
                        <button type="submit" name="signup">Create Account</button>
                    </form>
                </div>
            </div>
            <?php if (isset($login_error)): ?>
                <div class="message error"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <?php if (isset($signup_error)): ?>
                <div class="message error"><?php echo $signup_error; ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="info-section">
        <div class="info-card">
            <h3>Patient Services</h3>
            <p>Access appointment booking, medical records, billing information, and emergency contact details.</p>
        </div>
        <div class="info-card">
            <h3>Visitor Guidelines</h3>
            <p>Visiting hours: 4 PM – 8 PM daily. Please wear a mask and sanitize hands. Only 2 visitors per patient allowed.</p>
        </div>
    </div>

    <div class="card">
        <h2 style="text-align:center;">Submit Your Inquiry</h2>
        <form method="POST">
            <label>Name</label>
            <input type="text" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">

            <label>Email</label>
            <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <label>Inquiry Type</label>
            <select name="type" required>
                <option value="">-- Select --</option>
                <option value="Patient Registration" <?php echo (isset($_POST['type']) && $_POST['type']=='Patient Registration') ? 'selected' : ''; ?>>Patient Registration</option>
                <option value="Appointment Booking" <?php echo (isset($_POST['type']) && $_POST['type']=='Appointment Booking') ? 'selected' : ''; ?>>Appointment Booking</option>
                <option value="Visitor Information" <?php echo (isset($_POST['type']) && $_POST['type']=='Visitor Information') ? 'selected' : ''; ?>>Visitor Information</option>
                <option value="Other" <?php echo (isset($_POST['type']) && $_POST['type']=='Other') ? 'selected' : ''; ?>>Other</option>
            </select>

            <label>Message</label>
            <textarea name="message" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>

            <button type="submit" name="submit_inquiry">Submit Inquiry</button>
        </form>

        <?php if (isset($inquiry_success)): ?>
            <div class="message success"><?php echo $inquiry_success; ?></div>
        <?php endif; ?>
        <?php if (isset($inquiry_error)): ?>
            <div class="message error"><?php echo $inquiry_error; ?></div>
        <?php endif; ?>
    </div>

</div>

<footer>
    <p>Copyright © <span id="year"></span> - All Rights Reserved - <a href="#" style="color:white;">UU Hospital</a></p>
    <p>Developed by ASIF HASAN ONTU</p>
</footer>

<script>
    document.getElementById('year').textContent = new Date().getFullYear();
</script>
</body>
</html>