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

// Get the short code from the URL
$code = $_GET['code'];

// Retrieve the original URL and update access frequency
$sql = "SELECT original_url, access_frequency FROM urls WHERE short_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $code);
$stmt->execute();
$stmt->bind_result($original_url, $access_frequency);
$stmt->fetch();
$stmt->close();

if ($original_url) {
    // Increment access frequency
    $access_frequency++;
    $update_sql = "UPDATE urls SET access_frequency = ? WHERE short_code = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('is', $access_frequency, $code);
    $stmt->execute();
    $stmt->close();
    // Redirect to the original URL
    header("Location: $original_url");
    exit();
} else {
    echo "URL not found!";
}
?>