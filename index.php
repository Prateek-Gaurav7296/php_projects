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
            $created_date = date('Y-m-d H:i:s');
            $access_frequency = 0;
            $sql = "INSERT INTO urls (original_url, short_code, created_date, access_frequency) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $url, $short_code, $created_date, $access_frequency);
            $stmt->execute();
            $stmt->close();
        }
        $shortened_url = "/redirect.php?code=$short_code";
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

// Fetch data to display in table
$sql = "SELECT id, short_code, created_date, access_frequency FROM urls";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: aqua;
        }
        td {
            background-color: #D3D3D3;
        }
    </style>
</head>
<body>
    <h1>URL Shortener</h1>

    <form action="index.php" method="post">
        <input type="text" name="url" placeholder="Enter your URL" required>
        <button type="submit" name="submit">Shorten URL</button>
    </form>

    <?php if (isset($shortened_url)): ?>
        <p>Shortened URL: <a href="<?= $shortened_url ?>" id="shortUrl"><?= $shortened_url ?></a>
        <button type="button" onclick="copyToClipboard()">Copy</button></p>
    <?php elseif (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <script>
        function copyToClipboard() {
            var tempInput = document.createElement("input");
            tempInput.value = document.getElementById("shortUrl").textContent;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            alert("Copied to clipboard: " + tempInput.value);
        }
    </script>

    <h2>URL Records</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Short URL</th>
            <th>Created Date</th>
            <th>Access Frequency</th>
            <th>Delete</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><a href="/redirect.php?code=<?= $row['short_code'] ?>"><?= $row['short_code'] ?></a></td>
            <td><?= $row['created_date'] ?></td>
            <td><?= $row['access_frequency'] ?></td>
            <td>
                <form action="index.php" method="post">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>

<?php
$conn->close();
?>