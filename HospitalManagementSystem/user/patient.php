<?php
session_start();

// Re-initialize session data if missing (safe even after browser restart)
if (!isset($_SESSION['users']))             $_SESSION['users'] = [];
if (!isset($_SESSION['inquiries']))         $_SESSION['inquiries'] = [];
if (!isset($_SESSION['user_appointments'])) $_SESSION['user_appointments'] = [];
if (!isset($_SESSION['feedback']))          $_SESSION['feedback'] = [];

// ==================== LOGOUT – ONLY LOG OUT USER, KEEP ALL DATA ====================
if (isset($_GET['logout'])) {
    unset($_SESSION['logged_in_user']);   // ← This is the correct way
    header("Location: patient.php");
    exit;
}

// ==================== SIGNUP ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $name     = trim($_POST['signup_name'] ?? '');
    $email    = trim($_POST['signup_email'] ?? '');
    $username = trim($_POST['signup_username'] ?? '');
    $password = $_POST['signup_password'] ?? '';

    if (empty($name) || empty($email) || empty($username) || empty($password)) {
        $signup_error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = "Invalid email format.";
    } else {
        foreach ($_SESSION['users'] as $user) {
            if ($user['username'] === $username) {
                $signup_error = "Username already taken.";
                break;
            }
        }
        if (!isset($signup_error)) {
            $_SESSION['users'][] = [
                'name'     => htmlspecialchars($name),
                'email'    => htmlspecialchars($email),
                'username' => htmlspecialchars($username),
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ];
            $_SESSION['logged_in_user'] = $username;
            header("Location: login.php");
            exit;
        }
    }
}

// ==================== LOGIN ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['login_username'] ?? '');
    $password = $_POST['login_password'] ?? '';

    if (empty($username) || empty($password)) {
        $login_error = "Please enter username and password.";
    } else {
        foreach ($_SESSION['users'] as $user) {
            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                $_SESSION['logged_in_user'] = $username;
                header("Location: login.php");
                exit;
            }
        }
        $login_error = "Invalid username or password.";
    }
}

// ==================== SUBMIT INQUIRY ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    $inq_name    = trim($_POST['name'] ?? '');
    $inq_email   = trim($_POST['email'] ?? '');
    $inq_type    = $_POST['type'] ?? '';
    $inq_message = trim($_POST['message'] ?? '');

    if (empty($inq_name) || empty($inq_email) || empty($inq_type) || empty($inq_message)) {
        $inquiry_error = "All fields are required.";
    } elseif (!filter_var($inq_email, FILTER_VALIDATE_EMAIL)) {
        $inquiry_error = "Invalid email address.";
    } else {
        $_SESSION['inquiries'][] = [
            'name'      => htmlspecialchars($inq_name),
            'email'     => htmlspecialchars($inq_email),
            'type'      => htmlspecialchars($inq_type),
            'message'   => htmlspecialchars($inq_message),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $inquiry_success = "Thank you! Your inquiry has been sent to the admin.";
    }
}

// Get current user
$logged_in = isset($_SESSION['logged_in_user']);
$user_info = null;
if ($logged_in) {
    foreach ($_SESSION['users'] as $user) {
        if ($user['username'] === $_SESSION['logged_in_user']) {
            $user_info = $user;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient & Visitor's -Uttara Unity Hospital</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; background: #ffffff; }

        header {
            background-color: #003366;
            background-image: url(img/18691897.jpg);
            background-size: cover;
            background-position: center;
            color: #ffffff;
            padding: 20px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 15px;
        }
        .header-left { display: flex; align-items: center; }
        .logo {
            width: 100px; height: 100px;
            background: url(logo.png) center/cover no-repeat;
            border-radius: 50%;
            margin-right: 20px;
        }
        .title {
            font-size: 45px;
            font-family: "Comic Sans MS", cursive;
            font-weight: bold;
            text-shadow: 2px 2px 4px #000;
            margin: 0;
        }

        nav {
            background: #ffffff;
            border: 1px solid #003366;
            border-radius: 5px;
            margin: 0 15px 15px;
        }
        nav ul { list-style: none; padding: 0; margin: 0; display: flex; justify-content: center; flex-wrap: wrap; }
        nav li { flex: 1; text-align: center; min-width: 120px; }
        nav a {
            display: block; padding: 15px 10px;
            color: #003366; text-decoration: none;
            font-family: "Comic Sans MS", cursive; font-size: 21px; font-weight: bold;
            border-radius: 5px; transition: 0.3s;
        }
        nav a:hover { background: #003366; color: white; }

        .container { max-width: 900px; margin: 0 auto; padding: 0 15px; }

        .patient-visitor-section {
            background: radial-gradient(#ffffff, #f0f8ff);
            border: 3px solid #003366;
            border-radius: 10px;
            padding: 35px;
            box-shadow: 6px 6px 20px rgba(0,0,0,0.5);
            margin: 20px auto;
        }
        .patient-visitor-section h2 {
            font-size: 36px; color: #003366; font-family: "Comic Sans MS", cursive;
            text-align: center; margin: 0 0 20px 0;
        }

        .user-info {
            background: #e6f7ff; border: 2px solid #003366;
            border-radius: 8px; padding: 20px; text-align: center;
            margin-bottom: 30px; font-size: 18px;
        }
        .user-info a { color: #c62828; font-weight: bold; text-decoration: none; }

        .auth-section {
            background: #f9f9f9; border: 2px solid #003366;
            border-radius: 10px; padding: 30px; margin-bottom: 30px; text-align: center;
        }
        .auth-forms { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; margin-top: 20px; }
        .auth-form {
            width: 320px; background: white; padding: 25px;
            border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .auth-form h3 { color: #003366; font-family: "Comic Sans MS", cursive; font-size: 24px; margin: 0 0 15px; }

        .info-section { display: flex; gap: 25px; margin: 35px 0; flex-wrap: wrap; }
        .info-card {
            flex: 1; min-width:280px;
            background:#f0f8ff; border:2px solid #003366;
            border-radius:10px; padding:20px; text-align:center;
        }
        .info-card h3 { color:#003366; font-family:"Comic Sans MS", cursive; }

        .inquiry-form {
            background:#f9f9f9; border:3px solid #003366;
            border-radius:10px; padding:30px;
        }
        .inquiry-form h3 {
            text-align:center; color:#003366;
            font-family:"Comic Sans MS", cursive; font-size:28px; margin:0 0 20px;
        }

        label { display:block; margin:15px 0 5px; font-weight:bold; color:#003366; }
        input, select, textarea {
            width:100%; padding:12px;
            border:1px solid #003366; border-radius:5px;
        }
        textarea { height:130px; resize:vertical; }

        button {
            background:#003366; color:white; border:none;
            padding:12px 30px; border-radius:5px;
            font-weight:bold; font-size:18px; cursor:pointer;
            display:block; margin:25px auto 0;
        }
        button:hover { background:#005599; }

        .message {
            text-align:center; margin:20px 0; padding:12px;
            border-radius:5px; font-weight:bold;
        }
        .success { background:#d4edda; color:green; }
        .error   { background:#f8d7da; color:red; }

        footer {
            background:#003366; color:white;
            text-align:center; padding:20px; margin-top:40px;
        }
        footer a { color:white; text-decoration:none; }

        @media (max-width:768px) {
            header { flex-direction:column; text-align:center; }
            .logo { margin-bottom:10px; margin-right:0; }
            .title { font-size:36px; }
            nav ul { flex-direction:column; }
            .auth-forms, .info-section { flex-direction:column; align-items:center; }
            .auth-form { width:100%; max-width:350px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="header-left">
            <div class="logo"></div>
            <div class="title">Uttara Unity Hospital</div>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="home.html">Home</a></li>
            <li><a href="patient.php">Patient & Visitor's</a></li>
            <li><a href="doctor.php">Doctors</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="contact.html">Contact us</a></li>
        </ul>
    </nav>

    <div class="container">
        <section class="patient-visitor-section">
            <h2>Patient & Visitor's Portal</h2>
            <hr>
            <p style="text-align:center;margin:20px 0 30px;font-size:17px;">
                Manage your account, submit inquiries, and get hospital information.
            </p>

            <?php if ($logged_in && $user_info): ?>
                <div class="user-info">
                    <h3>Welcome back, <?= htmlspecialchars($user_info['name']) ?>!</h3>
                    <p>Email: <?= htmlspecialchars($user_info['email']) ?></p>
                    <p>Username: <?= htmlspecialchars($user_info['username']) ?></p>
                    <a href="?logout=1">Logout</a>
                </div>
            <?php else: ?>
                <div class="auth-section">
                    <h3>Login or Create Account</h3>
                    <div class="auth-forms">
                        <form class="auth-form" method="POST">
                            <h3>Login</h3>
                            <input type="text" name="login_username" placeholder="Username" required>
                            <input type="password" name="login_password" placeholder="Password" required>
                            <button type="submit" name="login">Login</button>
                            <?php if (isset($login_error)) echo "<div class='message error'>$login_error</div>"; ?>
                        </form>

                        <form class="auth-form" method="POST">
                            <h3>Sign Up</h3>
                            <input type="text" name="signup_name" placeholder="Full Name" required>
                            <input type="email" name="signup_email" placeholder="Email" required>
                            <input type="text" name="signup_username" placeholder="Username" required>
                            <input type="password" name="signup_password" placeholder="Password" required>
                            <button type="submit" name="signup">Create Account</button>
                            <?php if (isset($signup_error)) echo "<div class='message error'>$signup_error</div>"; ?>
                        </form>
                    </div>
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

            <div class="inquiry-form">
                <h3>Submit Your Inquiry</h3>
                <form method="POST">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>

                    <label>Email</label>
                    <input type="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>

                    <label>Inquiry Type</label>
                    <select name="type" required>
                        <option value="">-- Select --</option>
                        <option value="Patient Registration" <?= (isset($_POST['type']) && $_POST['type']=='Patient Registration') ? 'selected' : '' ?>>Patient Registration</option>
                        <option value="Appointment Booking" <?= (isset($_POST['type']) && $_POST['type']=='Appointment Booking') ? 'selected' : '' ?>>Appointment Booking</option>
                        <option value="Visitor Information" <?= (isset($_POST['type']) && $_POST['type']=='Visitor Information') ? 'selected' : '' ?>>Visitor Information</option>
                        <option value="Other" <?= (isset($_POST['type']) && $_POST['type']=='Other') ? 'selected' : '' ?>>Other</option>
                    </select>

                    <label>Message</label>
                    <textarea name="message" rows="5" required><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>

                    <button type="submit" name="submit_inquiry">Submit Inquiry</button>
                </form>

                <?php if (isset($inquiry_success)): ?>
                    <div class="message success"><?= $inquiry_success ?></div>
                <?php endif; ?>
                <?php if (isset($inquiry_error)): ?>
                    <div class="message error"><?= $inquiry_error ?></div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <footer>
        <p>Copyright © <span id="year"></span> - All Rights Reserved - <a href="#">UU Hospital</a></p>
        <p>Powered By UU Hospital</p>
        <p>Developed by - ASIF HASAN ONTU</p>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>