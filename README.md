# SAHYOG - Smart Donation Platform

SAHYOG is a smart donation platform designed to bridge the gap between **Donors** and **Receivers (NGOs)** with the support of **Admins**. It allows verified donors to post items for donation, and verified receivers to request these items. Admins manage the entire flow from verification to donation delivery.

This project was developed as a **Final Semester Web Development Project**.

---

## 🌟 Features

- Role-based access: Donor, Receiver, Admin
- Secure registration with document verification
- Donors can post donation items (with images, expiry, location)
- Receivers can view and request donations
- Admin approval flow for:
  - User registrations
  - Donation requests
  - Pickup coordination
- Notifications via dashboard and email
- OTP-based password reset system
- Dashboard stats for each role

---

## 📁 Folder Structure
sahyog/
├── assets/ # CSS, images
├── dashboard/
│ ├── admin/
│ ├── donor/
│ └── receiver/
├── includes/ # Database & session handling
├── uploads/ # User-uploaded documents/photos
├── vendor/ #phpmailer, composer, other mailing env
├── index.php # Landing page
├── login.php # Login page
├── logout.php # Logout handler
├── register.php # Registration form
├── reset_password.php # OTP-based password reset
├── sahyog_schema.pdf # 📌 Database Schema

---

## 🧩 Tech Stack

- **Frontend:** HTML, CSS, Bootstrap, JavaScript
- **Backend:** PHP (Vanilla PHP, no frameworks)
- **Database:** MySQL
- **Email:** PHPMailer for sending OTPs and notifications

---

## 🛠️ Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/sahyog.git
   cd sahyog
2. **Set up the Database**
   Import the schema provided in the sahyogdbschema.pdf file (found in the root directory).
   Use a tool like phpMyAdmin or the MySQL command line to recreate the sahyogdb database.
   
3. **Configure database connection**
   Go to includes/database.php
   Update the credentials if needed:
   $conn = mysqli_connect("localhost", "root", "", "sahyogdb");

4. **Set up the local server**
   Use XAMPP, MAMP, or similar.
   Place the project in your htdocs folder.
   Visit http://localhost/sahyog/index.php
   
-----

**📄 Database Schema**
📌 The full database schema (tables, fields, relationships) is included in sahyogdbschema.pdf.
Please refer to this before creating or importing the database.

--------

**🤝 Contributors**
1. Soumya Ranjan Dash – Lead Developer
2. Jyoti Ranjan Jena – Backend and Database
3. Biswaranjan Behera – Frontend Design
4. Sudhrutee Biswal - SRS Design
5. Jayashree Nayak - SRS Design
