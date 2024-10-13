<?php

// Function to fetch data from Wikipedia API
function getWikiData($query) {
    $query = urlencode($query);  // URL encode the query for safe API requests
    $url = "https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch=$query&format=json";

    // Use cURL to make a request to Wikipedia API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response from Wikipedia API
    $responseData = json_decode($output, true);

    // Check if valid results exist
    if (!empty($responseData['query']['search'])) {
        // Get the first result
        $firstResult = $responseData['query']['search'][0];
        return $firstResult['title'] . ": " . strip_tags($firstResult['snippet']);
    } else {
        return "Sorry, I couldn't find information on that.";
    }
}

// Handle user query from the web form or chatbot interface
if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $response = getWikiData($query);
} else {
    $response = "Please enter a valid query.";
}
?>

<!-- Basic HTML form to interact with the chatbot -->
<!DOCTYPE html>
<html>
<head>
    <title>Wikipedia Chatbot</title>
</head>
<body>
    <h2>Wikipedia Chatbot</h2>
    <form method="POST">
        <label for="query">Ask anything:</label><br>
        <input type="text" name="query" id="query" placeholder="Enter your query here"><br><br>
        <input type="submit" value="Search">
    </form>
    <br>
    <div>
        <strong>Response:</strong> <p><?php echo $response; ?></p>
    </div>
</body>
</html>