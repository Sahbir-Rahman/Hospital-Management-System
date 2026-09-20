<?php
require_once 'db.php';           // ← db.php must be inside admin folder
session_start();

// Admin credentials
$admin_user = "admin";
$admin_pass_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password: admin123

// Login
if (isset($_POST['login'])) {
    if ($_POST['user'] === $admin_user && password_verify($_POST['pass'], $admin_pass_hash)) {
        $_SESSION['admin'] = true;
    } else {
        $error = "Wrong username or password!";
    }
}

// Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    header("Location: admin.php");
    exit;
}

// Show Login Form
if (!isset($_SESSION['admin'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login - UU Hospital</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {font-family:Arial;background:#003366;color:white;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
            .box {background:white;color:#003366;padding:50px;border-radius:20px;width:380px;text-align:center;box-shadow:0 20px 50px rgba(0,0,0,0.5);}
            h2 {margin-bottom:30px;color:#003366;font-size:28px;}
            input {width:100%;padding:15px;margin:10px 0;border:2px solid #003366;border-radius:10px;font-size:16px;box-sizing:border-box;}
            button {background:#003366;color:white;padding:15px;border:none;border-radius:10px;width:100%;font-size:18px;cursor:pointer;margin-top:10px;}
            button:hover {background:#005599;}
            .error {color:#d63031;font-weight:bold;margin-top:15px;}
            small {color:#666;font-size:14px;margin-top:20px;display:block;}
        </style>
    </head>
    <body>
        <div class="box">
            <h2>UU Hospital<br>Admin Panel</h2>
            <form method="post">
                <input type="text" name="user" placeholder="Username" value="admin" required>
                <input type="password" name="pass" placeholder="Password" required>
                <button type="submit" name="login">LOGIN</button>
                <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
            </form>
            <small>Default: admin / admin123</small>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ================ ADMIN DASHBOARD (YOU ARE LOGGED IN) ================
$msg = "";

// Delete actions
if (isset($_GET['del_user'])) {
    $id = (int)$_GET['del_user'];
    $pdo->prepare("DELETE FROM users WHERE id = ? AND username != 'admin'")->execute([$id]);
    $msg = "User deleted!";
}
if (isset($_GET['del_appt'])) {
    $id = (int)$_GET['del_appt'];
    $pdo->prepare("DELETE FROM appointments WHERE id = ?")->execute([$id]);
    $msg = "Appointment deleted!";
}
if (isset($_GET['del_fb'])) {
    $id = (int)$_GET['del_fb'];
    $pdo->prepare("DELETE FROM feedback WHERE id = ?")->execute([$id]);
    $msg = "Feedback deleted!";
}
if (isset($_GET['del_inq'])) {
    $id = (int)$_GET['del_inq'];
    $pdo->prepare("DELETE FROM inquiries WHERE id = ?")->execute([$id]);
    $msg = "Inquiry deleted!";
}

// Fetch data
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
$appts = $pdo->query("SELECT a.*, u.name, u.username FROM appointments a JOIN users u ON a.user_id = u.id ORDER BY a.appointment_date DESC")->fetchAll();
$feedbacks = $pdo->query("SELECT * FROM feedback ORDER BY id DESC")->fetchAll();
$inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - UU Hospital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {font-family:Arial;background:#f4f8ff;margin:0;}
        header {background:#003366;color:white;padding:25px;text-align:center;}
        .container {max-width:1300px;margin:20px auto;padding:20px;}
        .stats {display:flex;gap:25px;flex-wrap:wrap;margin:30px 0;}
        .stat {background:#003366;color:white;padding:30px;border-radius:15px;flex:1;min-width:200px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.2);}
        .stat h2 {margin:10px 0;font-size:36px;}
        .card {background:white;padding:30px;margin:20px 0;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,0.1);}
        h2 {color:#003366;border-bottom:3px solid #003366;padding-bottom:10px;}
        table {width:100%;border-collapse:collapse;margin-top:15px;}
        th {background:#003366;color:white;padding:15px;text-align:left;}
        td {padding:12px;border-bottom:1px solid #ddd;}
        tr:hover {background:#f0f8ff;}
        .btn {background:#dc3545;color:white;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold;}
        .btn:hover {background:#c82333;}
        .msg {background:#d4edda;color:#155724;padding:15px;border-radius:10px;text-align:center;font-weight:bold;margin:20px 0;}
        .logout {display:block;width:200px;margin:40px auto;padding:15px;background:#dc3545;color:white;text-align:center;border-radius:10px;text-decoration:none;font-weight:bold;}
        .logout:hover {background:#c82333;}
    </style>
</head>
<body>

<header>
    <h1>UU Hospital - Admin Dashboard</h1>
    <p>Welcome, Administrator | <a href="?logout=1" style="color:yellow;text-decoration:underline;">Logout</a></p>
</header>

<div class="container">

    <?php if($msg): ?>
        <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="stats">
        <div class="stat"><h2><?php echo count($users); ?></h2><p>Total Users</p></div>
        <div class="stat"><h2><?php echo count($appts); ?></h2><p>Appointments</p></div>
        <div class="stat"><h2><?php echo count($feedbacks); ?></h2><p>Feedback</p></div>
        <div class="stat"><h2><?php echo count($inquiries); ?></h2><p>Inquiries</p></div>
    </div>

    <div class="card">
        <h2>All Registered Users</h2>
        <table>
            <tr><th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Action</th></tr>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo $u['username']; ?></td>
                <td><?php echo $u['email']; ?></td>
                <td>
                    <?php if($u['username'] !== 'admin'): ?>
                        <a href="?del_user=<?php echo $u['id']; ?>" class="btn" onclick="return confirm('Delete this user?')">Delete</a>
                    <?php else: ?>
                        <em>Protected</em>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <h2>All Appointments</h2>
        <table>
            <tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Reason</th><th>Action</th></tr>
            <?php foreach($appts as $a): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($a['name']); ?></strong><br>(@<?php echo $a['username']; ?>)</td>
                <td><?php echo htmlspecialchars($a['doctor']); ?></td>
                <td><?php echo $a['appointment_date']; ?></td>
                <td><?php echo $a['appointment_time']; ?></td>
                <td><?php echo htmlspecialchars($a['reason']); ?></td>
                <td><a href="?del_appt=<?php echo $a['id']; ?>" class="btn" onclick="return confirm('Delete appointment?')">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <a href="?logout=1" class="logout">LOGOUT</a>
</div>

</body>
</html>