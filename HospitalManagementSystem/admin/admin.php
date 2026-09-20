<?php
session_start();

// ====================== ADMIN AUTHENTICATION ======================
$admin_username = "admin";
$admin_password = "admin123"; 

// Login handling
if (isset($_POST['admin_login'])) {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    if ($user === $admin_username && $pass === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "Invalid admin credentials!";
    }
}

// Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: admin.php");
    exit;
}

// Protect the page
if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - UU Hospital</title>
        <style>
            body { font-family: Arial; background: linear-gradient(135deg, #003366, #005599); height: 100vh; display: flex; justify-content: center; align-items: center; margin:0; }
            .login-box { background: white; padding: 40px; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.5); width:350px; text-align:center; }
            h2 { color:#003366; font-family:"Comic Sans MS", cursive; }
            input { width:100%; padding:12px; margin:10px 0; border:2px solid #003366; border-radius:5px; font-size:16px; }
            button { background:#003366; color:white; padding:12px; width:100%; border:none; border-radius:5px; font-weight:bold; cursor:pointer; }
            button:hover { background:#005599; }
            .error { color:red; margin-top:10px; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>Admin Login</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required autocomplete="off">
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="admin_login">Login as Admin</button>
                <?php if(isset($login_error)) echo "<p class='error'>$login_error</p>"; ?>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ====================== ADMIN IS LOGGED IN ======================

// Initialize session arrays if not exist
if (!isset($_SESSION['users'])) $_SESSION['users'] = [];
if (!isset($_SESSION['user_appointments'])) $_SESSION['user_appointments'] = [];
if (!isset($_SESSION['inquiries'])) $_SESSION['inquiries'] = [];
if (!isset($_SESSION['feedback'])) $_SESSION['feedback'] = [];

// (Medical reports feature completely removed)

// ====================== DELETE USER ======================
if (isset($_GET['delete_user'])) {
    $del_username = $_GET['delete_user'];
    foreach ($_SESSION['users'] as $key => $user) {
        if ($user['username'] === $del_username) {
            unset($_SESSION['users'][$key]);
            unset($_SESSION['user_appointments'][$del_username]);
            $msg = "User deleted successfully!";
            break;
        }
    }
    $_SESSION['users'] = array_values($_SESSION['users']);
}

// ====================== DELETE APPOINTMENT ======================
if (isset($_GET['delete_appt'])) {
    $username = $_GET['username'];
    $index = (int)$_GET['delete_appt'];
    if (isset($_SESSION['user_appointments'][$username][$index])) {
        unset($_SESSION['user_appointments'][$username][$index]);
        $_SESSION['user_appointments'][$username] = array_values($_SESSION['user_appointments'][$username]);
        $msg = "Appointment deleted!";
    }
}

// ====================== DELETE FEEDBACK ======================
if (isset($_GET['delete_feedback'])) {
    $index = (int)$_GET['delete_feedback'];
    if (isset($_SESSION['feedback'][$index])) {
        unset($_SESSION['feedback'][$index]);
        $_SESSION['feedback'] = array_values($_SESSION['feedback']);
        $msg = "Feedback deleted successfully!";
    }
}

// ====================== DELETE INQUIRY (NEW FEATURE) ======================
if (isset($_GET['delete_inquiry'])) {
    $index = (int)$_GET['delete_inquiry'];
    if (isset($_SESSION['inquiries'][$index])) {
        unset($_SESSION['inquiries'][$index]);
        $_SESSION['inquiries'] = array_values($_SESSION['inquiries']); // reindex
        $msg = "Inquiry deleted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - UU Hospital</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#f4f7fa; }
        header { background:#003366; color:white; padding:20px; text-align:center; font-size:28px; font-family:"Comic Sans MS", cursive; position:relative; }
        .container { max-width:1400px; margin:20px auto; padding:20px; }
        .card { background:white; border-radius:10px; padding:20px; margin-bottom:30px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
        h2 { color:#003366; border-bottom:3px solid #003366; padding-bottom:10px; }
        table { width:100%; border-collapse:collapse; margin:15px 0; }
        table, th, td { border:1px solid #003366; }
        th { background:#003366; color:white; padding:12px; }
        td { padding:10px; text-align:center; background:#f9f9f9; }
        tr:nth-child(even) td { background:#eef5ff; }
        .btn { padding:8px 15px; background:#003366; color:white; text-decoration:none; border-radius:5px; font-size:14px; }
        .btn:hover { background:#005599; }
        .btn-danger { background:#d32f2f; }
        .btn-danger:hover { background:#b71c1c; }
        .msg { padding:15px; background:#4caf50; color:white; text-align:center; border-radius:5px; margin-bottom:20px; }
        .error { padding:15px; background:#f44336; color:white; text-align:center; border-radius:5px; margin-bottom:20px; }
        .logout { float:right; background:#d32f2f; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; }
        .logout:hover { background:#b71c1c; }
    </style>
</head>
<body>

<header>
    <div style="max-width:1400px; margin:auto; position:relative;">
        UU Hospital - Admin Panel
        <a href="?logout=1" class="logout">Logout</a>
    </div>
</header>

<div class="container">

    <?php if(isset($msg)) echo "<div class='msg'>$msg</div>"; ?>

    <!-- All Registered Patients -->
    <div class="card">
        <h2>All Registered Patients (<?php echo count($_SESSION['users']); ?>)</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Registered On</th>
                <th>Action</th>
            </tr>
            <?php if(empty($_SESSION['users'])): ?>
                <tr><td colspan="5">No patients registered yet.</td></tr>
            <?php else: ?>
                <?php foreach($_SESSION['users'] as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo date('d M Y', strtotime($user['registered_on'] ?? 'today')); ?></td>
                    <td>
                        <a href="?delete_user=<?php echo urlencode($user['username']); ?>" 
                           class="btn btn-danger" onclick="return confirm('Delete this patient and all their data?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

    <!-- All Appointments -->
    <div class="card">
        <h2>All Appointments</h2>
        <table>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Reason</th>
                <th>Action</th>
            </tr>
            <?php 
            $has_appt = false;
            foreach($_SESSION['user_appointments'] ?? [] as $username => $appts):
                foreach($appts as $i => $appt):
                    $has_appt = true;
                    $patient_name = "Unknown";
                    foreach($_SESSION['users'] as $u) {
                        if($u['username'] === $username) { $patient_name = $u['name']; break; }
                    }
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient_name); ?> (@<?php echo $username; ?>)</td>
                    <td><?php echo htmlspecialchars($appt['doctor']); ?></td>
                    <td><?php echo $appt['date']; ?></td>
                    <td><?php echo $appt['time']; ?></td>
                    <td><?php echo htmlspecialchars($appt['reason']); ?></td>
                    <td>
                        <a href="?delete_appt=<?php echo $i; ?>&username=<?php echo urlencode($username); ?>" 
                           class="btn btn-danger" onclick="return confirm('Delete appointment?')">Delete</a>
                    </td>
                </tr>
            <?php 
                endforeach;
            endforeach;
            if(!$has_appt): ?>
                <tr><td colspan="6">No appointments booked yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- All Feedback Messages -->
    <div class="card">
        <h2>Patient Feedback</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Submitted On</th>
                <th>Action</th>
            </tr>
            <?php if(empty($_SESSION['feedback'])): ?>
                <tr><td colspan="5">No feedback received yet.</td></tr>
            <?php else: ?>
                <?php foreach(array_reverse($_SESSION['feedback']) as $index => $fb): 
                    $original_index = count($_SESSION['feedback']) - 1 - $index;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($fb['name']); ?></td>
                    <td><?php echo htmlspecialchars($fb['email']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($fb['message'])); ?></td>
                    <td><?php echo $fb['timestamp']; ?></td>
                    <td>
                        <a href="?delete_feedback=<?php echo $original_index; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Delete this feedback permanently?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

    <!-- All Inquiries (Now with Delete Option) -->
    <div class="card">
        <h2>Patient Inquiries / Messages</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Type</th>
                <th>Message</th>
                <th>Submitted On</th>
                <th>Action</th> <!-- New column -->
            </tr>
            <?php if(empty($_SESSION['inquiries'])): ?>
                <tr><td colspan="6">No inquiries yet.</td></tr>
            <?php else: ?>
                <?php foreach(array_reverse($_SESSION['inquiries']) as $index => $inq): 
                    $original_index = count($_SESSION['inquiries']) - 1 - $index;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($inq['name']); ?></td>
                    <td><?php echo htmlspecialchars($inq['email']); ?></td>
                    <td><?php echo htmlspecialchars($inq['type']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($inq['message'])); ?></td>
                    <td><?php echo $inq['timestamp']; ?></td>
                    <td>
                        <a href="?delete_inquiry=<?php echo $original_index; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Delete this inquiry permanently?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

</div>

</body>
</html>