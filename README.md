# Ice Cream Parlour Management System

An academic full-stack web project for managing an ice cream parlour's operations, including digital ordering, billing, and admin management.

## Project Overview
This system is designed to automate the manual processes of an ice cream shop. It provides a platform for customers to browse flavors, place orders, and download receipts, while allowing admins to manage the product catalog and view sales.

**Tech Stack:**
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend:** PHP (Native)
- **Database:** MySQL
- **Tooling:** XAMPP/WAMP
https://www.gianisicecream.com/

## Features
### User / Customer
1.  **Authentication**: Secure Signup and Login.
2.  **Digital Menu**: Browse available ice cream flavors with descriptions and prices.
3.  **Shopping Cart**: Add items, update quantities, and remove items.
4.  **Order Placement**: Simple one-click checkout.
5.  **Bill Generation**: Instant PDF receipt generation and download using `html2pdf.js`.
6.  **Order History**: View past orders.

### Admin
1.  **Authentication**: Secure Admin Login.
2.  **Product Management**: Add and Delete ice cream flavors.
3.  **Order Oversight**: View all customer orders and details.

## Installation & Setup
1.  **Software**: Ensure XAMPP or WAMP is installed.
2.  **Database**:
    -   The system checks for the database `icecream_db`.
    -   Run `install.php` in your browser **ONCE** to create the tables and seed default data.
    -   Default Database Config: `root` user, no password (edit `config.php` if different).
3.  **Run**:
    -   Place the project folder in `htdocs` (XAMPP) or `www` (WAMP).
    -   Open `http://localhost/php/install.php` to initialize.
    -   Go to `http://localhost/php/index.php` to start using the app.

## Accounts
- **Admin**: `admin@icecream.com` / `admin123`
- **User**: (Create your own via Signup)

## Key Files
- `index.php`: Landing page and menu.
- `cart.php`: Shopping cart.
- `invoice.php`: Displays receipt and PDF download logic.
- `admin_dashboard.php`: Admin control panel.
- `install.php`: Database setup script.

---
*Created for Academic Evaluation*
