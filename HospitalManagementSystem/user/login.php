<?php
session_start();

// Re-initialize session arrays (important for safety)
if (!isset($_SESSION['users']))             $_SESSION['users'] = [];
if (!isset($_SESSION['user_appointments'])) $_SESSION['user_appointments'] = [];
if (!isset($_SESSION['inquiries']))         $_SESSION['inquiries'] = [];
if (!isset($_SESSION['feedback']))          $_SESSION['feedback'] = [];

// If not logged in → go to patient.php
if (!isset($_SESSION['logged_in_user'])) {
    header('Location: patient.php');
    exit;
}

$username = $_SESSION['logged_in_user'];
$user_info = null;
foreach ($_SESSION['users'] as $user) {
    if ($user['username'] === $username) {
        $user_info = $user;
        break;
    }
}

// Initialize appointments for this user
if (!isset($_SESSION['user_appointments'][$username])) {
    $_SESSION['user_appointments'][$username] = [];
}

$doctors = ['Dr. Jerry', 'Dr. Tom Smith', 'Dr. Pikachu', 'Dr. Doraemon'];

// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    $doctor = trim($_POST['doctor']);
    $date   = $_POST['date'];
    $time   = $_POST['time'];
    $reason = trim($_POST['reason']);

    if (!empty($doctor) && !empty($date) && !empty($time) && !empty($reason) && strtotime($date) > time()) {
        $conflict = false;
        foreach ($_SESSION['user_appointments'][$username] as $appt) {
            if ($appt['date'] === $date && $appt['time'] === $time) {
                $conflict = true;
                break;
            }
        }
        if (!$conflict) {
            $_SESSION['user_appointments'][$username][] = [
                'doctor' => htmlspecialchars($doctor),
                'date'   => htmlspecialchars($date),
                'time'   => htmlspecialchars($time),
                'reason' => htmlspecialchars($reason)
            ];
            $booking_success = "Appointment booked successfully!";
        } else {
            $booking_error = "This time slot is already booked.";
        }
    } else {
        $booking_error = "Please fill all fields and select a future date.";
    }
}

// LOGOUT – ONLY REMOVE LOGIN STATUS, KEEP ALL DATA!
if (isset($_GET['logout'])) {
    unset($_SESSION['logged_in_user']);   // ← ONLY THIS LINE
    header('Location: patient.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard -Uttara Unity Hospital</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; background-color: #ffffff; }
        header { background-color: #003366; background-image: url(img/18691897.jpg); background-size: cover; color: #ffffff; padding: 20px; display: flex; align-items: center; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .logo { width: 100px; height: 100px; background-image: url(logo.png); background-size: cover; border-radius: 50%; margin-right: 20px; }
        .title { font-size: 45px; font-family: "Comic Sans MS", cursive; font-weight: bold; text-shadow: 2px 2px 4px #000; }
        nav { background-color: #ffffff; border: 1px solid #003366; border-radius: 5px; margin-top: 10px; }
        nav ul { list-style: none; padding: 0; margin: 0; display: flex; justify-content: space-around; }
        nav a { text-decoration: none; color: #003366; font-weight: bold; font-family: "Comic Sans MS", cursive; font-size: 21px; padding: 15px; display: block; border-radius: 5px; transition: background-color 0.3s, color 0.3s; }
        nav a:hover { color: #ffffff; background-color: #003366; }
        .container { margin: 20px; }
        .dashboard-section { background: radial-gradient(#ffffff, #f0f8ff); border: 3px solid #003366; border-radius: 10px; padding: 20px; box-shadow: 6px 6px 20px rgba(0,0,0,0.5); max-width: 1200px; margin: 0 auto; }
        .dashboard-section h2 { font-size: 36px; font-weight: bold; color: #003366; font-family: "Comic Sans MS", cursive; text-align: center; margin-top: 0; }
        .welcome { text-align: center; margin-bottom: 30px; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .dashboard-card { background: #f9f9f9; border: 1px solid #003366; border-radius: 5px; padding: 15px; }
        .dashboard-card h3 { color: #003366; font-family: "Comic Sans MS", cursive; margin-top: 0; }
        .dashboard-card ul { list-style: none; padding: 0; }
        .dashboard-card li { margin: 10px 0; padding: 10px; background: #e0f7fa; border-radius: 5px; }
        .booking-form label { display: block; margin: 10px 0 5px; font-weight: bold; color: #003366; }
        .booking-form input, .booking-form select, .booking-form textarea { width: 100%; padding: 10px; border: 1px solid #003366; border-radius: 5px; }
        .booking-form button { background-color: #003366; color: #ffffff; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .booking-form button:hover { background-color: #005599; }
        .message { text-align: center; margin: 10px 0; font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
        .logout-btn { display: block; background-color: #c62828; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 5px; text-align: center; margin: 30px auto; width: 180px; font-weight: bold; }
        .logout-btn:hover { background-color: #a00000; }
        footer { background-color: #003366; color: #ffffff; text-align: center; padding: 20px; margin-top: 20px; }
        @media (max-width: 768px) { .title { font-size: 30px; } nav ul { flex-direction: column; } .dashboard-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="logo"></div>
        <div class="title">Uttara Unity Hospital</div>
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
        <section class="dashboard-section">
            <h2>Patient Dashboard</h2>
            <hr>

            <div class="welcome">
                <h3>Welcome , <?php echo htmlspecialchars($user_info['name']); ?>!</h3>
                <p>Email: <?php echo htmlspecialchars($user_info['email']); ?> | Username: <?php echo htmlspecialchars($user_info['username']); ?></p>
            </div>

            <div class="dashboard-grid">
                <!-- Book Appointment -->
                <div class="dashboard-card">
                    <h3>Book Doctor's Appointment</h3>
                    <form class="booking-form" method="POST">
                        <label>Select Doctor:</label>
                        <select name="doctor" required>
                            <option value="">Choose a doctor</option>
                            <?php foreach ($doctors as $doc): ?>
                                <option value="<?= $doc ?>"><?= $doc ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Date:</label>
                        <input type="date" name="date" required min="<?= date('Y-m-d') ?>">

                        <label>Time:</label>
                        <input type="time" name="time" required>

                        <label>Reason:</label>
                        <textarea name="reason" rows="3" required></textarea>

                        <button type="submit" name="book_appointment">Book Appointment</button>
                    </form>

                    <?php if (isset($booking_success)): ?>
                        <p class="message success"><?= $booking_success ?></p>
                    <?php endif; ?>
                    <?php if (isset($booking_error)): ?>
                        <p class="message error"><?= $booking_error ?></p>
                    <?php endif; ?>
                </div>

                <!-- Upcoming Appointments -->
                <div class="dashboard-card">
                    <h3>Upcoming Appointments</h3>
                    <ul>
                        <?php if (!empty($_SESSION['user_appointments'][$username])): ?>
                            <?php foreach ($_SESSION['user_appointments'][$username] as $appt): ?>
                                <li>
                                    <strong><?= $appt['date'] ?> at <?= $appt['time'] ?></strong><br>
                                    Doctor: <?= $appt['doctor'] ?><br>
                                    Reason: <?= $appt['reason'] ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No upcoming appointments.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <a href="?logout=1" class="logout-btn">Logout</a>
        </section>
    </div>

    <footer>
        <p>Copyright © <span id="year"></span> - All Rights Reserved - <a href="#">UU Hospital</a></p>
        <p>Powered By UU Hospital</p>
        <p>Developed by - ASIF HASAN ONTU</p>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
        document.querySelector('input[type="date"]').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>