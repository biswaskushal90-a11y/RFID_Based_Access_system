# RFID Based Access System

## Overview

The RFID Based Access System is a web-based access control and attendance management system developed using ESP32, RC522 RFID Reader, PHP, MySQL, HTML, CSS, and JavaScript.

It allows users to authenticate RFID cards, maintains access records in real time, and provides an admin dashboard to monitor attendance and manage registered RFID cards.

## Features

- RFID Card Authentication
- Real-Time Access Logging
- Secure Access Control
- Admin Login System
- RFID Card Enrollment
- Add, Edit, and Delete RFID Cards
- Automatic UID Detection
- Attendance and Access Monitoring
- Student Record Search
- Responsive Web Dashboard
- Automatic Log Refresh

## System Workflow

1. The user scans an RFID card using the RC522 reader.
2. The ESP32 reads the RFID card's unique ID (UID).
3. The UID is transmitted to the PHP server through Wi-Fi.
4. The server verifies the UID against the database.
5. Access is granted or denied based on the authentication result.
6. Scan information is recorded in the MySQL database.
7. The admin dashboard displays access logs in real time.
8. Administrators can manage registered RFID cards through the Card Management Panel.

## Technologies Used

### Hardware
- ESP32
- RC522 RFID Reader

### Software
- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- XAMPP

## Project Structure

```text
RFID_PROJECT/
│
├── arduino_rfid2.ino
├── index.php
├── login.php
├── logout.php
├── manage_cards.php
├── data_upload.php
├── latest_uid.php
├── get_uid.php
├── check_uid.php
├── dbcon.php
└── README.md
```

## Database Tables

### authorized_cards
Stores information about registered and authorized RFID cards.

### access_log
Maintains access history, including UID, Name, Status, and Scan Time.

## Installation

1. Download and install [XAMPP](https://www.apachefriends.org/).
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Create a MySQL database named:

```sql
access
```

4. Create or import the required database tables.
5. Place the project files inside the XAMPP directory:

```text
htdocs/RFID_PROJECT
```

6. Configure the database credentials in:

```php
dbcon.php
```

7. Upload the Arduino code to the ESP32.
8. Connect the ESP32 with the RC522 RFID Reader.
9. Open the project in your browser:

```text
http://localhost/RFID_PROJECT
```

## Screenshots

The following screenshots demonstrate the system interface:

- Admin Dashboard
- Admin Login Page
- RFID Card Management Panel
- RFID Scanning and Access Logs

## Future Enhancements

- Email Notifications
- SMS Alerts
- Face Recognition Integration
- Fingerprint Authentication
- Cloud Database Integration
- Mobile Application

## Author

**Kushal Biswas**

BCA Student  
EIILM Kolkata

## License

This project is developed for educational and learning purposes.