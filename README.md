UU Hospital Management System
Hospital Banner 
A simple, web-based hospital management system designed for managing patient registrations, appointments, feedback, and administrative tasks. This project is built as a demonstration with fun, cartoon-themed elements (e.g., doctor profiles inspired by popular characters like Doraemon, Pikachu, Tom & Jerry). It uses PHP sessions for data storage (no external database required), making it easy to set up and run.
Note: This is a basic prototype for educational purposes. In a real-world scenario, use a proper database (e.g., MySQL) and enhance security features.
Features

Admin Panel:
Secure login (default: username admin, password admin123).
View and manage registered users.
View, manage, and delete patient appointments.
View and delete patient feedback and inquiries.
Logout functionality.

Patient Portal:
User signup and login.
Book appointments with doctors (select doctor, date, time, and reason).
View upcoming appointments.
Submit inquiries (e.g., registration, booking, visitor info).
Logout functionality.

Feedback System:
Patients can submit feedback with name, email, and message.
Admin can view and delete feedback entries.

Doctor Profiles:
Static profiles for doctors with specialties, experience, and bios (fun cartoon themes).

Other Pages:
Home page with "About Us" section.
Contact Us page with hospital details.
Client-side form validation using JavaScript.

General:
Responsive design for mobile and desktop.
Session-based data persistence (users, appointments, feedback, inquiries).
No database required – all data is stored in PHP sessions (resets on server restart).


Technologies Used

Backend: PHP (session management for data storage).
Frontend: HTML5, CSS3 (with gradients, shadows, and responsive layouts).
Scripting: JavaScript (form validation in validate.js).
No external dependencies: Runs on a basic PHP server like XAMPP or WAMP.
Styling: Custom CSS with comic-inspired fonts (e.g., "Comic Sans MS" for titles).

Installation

Prerequisites:
PHP 7+ (with session support enabled).
A local web server (e.g., XAMPP, MAMP, or built-in PHP server).

Clone the Repository:textgit clone https://github.com/your-username/uu-hospital.git
Set Up the Server:
Place the project folder in your server's root directory (e.g., htdocs in XAMPP).
Start your server (Apache) and ensure PHP is running.

Access the Application:
Open a browser and go to http://localhost/uu-hospital/home.html (or the project folder path).


Note: Since data is stored in sessions, it will reset when the server restarts or sessions expire. For persistent storage, integrate a database.
Usage
Admin Access

Navigate to admin.php.
Login with:
Username: admin
Password: admin123

Manage users, appointments, feedback, and inquiries from the dashboard.

Patient Access

Navigate to patient.php.
Sign up with a name, email, username, and password.
Login to access the dashboard (login.php).
Book appointments, submit inquiries, and view your data.

Other Pages

Home: home.html – About the hospital.
Doctors: doctor.php – View doctor profiles.
Feedback: feedback.php – Submit feedback.
Contact: contact.html – Hospital contact info.

Contributing
Contributions are welcome! Please follow these steps:

Fork the repository.
Create a new branch (git checkout -b feature-branch).
Commit your changes (git commit -m 'Add new feature').
Push to the branch (git push origin feature-branch).
Open a Pull Request.
