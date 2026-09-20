<?php
require_once 'db.php';
session_start();

$success = $error = '';

// Handle Feedback Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($message) < 10) {
        $error = "Message must be at least 10 characters long.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $success = "Thank you! Your feedback has been submitted successfully.";
            $_POST = []; // Clear form
        } catch (Exception $e) {
            $error = "Sorry, something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - UU Hospital</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            background: #f4f8ff; 
            line-height: 1.6;
        }
        header {
            background: linear-gradient(rgba(0,51,102,0.9), rgba(0,51,102,0.95)), url('img/18691897.jpg');
            background-size: cover;
            color: white;
            padding: 50px 20px;
            text-align: center;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 18px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 { font-size: 42px; margin: 0; color: #003366; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        h2 { color: #003366; margin-bottom: 20px; }
        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }
        label {
            display: block;
            font-weight: bold;
            color: #003366;
            margin-bottom: 8px;
            font-size: 17px;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #003366;
            border-radius: 10px;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #005599;
            box-shadow: 0 0 10px rgba(0, 85, 153, 0.3);
        }
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        button {
            background: #003366;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
        }
        button:hover {
            background: #005599;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .message {
            padding: 18px;
            border-radius: 12px;
            margin: 25px 0;
            font-weight: bold;
            font-size: 18px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .nav-links {
            margin-top: 30px;
        }
        .nav-links a {
            color: #003366;
            text-decoration: none;
            font-weight: bold;
            margin: 0 15px;
            font-size: 16px;
        }
        .nav-links a:hover {
            color: #005599;
            text-decoration: underline;
        }
        footer {
            background: #003366;
            color: white;
            text-align: center;
            padding: 25px;
            margin-top: 60px;
            font-size: 15px;
        }
        .emoji { font-size: 50px; margin-bottom: 20px; }
    </style>
</head>
<body>

<header>
    <h1>UU Hospital</h1>
    <p>We Value Your Feedback</p>
</header>

<div class="container">
    <div class="card">
        <div class="emoji">Speak Up</div>
        <h2>Share Your Experience With Us</h2>
        <p>Your feedback helps us improve our services and care for all patients.</p>

        <?php if ($success): ?>
            <div class="message success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" name="name" id="name" placeholder="Enter your full name" required 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">Your Email</label>
                <input type="email" name="email" id="email" placeholder="you@example.com" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="message">Your Message / Feedback</label>
                <textarea name="message" id="message" placeholder="Please share your thoughts, suggestions, or experience..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
            </div>

            <button type="submit">Submit Feedback</button>
        </form>

        <div class="nav-links">
            <a href="home.html">Home</a> • 
            <a href="patient.php">Patient Portal</a> • 
            <a href="login.php">My Dashboard</a> • 
            <a href="contact.html">Contact Us</a>
        </div>
    </div>
</div>

<footer>
    <p>Copyright © <span id="year"></span> - All Rights Reserved - UU Hospital</p>
    <p>Developed by ASIF HASAN ONTU</p>
</footer>

<script>
    document.getElementById('year').textContent = new Date().getFullYear();
</script>

</body>
</html>