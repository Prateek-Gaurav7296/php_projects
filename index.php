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
            $created_date = date('Y-m-d H:i:s');
            $access_frequency = 0;
            $sql = "INSERT INTO urls (original_url, short_code, created_date, access_frequency) VALUES (?, ?, ?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $url, $short_code,$created_date,$access_frequency);
            $stmt->execute();
            $stmt->close();
        }
        $shortened_url = "/redirect.php?code=$short_code";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>URL Shortener</h1>

    <form action="index.php" method="post">
        <input type="text" name="url" placeholder="Enter your URL" required>
        <button type="submit" name="submit">Shorten URL</button>
    </form>

    <?php if (isset($shortened_url)): ?>
        <p>Shortened URL: <a href="<?= $shortened_url ?>"><?= $short_code ?></a></p>
    <?php elseif (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <table style="width:100%">
  <tr>
    <td></td>
    <td>Maria Anders</td>
    <td>Germany</td>
  </tr>
  <tr>
    <td>Centro comercial Moctezuma</td>
    <td>Francisco Chang</td>
    <td>Mexico</td>
  </tr>
</table>
    
</body>
</html>
