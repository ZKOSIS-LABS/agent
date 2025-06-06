<?php
// ==============================================================================================
// DO THIS ONLY IF YOU ARE GOING TO RUN THE APPLICATION ON A PHP SERVER.  (see readme.md for more info)
// ==============================================================================================
// Rename this script to "apicall.php"
// Replace "YOUR_OPENAI_API_KEY_HERE" with your actual OpenAI API key.
// IMPORTANT: DO NOT upload the "apikeys.js" file to your server. (see readme.md for more info)
// It is only meant for running the HTML client locally.
// This PHP script is executed only when the HTML client can not find "apikeys.js"'s (openai_apikey).
// This is intended functionality to choose between local and remote API key.

$APIKEY = "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";

//ini_set('post_max_size', '64M');//use if you get 413 Request Entity Too Large error
//ini_set('memory_limit', '128M');//use if you get 500 Internal Server Error

// use this if your server is impatient and times out before the API call is finished
ini_set('max_execution_time', '300'); // 300 seconds = 5 minutes


$data = file_get_contents('php://input');
$url = 'https://api.openai.com/v1/chat/completions';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-type: application/json",
    "Authorization: Bearer " . $APIKEY
));

$result = curl_exec($ch);

if ($result === FALSE) {
    // Handle error
    $error = curl_error($ch);
    $response = array(
        'status' => 'error',
        'message' => $error,
        'inputData' => $data
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
} else {
    $response = json_decode($result, true);

    if (isset($response['error'])) {
        $error = $response['error'];
        $message = $error['message'] ?? '(php) - An error occurred.';
        $code = $error['code'] ?? '';
        $details = $error['details'] ?? '';

        $errorMessage = "Error: $message\nCode: $code\nDetails: $details";
        $response = array(
            'status' => 'error',
            'message' => $errorMessage,
            'inputData' => $data
        );
    }
}

curl_close($ch);

header('Content-Type: application/json');
echo json_encode($response);
