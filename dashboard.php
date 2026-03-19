<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php#login");
    exit;
}
header("Location: index.php#profile");
exit;

