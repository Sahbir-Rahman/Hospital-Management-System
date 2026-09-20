<?php
session_start();

if (!isset($_SESSION['feedback'])) {
    $_SESSION['feedback'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    
    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['feedback'][] = [
            'name' => htmlspecialchars($name),
            'email' => htmlspecialchars($email),
            'message' => htmlspecialchars($message),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $success = "Thank you for your feedback!";
    } else {
        $error = "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback -Uttara Unity Hospital</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* Header: Logo + Name in left corner */
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

        .header-left {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 100px;
            height: 100px;
            background-image: url(logo.png);
            background-size: cover;
            background-position: center;
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
            background-color: #ffffff;
            border: 1px solid #003366;
            border-radius: 5px;
            margin: 0 15px 10px;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        nav li {
            flex: 1;
            text-align: center;
            min-width: 120px;
        }

        nav a {
            text-decoration: none;
            color: #003366;
            font-weight: bold;
            font-family: "Comic Sans MS", cursive;
            font-size: 21px;
            padding: 15px 10px;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        nav a:hover {
            color: #ffffff;
            background-color: #003366;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .feedback-section {
            background: radial-gradient(#ffffff, #f0f8ff);
            border: 3px solid #003366;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 6px 6px 20px rgba(0,0,0,0.5);
            margin: 30px auto;
        }

        .feedback-section h2 {
            font-size: 36px;
            font-weight: bold;
            color: #003366;
            font-family: "Comic Sans MS", cursive;
            text-align: center;
            margin: 0 0 20px 0;
        }

        .feedback-section > p {
            text-align: center;
            margin-bottom: 30px;
            font-size: 17px;
        }

        .feedback-form {
            max-width: 700px;
            margin: 0 auto;
        }

        .feedback-form label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #003366;
        }

        .feedback-form input,
        .feedback-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #003366;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }

        .feedback-form textarea {
            height: 130px;
            resize: vertical;
        }

        .feedback-form button {
            background-color: #003366;
            color: #ffffff;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            display: block;
            margin: 25px auto 0;
            transition: background-color 0.3s;
        }

        .feedback-form button:hover {
            background-color: #005599;
        }

        .message {
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 18px;
        }

        .success { color: green; }
        .error { color: red; }

        footer {
            background-color: #003366;
            color: #ffffff;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }

        footer a { color: #ffffff; text-decoration: none; }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }
            .logo { margin-bottom: 10px; margin-right: 0; }
            .title { font-size: 36px; }
            nav ul { flex-direction: column; }
            nav a { padding: 12px; }
            .feedback-section { padding: 20px; margin: 20px 10px; }
        }
    </style>
</head>
<body>

    <!-- Logo + Name in left corner -->
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
        <section class="feedback-section">
            <h2>Feedback</h2>
            <hr>
            <p>We value your feedback! Please share your thoughts about our services.</p>
            
            <form class="feedback-form" method="POST" action="">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
                
                <button type="submit">Submit Feedback</button>
            </form>
            
            <?php if (isset($success)): ?>
                <p class="message success"><?php echo $success; ?></p>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <p class="message error"><?php echo $error; ?></p>
            <?php endif; ?>
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