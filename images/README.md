# Goswami Industry - E-Commerce Website

A fully functional, modern, premium e-commerce website built with HTML5, CSS3, JavaScript (Vanilla), PHP, and MySQL.

## Features Included
- Modern UI with dark theme and gold/orange accents
- Fully responsive on all devices
- **Voice Search** via Web Speech API
- **AI Chatbot UI**
- Add to Cart functionality using PHP Sessions
- User Authentication (Login / Signup)
- Admin Panel (Dashboard, Manage Products, Upload Images, Manage Orders)
- Secure password hashing and SQL injection prevention
- Smooth animations and hover effects

---

## 🚀 How to Run Locally with XAMPP

1. **Install XAMPP**: If you don't have it, download and install XAMPP from apachefriends.org.
2. **Move Files**: Move the entire `GOSWAMI INDUSTRIES` folder into your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\GOSWAMI INDUSTRIES`).
3. **Start Servers**: Open the XAMPP Control Panel and start **Apache** and **MySQL**.
4. **Database Setup**:
   - Open your browser and go to `http://localhost/phpmyadmin/`.
   - You don't need to manually create the database, simply click on the **Import** tab.
   - Choose the `database.sql` file located in your project folder.
   - Click **Import** (or "Go" at the bottom). This will create the `goswami_industry` database, tables, and insert sample data (including an admin account).
5. **Access the Website**: 
   - Open your browser and go to: `http://localhost/GOSWAMI INDUSTRIES/`
   
*(Note: If you are running it using the PHP built-in server that I started on port 8000, you can access it at `http://localhost:8000`. However, you still need MySQL running on port 3306 with a user `root` and no password to connect to the database.)*

---

## 🔐 Credentials

### Admin Login
- **Email**: `admin@goswami.com`
- **Password**: `admin123`

You can also create a regular user account from the Signup page.

---

## 📁 Directory Structure
- `/css` - Contains the `style.css` for the custom premium UI.
- `/js` - Contains `script.js` handling the Chatbot, Voice Search, and UI animations.
- `/images` - Uploaded product images are saved here.
- `/includes` - Contains reusable `header.php`, `footer.php`, and database connection `db.php`.
- `/admin` - Contains the secure admin panel files.
- `database.sql` - The complete database schema with dummy data.
