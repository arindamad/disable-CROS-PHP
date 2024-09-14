<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['url_get'])) {
        $response = array('error' => 'Missing URL parameter');
    } else {
        $url = $_GET['url_get']; // The full URL from the query string

        // Fetch data from the specified URL
        $data = fetchData($url);

        if ($data === FALSE) {
            $response = array('error' => 'Error occurred while fetching data from the specified URL');
        } else {
            // Log or output the raw response for debugging
            error_log('Raw response: ' . $data); // Logs the raw response to the server logs
            echo $data; // Return the raw response as-is
            exit;
        }
    }
} else {
    $response = array('error' => 'Invalid request method. Only GET requests are allowed.');
}

// Return the final response (if not already sent)
echo json_encode($response);

function fetchData($url) {
    $ch = curl_init();
    
    // Set the URL and return the transfer as a string
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute the request
    $response = curl_exec($ch);

    // Handle errors
    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
        $response = FALSE;
    }

    curl_close($ch);

    return $response;
}
?>
