<?php
// doctor.php - Doctor Profiles Page

$doctors = [
    [
        'name'       => 'Dr. Jerry',
        'specialty'  => 'Cardiology',
        'experience' => '15 years',
        'image'      => 'doc.png',
        'bio'        => 'Dr. Jerry is a renowned cardiologist with expertise in heart surgeries and treatments.'
    ],
    [
        'name'       => 'Dr. Tom Smith',
        'specialty'  => 'Neurology',
        'experience' => '12 years',
        'image'      => 'doc2.png',
        'bio'        => 'Dr. Tom Smith specializes in neurological disorders and has published numerous research papers.'
    ],
    [
        'name'       => 'Dr. Pikachu',
        'specialty'  => 'Orthopedics',
        'experience' => '10 years',
        'image'      => 'doc3.png',  // Fixed typo: was 'doc3.ppg'
        'bio'        => 'Dr. Pikachu is an expert in orthopedic surgeries and sports medicine.'
    ],
    [
        'name'       => 'Dr. Doraemon',
        'specialty'  => 'Pediatrics',
        'experience' => '8 years',
        'image'      => 'do.png',
        'bio'        => 'Dr. Doraemon focuses on child healthcare and has a compassionate approach to pediatric care.'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors -Uttara Unity Hospital</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        header {
            background-color: #003366;
            background-image: url(img/18691897.jpg);
            background-size: cover;
            color: #ffffff;
            padding: 20px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .logo {
            width: 100px;
            height: 100px;
            background-image: url(logo.png);
            background-size: cover;
            border-radius: 50%;
            margin-right: 20px;
        }
        .title {
            font-size: 45px;
            font-family: "Comic Sans MS", cursive;
            font-weight: bold;
            text-shadow: 2px 2px 4px #000;
        }
        nav {
            background-color: #ffffff;
            border: 1px solid #003366;
            border-radius: 5px;
            margin-top: 10px;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: space-around;
        }
        nav a {
            text-decoration: none;
            color: #003366;
            font-weight: bold;
            font-family: "Comic Sans MS", cursive;
            font-size: 21px;
            padding: 15px;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        nav a:hover {
            color: #ffffff;
            background-color: #003366;
        }
        .container {
            margin: 20px;
        }
        .doctors {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            padding: 20px;
        }
        .doctor-profile {
            background: radial-gradient(#ffffff, #f0f8ff);
            border: 3px solid #003366;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 6px 6px 20px rgba(0,0,0,0.5);
            width: 320px;
            text-align: center;
            transition: transform 0.3s;
        }
        .doctor-profile:hover {
            transform: translateY(-10px);
        }
        .doctor-profile img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #003366;
            margin-bottom: 15px;
        }
        .doctor-profile h3 {
            font-size: 26px;
            color: #003366;
            font-family: "Comic Sans MS", cursive;
            margin: 10px 0;
        }
        .doctor-profile p {
            margin: 8px 0;
            color: #333;
            line-height: 1.5;
        }
        footer {
            background-color: #003366;
            color: #ffffff;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
        footer a { color: #fff; text-decoration: underline; }
        @media (max-width: 768px) {
            .title { font-size: 30px; }
            nav ul { flex-direction: column; }
            .doctors { gap: 20px; }
            .doctor-profile { width: 90%; max-width: 350px; }
        }
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
        <section class="doctors">
            <?php foreach ($doctors as $doctor): ?>
                <div class="doctor-profile">
                    <img src="<?= htmlspecialchars($doctor['image']) ?>" 
                         alt="<?= htmlspecialchars($doctor['name']) ?>">
                    <h3><?= htmlspecialchars($doctor['name']) ?></h3>
                    <p><strong>Specialty:</strong> <?= htmlspecialchars($doctor['specialty']) ?></p>
                    <p><strong>Experience:</strong> <?= htmlspecialchars($doctor['experience']) ?></p>
                    <p><?= htmlspecialchars($doctor['bio']) ?></p>
                </div>
            <?php endforeach; ?>
        </section>
    </div>

    <footer>
        <p>Copyright &copy; <span id="year"></span> - All Rights Reserved - <a href="#">UU Hospital</a></p>
        <p>Powered By UU Hospital</p>
        <p>Developed by - ASIF HASAN ONTU</p>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>