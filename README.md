# UNITEN CareSpace

UNITEN CareSpace is a web-based mental health support platform developed as a Final Year Project (FYP) for Universiti Tenaga Nasional (UNITEN).  
The system is designed to provide a safe and structured environment for students to access counseling services, while allowing counselors and administrators to efficiently manage sessions and data.

---

## 🎯 Project Objectives

- To provide UNITEN students with an accessible platform for mental health support
- To enable counselors to manage counseling sessions and student records efficiently
- To allow administrators to monitor system usage and manage users
- To apply proper UI/UX and HCI principles in a real-world web system
- To demonstrate full-stack web development skills using PHP and MySQL

---

## 👥 User Roles

### 1. Student
- Register and log in to the system
- View personal profile information
- Register for mental health-related events or counseling sessions
- Access relevant information provided by the platform

### 2. Counselor / Therapist
- Log in securely
- View assigned students
- Manage counseling sessions
- Upload session-related information or reports

### 3. Admin
- Manage users (students and counselors)
- Monitor registrations and system data
- Access administrative dashboards

---

## ✨ Key Features

- Role-based login system (Student / Counselor / Admin)
- Secure session handling
- Event and counseling registration system
- Profile management (email and phone number update)
- Admin dashboard for monitoring and management
- Organized database structure for scalability

---

## 🛠️ Technologies Used

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Server Environment:** XAMPP
- **Version Control:** Git & GitHub

---

## 🗂️ Project Structure

APP/
├── README.md
├── main/
│ ├── admin/
│ ├── counsellor/
│ ├── student/
│ ├── uploads/
│ ├── admin_page.php
│ ├── counsellor_page.php
│ ├── mainpage.php
│ └── config.php
├── database/
│ ├── UNITEN_CARESPACE_admin.sql
│ ├── UNITEN_CARESPACE_user.sql
│ ├── UNITEN_CARESPACE_therapist.sql
│ └── UNITEN_CARESPACE_pictures.sql

---

## 🗄️ Database Setup

This project uses **MySQL** and includes **4 SQL files** located in the `database/` folder:

1. `UNITEN_CARESPACE_admin.sql` – Admin accounts
2. `UNITEN_CARESPACE_user.sql` – Student user accounts
3. `UNITEN_CARESPACE_therapist.sql` – Counselor accounts
4. `UNITEN_CARESPACE_pictures.sql` – Uploaded images and related data

### Steps:
1. Open **phpMyAdmin**
2. Create a database (e.g. `uniten_carespace`)
3. Import all 4 `.sql` files into the database
4. Update database credentials in `config.php`

---

## ▶️ How to Run the Project

1. Install **XAMPP**
2. Place the project folder inside:
3. Start **Apache** and **MySQL** from XAMPP
4. Import the database files via phpMyAdmin
5. Open a browser and go to:
http://localhost/APP/main/mainpage.php

---

## 🔐 Security Notes

- Passwords are stored in plain text for academic purposes
- `config.php` is excluded from GitHub to prevent credential leakage
- Further improvements can include password hashing and input sanitization

---

## 👩‍💻 Created By

**Yasmine Essam**  
Bachelor of Information Systems  
Universiti Tenaga Nasional (UNITEN)

---

## 📌 Disclaimer

This system was developed strictly for **academic purposes** as part of a Final Year Project and is not intended for production use.
