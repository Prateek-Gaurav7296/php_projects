<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// MySQL connection details
$host = "localhost";
$db = "url_shortener";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Fetch original URL from database
    $sql = "SELECT original_url FROM urls WHERE short_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $stmt->bind_result($url);
    $stmt->fetch();
    $stmt->close();

    if ($url) {
        // Redirect to original URL
        header("Location: $url");
        exit();
    } else {
        echo "URL not found.";
    }
} else {
    echo "Invalid request.";
}
?>