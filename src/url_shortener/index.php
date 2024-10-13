<?php

// MySQL connection details
$host = 'db';
$db = 'url_shortener';
$user = 'app_user';
$pass = 'app_password';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateShortCode($url) {
    $secret_key = bin2hex(random_bytes(32));
    $hashed_url = hash_hmac('sha256', $url, $secret_key);
    return substr($hashed_url, 0, 8);
}

if (isset($_POST['submit'])) {
    $url = $_POST['url'];

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $error = "Please enter a valid URL.";
    } else {
        $sql = "SELECT short_code FROM urls WHERE original_url = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $url);
        $stmt->execute();
        $stmt->bind_result($existing_code);
        $stmt->fetch();
        $stmt->close();

        if ($existing_code) {
            $short_code = $existing_code;
        } else {
            $short_code = generateShortCode($url);
            $date = new DateTime("now", new DateTimeZone('Asia/Kolkata'));
            $created_date = $date->format('Y-m-d H:i:s');
            $access_frequency = 0;
            $sql = "INSERT INTO urls (original_url, short_code, created_date, access_frequency) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $url, $short_code, $created_date, $access_frequency);
            $stmt->execute();
            $stmt->close();
        }
        $shortened_url = "/url_shortener/redirect.php?code=$short_code";
    }
}

// Delete functionality
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM urls WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch data to display in the table
$sql = "SELECT id, short_code, created_date, access_frequency FROM urls";
$result = $conn->query($sql);
include 'view.html';
?>

<?php
$conn->close();
?>