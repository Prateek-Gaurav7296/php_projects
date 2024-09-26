<?php
// MySQL connection details
$host = 'localhost';
$db = 'url_shortener';
$user = 'root';
$pass = 'Test@1234';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Fetch original URL and access frequency from database
    $sql = "SELECT original_url, access_frequency FROM urls WHERE short_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $stmt->bind_result($url, $access_frequency);
    $stmt->fetch();
    $stmt->close();

    if ($url) {
        // Increment the access_frequency by 1
        $new_access_frequency = $access_frequency + 1;
        $updateSql = "UPDATE urls SET access_frequency = ? WHERE short_code = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param('is', $new_access_frequency, $code);
        $updateStmt->execute();
        $updateStmt->close();

        // Redirect to original URL
        header("Location: $url");
        exit();
    } else {
        echo "URL not found.";
    }
} else {
    echo "Invalid request.";
}

// Close connection
$conn->close();
?>