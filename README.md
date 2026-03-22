# Recipe Realm - Web App

## Setup Instructions

1. **How to import the database:**
   - Open XAMPP / WAMP and start Apache & MySQL.
   - Go to phpMyAdmin (http://localhost/phpmyadmin).
   - Create a new database named `recipe_realm`.
   - Click on the "Import" tab.
   - Choose the `database.sql` file included in this repository and click "Import" to restore all tables and data.

2. **How to run the project:**
   - Move the entire `project` (or `RecipeRealm`) folder to your local server directory (`htdocs` for XAMPP, `www` for WAMP).
   - Open a web browser and navigate to `http://localhost/RecipeRealm/`.
   - You can test authentication by registering a new user, logging in, or simply exploring the featured recipes.
   - To test the database-driven queries, go to `contact.php` and submit a test message.

