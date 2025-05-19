<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


session_start();


if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}


$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['question'])) {
    $question = trim($data['question']);
    if (!empty($question)) {
        $_SESSION['chat_history'][] = ['role' => 'user', 'content' => $question];

        
        $messages = [['role' => 'system', 'content' => "You are a helpful tutor assisting students."]];
        foreach ($_SESSION['chat_history'] as $m) {
            $messages[] = $m;
        }

       $api_key = $_ENV['API_KEY'];
        $responseText = callOpenRouter($api_key, $messages);

        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $responseText];

        echo json_encode(['reply' => $responseText, 'history' => $_SESSION['chat_history']]);
        exit;
    }
}

echo json_encode(['error' => 'Invalid request']);
exit;


function callOpenRouter($api_key, $messages) {
    $url = 'https://openrouter.ai/api/v1/chat/completions';
    $data = [
        'model' => 'openai/gpt-3.5-turbo',
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 1000
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n" .
                        "Authorization: Bearer $api_key\r\n" .
                        "HTTP-Referer: https://localhost" .
                        "X-Title: Student Q&A System",
            'method' => 'POST',
            'content' => json_encode($data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        $error = error_get_last();
        return "Sorry, I encountered an error: " . ($error['message'] ?? 'Unknown error');
    }
    $response = json_decode($result, true);
    return $response['choices'][0]['message']['content'];
}
?>

