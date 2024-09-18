<?php
// MySQL connection details
$host = 'localhost';
$db = 'url_shortener';
$user = 'root';
$pass = 'Test@1234';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateShortCode($length = 6) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

if (isset($_POST['submit'])) {
    $url = $_POST['url'];
    
    // Check if URL is valid
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $error = "Please enter a valid URL.";
    } else {
        // Check if the URL already exists in the database
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
            // Generate a unique short code
            $short_code = generateShortCode();
            $sql = "INSERT INTO urls (original_url, short_code) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $url, $short_code);
            $stmt->execute();
            $stmt->close();
        }
        $shortened_url = "http://localhost/url_shortener/redirect.php?code=$short_code";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
</head>
<body>
    <h1>URL Shortener</h1>

    <form action="index.php" method="post">
        <input type="text" name="url" placeholder="Enter your URL" required>
        <button type="submit" name="submit">Shorten URL</button>
    </form>

    <?php if (isset($shortened_url)): ?>
        <p>Shortened URL: <a href="<?= $shortened_url ?>"><?= $shortened_url ?></a></p>
    <?php elseif (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>
</body>
</html>
