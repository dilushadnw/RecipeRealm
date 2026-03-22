<?php
require_once "includes/db.php";
require_once "includes/functions.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $userMessage = trim($_POST["message"] ?? "");

    if (!$name || !$email || !$userMessage) {
        $message = "<p style='color:red;'>All fields are required.</p>";
    } else {
        $stmt = $db->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $userMessage);
        
        if ($stmt->execute()) {
            $message = "<p style='color:green;'>Your message has been sent successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Failed to send message.</p>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - Recipe Realm</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contact-container { max-width: 600px; margin: 100px auto; padding: 2rem; background: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .contact-container h1 { margin-bottom: 1rem; color: var(--primary-color); }
        .contact-form .form-group { margin-bottom: 1rem; }
        .contact-form label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .contact-form input, .contact-form textarea { width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; }
        .contact-form button { margin-top: 1rem; width: 100%; }
        .back-link { display: inline-block; margin-top: 1rem; color: var(--text-light); text-decoration: none; }
        .back-link:hover { color: var(--primary-color); }
    </style>
</head>
<body>
    <div class="contact-container">
        <h1>Contact Us</h1>
        <?php echo $message; ?>
        <form class="contact-form" action="contact.php" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn primary-btn">Send Message</button>
        </form>
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
</body>
</html>
