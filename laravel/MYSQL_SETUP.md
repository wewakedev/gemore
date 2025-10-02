# MySQL Database Setup for Gemore Project

This guide will help you set up your Gemore project to use XAMPP's MySQL database instead of SQLite.

## Prerequisites

1. **XAMPP installed and running**
2. **MySQL service started in XAMPP Control Panel**
3. **PHP with MySQL extensions enabled**

## Step-by-Step Setup

### 1. Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services
3. Ensure both services show green status

### 2. Create Environment File

1. Copy the `.env.example` file to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Update the `.env` file with these database settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gemore
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### 3. Set Up MySQL Database

1. **Option A: Use the setup script (Recommended)**
   ```bash
   php setup_mysql.php
   ```

2. **Option B: Manual setup via phpMyAdmin**
   - Open http://localhost/phpmyadmin in your browser
   - Click "New" to create a new database
   - Enter "gemore" as the database name
   - Select "utf8mb4_unicode_ci" as the collation
   - Click "Create"

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Database Migrations

```bash
php artisan migrate
```

### 6. Start the Application

```bash
php artisan serve
```

Your application should now be running at http://localhost:8000

## Database Configuration Details

- **Host**: 127.0.0.1 (localhost)
- **Port**: 3306 (default MySQL port)
- **Database**: gemore
- **Username**: root (default XAMPP username)
- **Password**: (empty by default in XAMPP)

## Troubleshooting

### Common Issues

1. **"Connection refused" error**
   - Make sure MySQL service is running in XAMPP
   - Check if port 3306 is not blocked by firewall

2. **"Access denied" error**
   - Verify username is "root" and password is empty
   - Check if MySQL is configured to allow root access from localhost

3. **"Database not found" error**
   - Run the setup script: `php setup_mysql.php`
   - Or manually create the database in phpMyAdmin

4. **"PDO MySQL extension not loaded" error**
   - Enable MySQL extension in php.ini
   - Restart Apache service in XAMPP

### Verify MySQL Connection

You can test the MySQL connection by running:
```bash
php setup_mysql.php
```

This script will:
- Connect to MySQL server
- Create the database if it doesn't exist
- Test the connection
- Provide helpful error messages if something goes wrong

## File Changes Made

1. **`config/database.php`** - Changed default connection from 'sqlite' to 'mysql'
2. **`setup_mysql.php`** - Created setup script for MySQL database
3. **`.env.example`** - Updated with MySQL configuration (if you can create it)

## Next Steps

After successful setup:
1. Your Laravel application will use MySQL instead of SQLite
2. All existing migrations will create tables in MySQL
3. You can use phpMyAdmin to view and manage your database
4. The application will be more scalable and suitable for production use

## Support

If you encounter any issues:
1. Check XAMPP error logs
2. Verify MySQL service status
3. Test connection with the setup script
4. Ensure all required PHP extensions are enabled
