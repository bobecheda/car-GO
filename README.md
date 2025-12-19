# 🚗 Car-Go Rentals
### Premium Car Rental Management System

![Project Status](https://img.shields.io/badge/Status-Active-success?style=flat-square)
![Tech Stack](https://img.shields.io/badge/Stack-PHP%20%7C%20MySQL%20%7C%20JS-blue?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-lightgrey?style=flat-square)

**Car-Go Rentals** is a modern, responsive, and fully functional car rental management system designed to streamline the vehicle rental process. It features a high-performance public landing page, a dedicated customer portal for seamless bookings, and a robust admin dashboard for complete fleet and user management.

---

## ✨ Key Features

### 🌐 Public Landing Page (`pages/index.php`)
A visually immersive experience built with **vanilla HTML, CSS, and JavaScript** (no heavy frameworks).
- **Hero Section**: Full-screen immersive background with smooth parallax effects and clear CTAs.
- **Modern UI**: Dark-themed aesthetic with "Accent Yellow" highlights for a premium feel.
- **Interactive Elements**:
  - **Scroll Reveal Animations**: Elements fade and slide in using the `IntersectionObserver` API.
  - **Dynamic Counters**: "Stats & Achievements" section with animated number counting.
  - **Accordion FAQ**: Smooth expand/collapse animations for common questions.
  - **Testimonial Carousel**: Auto-playing slider showcasing customer feedback.
- **Responsive Design**: Fully optimized for Desktop, Tablet, and Mobile devices.

### 👤 Customer Portal (`customer/`)
A personal dashboard for users to manage their rental experience.
- **Browse & Book**: View available vehicles with detailed specs and pricing.
- **Booking Management**: Track booking status (Pending, Approved, Completed, Rejected) in real-time.
- **My Bookings**: A clean data table history of all past and current rentals.
- **Profile Management**: Update personal details and account settings.

### 🛠 Admin Dashboard (`admin/`)
A powerful control center for business owners.
- **Dashboard Overview**: At-a-glance metrics (Total Cars, Active Bookings, Revenue).
- **Fleet Management**: Add, edit, and remove vehicles (`manage_vehicles.php`).
- **Booking Control**: Approve or reject customer booking requests (`manage_bookings.php`).
- **User Management**: Oversee customer accounts (`manage_customers.php`).

---

## 💻 Technology Stack

| Component | Technology | Description |
| :--- | :--- | :--- |
| **Frontend** | HTML5, CSS3 | Semantic markup, CSS Grid/Flexbox, CSS Variables for theming. |
| **Scripting** | JavaScript (ES6+) | Native DOM manipulation, Event handling, Performance optimizations (RequestAnimationFrame). |
| **Backend** | PHP | Server-side logic, Session management, Secure database interactions. |
| **Database** | MySQL | Relational database for storing users, vehicles, and bookings. |
| **Server** | Apache (XAMPP) | Local development server environment. |

---

## 🚀 Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/index.html) (or any PHP/MySQL environment).
- A modern web browser (Chrome, Firefox, Edge).

### Steps
1.  **Clone/Download** the project to your web server directory (e.g., `C:\xampp\htdocs\car_rental_system`).
2.  **Start Services**: Open XAMPP Control Panel and start **Apache** and **MySQL**.
3.  **Database Setup**:
    - Open PHPMyAdmin (`http://localhost/phpmyadmin`).
    - Create a new database named `car_rental_db` (or check `config/db.php` for the configured name).
    - Import the provided SQL file (if available) or create tables for `users`, `vehicles`, `bookings`.
4.  **Configuration**:
    - Ensure `config/db.php` has the correct database credentials:
      ```php
      $servername = "localhost";
      $username = "root";
      $password = "";
      $dbname = "car_rental_db";
      ```
5.  **Run the Project**:
    - Open your browser and navigate to: `http://localhost/car_rental_system/pages/index.php`

---

## 📂 Project Structure

```bash
car_rental_system/
├── admin/                 # Admin dashboard & management scripts
│   ├── dashboard.php      # Main admin overview
│   ├── manage_bookings.php# Booking approval interface
│   └── ...
├── customer/              # Customer portal scripts
│   ├── index.php          # Customer dashboard
│   ├── booking.php        # Vehicle booking flow
│   ├── my_bookings.php    # User booking history
│   └── ...
├── pages/                 # Public facing pages
│   ├── index.php          # Main Landing Page (Hero, About, etc.)
│   ├── styles/            # Landing page CSS (landing.css)
│   └── scripts/           # Landing page JS (landing.js)
├── config/                # Configuration files
│   └── db.php             # Database connection
├── uploads/               # Vehicle images storage
├── assets/                # Shared assets (global CSS)
├── login.php              # Authentication entry point
├── register.php           # New user registration
└── README.md              # Project documentation
```

---

## 🎨 UI/UX Highlights

- **Performance**: Scroll events are throttled using `requestAnimationFrame` for buttery smooth performance.
- **Accessibility**: Semantic HTML tags (`<nav>`, `<main>`, `<section>`, `<footer>`) used throughout.
- **Consistency**: All buttons and interactive elements use a unified "Accent Yellow" color scheme.

---

## 👥 Credits

**Developed by Car-Go Dev Team**
- *Reliable. Affordable. Always on the Go.*

---

© 2025 Car-Go Rentals. All Rights Reserved.
