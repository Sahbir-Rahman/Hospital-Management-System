<?php
require_once 'db.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: patient.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_info = $stmt->fetch();

// Doctors list
$doctors = [
    'Dr. John Doe',
    'Dr. Jane Smith',
    'Dr. Michael Johnson',
    'Dr. Emily Davis',
    'Dr. Sarah Williams',
    'Dr. Ahmed Khan'
];

// Handle Appointment Booking
$booking_success = $booking_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    $doctor = trim($_POST['doctor']);
    $date   = $_POST['date'];
    $time   = $_POST['time'];
    $reason = trim($_POST['reason']);

    if (empty($doctor) || empty($date) || empty($time) || empty($reason)) {
        $booking_error = "All fields are required.";
    } elseif (strtotime($date) < strtotime('today')) {
        $booking_error = "Cannot book appointment in the past.";
    } else {
        // Check for conflict
        $stmt = $pdo->prepare("SELECT id FROM appointments WHERE user_id = ? AND appointment_date = ? AND appointment_time = ?");
        $stmt->execute([$user_id, $date, $time]);

        if ($stmt->rowCount() > 0) {
            $booking_error = "You already have an appointment at this time.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO appointments (user_id, doctor, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $doctor, $date, $time, $reason]);
            $booking_success = "Appointment booked successfully!";
        }
    }
}

// Fetch user's appointments
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC, appointment_time DESC");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: patient.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - UU Hospital</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f8ff; }
        header {
            background: linear-gradient(rgba(0,51,102,0.9), rgba(0,51,102,0.95)), url('img/18691897.jpg');
            background-size: cover; color: white; padding: 40px 20px; text-align: center;
        }
        .container { max-width: 1100px; margin: 30px auto; padding: 20px; }
        .card { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #003366; }
        .welcome { text-align: center; background: #e6f2ff; padding: 20px; border-radius: 10px; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin-top: 30px; }
        .dashboard-card { background: #f8fdff; border: 2px solid #003366; border-radius: 12px; padding: 25px; }
        input, select, textarea, button {
            width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #003366; border-radius: 8px; font-size: 16px;
        }
        button { background: #003366; color: white; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #005599; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #003366; color: white; }
        tr:hover { background: #f0f8ff; }
        .message { padding: 15px; border-radius: 8px; margin: 15px 0; font-weight: bold; text-align: center; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .logout-btn { display: inline-block; background: #dc3545; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .logout-btn:hover { background: #c82333; }
        footer { background: #003366; color: white; text-align: center; padding: 20px; margin-top: 50px; }
        .no-appointments { text-align: center; color: #666; font-style: italic; padding: 20px; }
    </style>
</head>
<body>

<header>
    <h1>UU Hospital Management System</h1>
    <p>Patient Dashboard</p>
</header>

<div class="container">

    <div class="card welcome">
        <h2>Welcome back, <?php echo htmlspecialchars($user_info['name']); ?>!</h2>
        <p>Email: <?php echo htmlspecialchars($user_info['email']); ?> | Username: <?php echo htmlspecialchars($user_info['username']); ?></p>
        <a href="?logout=1" class="logout-btn">Logout</a>
    </div>

    <?php if ($booking_success): ?>
        <div class="message success"><?php echo $booking_success; ?></div>
    <?php endif; ?>
    <?php if ($booking_error): ?>
        <div class="message error"><?php echo $booking_error; ?></div>
    <?php endif; ?>

    <div class="dashboard-grid">

        <!-- Book Appointment -->
        <div class="dashboard-card">
            <h3>Book New Appointment</h3>
            <form method="POST">
                <label>Select Doctor</label>
                <select name="doctor" required>
                    <option value="">-- Choose Doctor --</option>
                    <?php foreach ($doctors as $doc): ?>
                        <option value="<?php echo $doc; ?>"><?php echo $doc; ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Date</label>
                <input type="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>

                <label>Time</label>
                <input type="time" name="time" required>

                <label>Reason for Visit</label>
                <textarea name="reason" rows="4" placeholder="Briefly describe your symptoms or reason..." required></textarea>

                <button type="submit" name="book_appointment">Book Appointment</button>
            </form>
        </div>

        <!-- Upcoming Appointments -->
        <div class="dashboard-card">
            <h3>Your Appointments</h3>
            <?php if (empty($appointments)): ?>
                <p class="no-appointments">No appointments booked yet.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Doctor</th>
                        <th>Reason</th>
                    </tr>
                    <?php foreach ($appointments as $appt): ?>
                    <tr>
                        <td><strong><?php echo date('d M Y', strtotime($appt['appointment_date'])); ?></strong></td>
                        <td><?php echo date('h:i A', strtotime($appt['appointment_time'])); ?></td>
                        <td><?php echo htmlspecialchars($appt['doctor']); ?></td>
                        <td><?php echo htmlspecialchars($appt['reason']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>

    </div>

</div>

<footer>
    <p>Copyright © <span id="year"></span> - All Rights Reserved - UU Hospital</p>
    <p>Developed by ASIF HASAN ONTU</p>
</footer>

<script>
    document.getElementById('year').textContent = new Date().getFullYear();
    // Set minimum date to today
    document.querySelector('input[type="date"]').min = new Date().toISOString().split('T')[0];
</script>

</body>
</html>