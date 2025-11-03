# Dakoii Admin Portal - Setup Guide

## 🎯 Quick Setup (Choose ONE method)

### **Method 1: Using Browser (Recommended - Easiest)**

1. **Open your browser** and navigate to:
   ```
   http://localhost/corres/run_migration.php
   ```

2. **Wait for the script to complete**. You should see:
   ```
   ✓ Migrations completed successfully!
   ✓ Table 'dakoii_users' created successfully!
   ✓ Seeder completed successfully!
   ```

3. **Access the Dakoii Admin Portal**:
   ```
   http://localhost/corres/dakoii
   ```

4. **Login with these credentials**:
   - **Username**: `fkenny`
   - **Password**: `dakoii`

---

### **Method 2: Using Command Line**

1. **Open Command Prompt or Terminal** in the project directory:
   ```bash
   cd C:\xampp\htdocs\corres
   ```

2. **Run the migration**:
   ```bash
   php spark migrate
   ```

3. **Run the seeder**:
   ```bash
   php spark db:seed DakoiiUserSeeder
   ```

4. **Access the portal**:
   ```
   http://localhost/corres/dakoii
   ```

5. **Login credentials**:
   - **Username**: `fkenny`
   - **Password**: `dakoii`

---

### **Method 3: Using phpMyAdmin (Manual SQL)**

1. **Open phpMyAdmin** in your browser:
   ```
   http://localhost/phpmyadmin
   ```

2. **Select the database** `corres_db`

3. **Click on "SQL" tab**

4. **Generate password hash first**:
   - Open: `http://localhost/corres/generate_password.php`
   - Copy the generated hash

5. **Run this SQL** (replace the hash with the one you copied):
   ```sql
   CREATE TABLE IF NOT EXISTS `dakoii_users` (
     `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
     `name` VARCHAR(255) NOT NULL,
     `username` VARCHAR(100) NOT NULL,
     `password` VARCHAR(255) NOT NULL,
     `created_at` DATETIME DEFAULT NULL,
     `updated_at` DATETIME DEFAULT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `username` (`username`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   INSERT INTO `dakoii_users` (`name`, `username`, `password`, `created_at`, `updated_at`) 
   VALUES ('Free Kenny', 'fkenny', 'PASTE_HASH_HERE', NOW(), NOW());
   ```

6. **Access the portal**:
   ```
   http://localhost/corres/dakoii
   ```

---

## 📋 Default Credentials

After setup, use these credentials to login:

- **Name**: Free Kenny
- **Username**: `fkenny`
- **Password**: `dakoii`

---

## ✅ Verification Steps

After running the setup, verify everything is working:

1. **Check if table exists**:
   - Open phpMyAdmin
   - Select `corres_db` database
   - Look for `dakoii_users` table

2. **Check if user exists**:
   - Click on `dakoii_users` table
   - You should see one row with username `fkenny`

3. **Test login**:
   - Go to: `http://localhost/corres/dakoii`
   - Enter username: `fkenny`
   - Enter password: `dakoii`
   - Click "Sign In"
   - You should be redirected to the dashboard

---

## 🔧 Troubleshooting

### Issue: "Table already exists" error
**Solution**: The table is already created. Just run the seeder:
```bash
php spark db:seed DakoiiUserSeeder
```

### Issue: "User already exists" error
**Solution**: The user is already in the database. You can:
- Try logging in with existing credentials
- Or delete the user and re-run the seeder:
  ```sql
  DELETE FROM dakoii_users WHERE username = 'fkenny';
  ```

### Issue: Can't access run_migration.php
**Solution**: Make sure:
- XAMPP Apache is running
- You're using the correct URL: `http://localhost/corres/run_migration.php`
- The file exists in the root directory

### Issue: "Invalid username or password"
**Solution**: 
- Make sure you're using the correct credentials
- Username: `fkenny` (lowercase)
- Password: `dakoii` (lowercase)
- If still not working, regenerate the password hash using `generate_password.php`

---

## 📁 Helper Files Created

- **run_migration.php** - Automated setup script (browser or CLI)
- **generate_password.php** - Password hash generator
- **dakoii_setup.sql** - Manual SQL setup file
- **DAKOII_SETUP_GUIDE.md** - This guide

---

## 🚀 Next Steps

After successful login:

1. Explore the dashboard
2. Change the default password (feature to be implemented)
3. Start building your admin features

---

## 📞 Support

If you encounter any issues:
1. Check the troubleshooting section above
2. Verify your database connection in `app/Config/Database.php`
3. Make sure XAMPP MySQL is running
4. Check PHP error logs in `writable/logs/`

---

**Happy Administrating! 🎉**

