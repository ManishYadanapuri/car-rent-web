# LuxRide - Premium Car Rental Website

## Project Overview
LuxRide is a web-based premium car rental platform designed to provide users with a seamless experience for browsing, booking, and managing car rentals. The platform includes a user-facing website for car selection and booking, as well as an administrative panel for managing users, cars, bookings, and payments.

## Features

### User-Facing Features
*   **User Authentication:** Secure registration and login system with session management.
*   **Car Catalog:** Browse a wide range of premium cars, categorized by type (e.g., electric, family, luxury, offroad, pickup, sedan, sports, supercar, SUV, vintage).
*   **Car Filtering:** Filter cars based on various criteria.
*   **Car Booking:** Users can select a car, specify pickup location, rate type, and duration. Requires upload of Aadhaar and Driving License documents.
*   **Payment Processing:** Integration for handling payments, updating booking status upon successful payment.
*   **Wishlist:** Users can add cars to a personal wishlist.
*   **User Dashboard:** A personalized area for users to view their bookings and manage their profile.

### Admin Panel Features
*   **Admin Authentication:** Separate login for administrators.
*   **Dashboard Overview:** Centralized view of key statistics, including user counts, car metrics, booking summaries, and revenue totals.
*   **User Management:** View, add, edit, delete, and toggle status (active/suspended) of user accounts.
*   **Car Management:** CRUD operations for managing the car inventory.
*   **Booking Management:** View, add, update status (pending, confirmed, active, completed, cancelled), and delete bookings.
*   **Payment Management:** View payment records and financial statistics.
*   **Activity Logs:** Track user and admin activities within the system.

## Technology Stack

*   **Frontend:** HTML, CSS, JavaScript
*   **Backend:** PHP
*   **Database:** MySQL (accessed via PDO)
*   **Web Server:** Apache (typically used with XAMPP)

## Project Structure

The project is organized into the following main directories and files:

```
car-rent-web/
├── admin/                  # Admin panel files (dashboard, login)
├── api/                    # Backend API endpoints (PHP files for database interaction, authentication, booking, payment, etc.)
├── bgpic/                  # Background images
├── footers/                # Footer-related HTML files (e.g., about, terms, privacy)
├── img/                    # Car images and other visual assets, organized by car category
├── uploads/                # Directory for uploaded user documents (e.g., Aadhaar, Driving License)
│   └── documents/          # Stores uploaded documents for bookings
├── user/                   # User-specific files (e.g., dashboard)
├── cars.css                # Stylesheet for the car listing page
├── cars.html               # HTML for displaying car listings
├── cars.js                 # JavaScript for car filtering and interaction
├── features.js             # JavaScript for dynamic features on the main page
├── index.css               # Stylesheet for the main landing page
├── index.html              # Main landing page of the website
├── payment.php             # PHP file for payment processing (likely frontend integration)
├── premium.css             # Stylesheet for premium features or sections
├── signinUP.css            # Stylesheet for the sign-in/sign-up pages
└── signinUP.html           # HTML for user registration and login
```

## Setup and Installation

To set up the LuxRide project locally, follow these steps:

### Prerequisites
*   **XAMPP:** A local Apache distribution containing MySQL, PHP, and Perl. Download and install it from [Apache Friends](https://www.apachefriends.org/index.html).

### Database Setup
1.  **Start XAMPP:** Launch the XAMPP control panel and start the Apache and MySQL services.
2.  **Create Database:** Open phpMyAdmin (usually accessible via `http://localhost/phpmyadmin/`) and create a new database named `luxride`.
3.  **Database Schema:** The project relies on several tables. While a direct `.sql` schema file was not provided, the following tables can be inferred from the PHP API files:
    *   `users`: Stores user registration details (id, full_name, email, phone, password, status, login_count, last_login, created_at).
    *   `admins`: Stores administrator credentials (username, password).
    *   `cars`: Stores car details (id, name, brand, type, price, image, rating, seats, transmission, fuel, badge).
    *   `bookings`: Stores booking information (booking_ref, user_id, car_id, car_name, car_brand, user_name, user_email, cust_phone, pickup_location, rate_type, duration, total_amount, booking_type, aadhar_path, license_path, dl_number, status, created_at).
    *   `payments`: Stores payment transaction details (txn_id, booking_id, car_name, cust_name, cust_email, cust_phone, amount, payment_method, method_detail, status, created_at).
    *   `activity_logs`: Records system activities (user_email, action, description, ip_address, timestamp).
    *   `wishlist`: Stores user's wishlisted cars (id, user_id, car_id, added_at).

    *You will need to manually create these tables based on the fields mentioned in the respective PHP files (e.g., `api/register.php`, `api/cars_admin.php`, `api/create_booking.php`, `api/process_payment.php`, `api/admin_stats.php`, `api/wishlist.php`).*

### Project Deployment
1.  **Place Project Files:** Copy the entire `car-rent-web` folder into the `htdocs` directory of your XAMPP installation (e.g., `C:\xampp\htdocs\car-rent-web`).
2.  **Access Website:** Open your web browser and navigate to `http://localhost/car-rent-web/` for the user-facing website and `http://localhost/car-rent-web/admin/login.php` for the admin panel.

### Configuration
*   **Database Connection:** The database connection details are configured in `api/db.php`. Ensure the `$dbname`, `$username`, and `$password` variables match your MySQL setup. By default, for XAMPP, the username is `root` and the password is an empty string.

    ```php
    // api/db.php
    $host     = "localhost";
    $dbname   = "luxride";
    $username = "root";      // default XAMPP username
    $password = "";          // default XAMPP password is empty
    ```

## Usage

### User Flow
1.  **Registration/Login:** Access `signinUP.html` to create a new account or log in with existing credentials.
2.  **Browse Cars:** After logging in, navigate to `index.html` or `cars.html` to view available cars.
3.  **Book a Car:** Select a car and proceed with the booking process, providing necessary details and uploading documents.
4.  **Make Payment:** Complete the payment for the booking.
5.  **User Dashboard:** View booking history and other personal information on `user/dashboard.php`.

### Admin Flow
1.  **Admin Login:** Access `admin/login.php` and log in with administrator credentials.
2.  **Dashboard:** Upon successful login, you will be redirected to `admin/dashboard.php`, which provides an overview of the system.
3.  **Management:** Use the sidebar navigation to manage users, cars, bookings, and payments.

## API Endpoints (Key Examples)

*   `/api/register.php`: User registration.
*   `/api/login.php`: User login.
*   `/api/create_booking.php`: Handles new car bookings, including document uploads.
*   `/api/process_payment.php`: Processes payments for bookings.
*   `/api/get_cars.php`: Retrieves a list of available cars.
*   `/api/admin_stats.php`: Provides statistics for the admin dashboard.
*   `/api/cars_admin.php`: CRUD operations for cars (admin only).
*   `/api/admin_users.php`: CRUD operations for users (admin only).
*   `/api/admin_bookings.php`: Management of bookings (admin only).
*   `/api/admin_payments.php`: Management of payments (admin only).

## Security Considerations
*   **Password Hashing:** User and admin passwords are hashed using `PASSWORD_BCRYPT` for storage.
*   **Session Management:** PHP sessions are used for user and admin authentication, with appropriate cookie settings.
*   **File Uploads:** Document uploads are restricted by file type (jpg, jpeg, png, webp, pdf) and size (5MB limit).
*   **Input Validation:** Basic input validation is performed on various API endpoints to prevent common vulnerabilities.

## Credits

This project was developed by the LuxRide team.

---
*Generated by Manus AI on May 08, 2026*
