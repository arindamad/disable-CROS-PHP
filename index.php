<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = file_get_contents("php://input");

    if ($postData === FALSE) {
        $response = array('error' => 'Error occurred while fetching POST data');
    } else {
        $jsonData = json_decode($postData, TRUE);

        if ($jsonData === NULL || !isset($jsonData['url'])) {
            $response = array('error' => 'Invalid or missing JSON POST data');
        } else {
            $url = $jsonData['url'];

            // Remove the 'url' from the $jsonData, leaving only dynamic data
            unset($jsonData['url']);

            if (empty($jsonData)) {
                $response = array('error' => 'No dynamic fields provided');
            } else {
                $data = fetchData($url, $jsonData);

                if ($data === FALSE) {
                    $response = array('error' => 'Error occurred while fetching data from the specified URL');
                } else {
                    // Ensure that the response is formatted properly as JSON
                    $decodedData = json_decode($data, TRUE);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $response = $decodedData;
                    } else {
                        $response = array('error' => 'Invalid JSON response from the server');
                    }
                }
            }
        }
    }
} else {
    $response = array('error' => 'Invalid request method. Only POST requests are allowed.');
}

echo json_encode($response);

function fetchData($url, $postData) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
    $json_data = json_encode($postData);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
        $response = FALSE;
    }

    curl_close($ch);

    return $response;
}
?>
