<?php

require_once __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); 
$dotenv->load();

header('Content-Type: application/json');

$apiKey = $_ENV['YOUTUBE_API_KEY']; 
$query = isset($_GET['q']) ? urlencode($_GET['q']) : '';
$maxResults = 20;

$url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&q={$query}&maxResults={$maxResults}&key={$apiKey}";

$response = file_get_contents($url);
if ($response === FALSE) {
    echo json_encode(['error' => 'Failed to fetch data']);
    exit;
}

$data = json_decode($response, true);

$videos = [];
foreach ($data['items'] as $item) {
    $videos[] = [
        'title' => $item['snippet']['title'],
        'videoId' => $item['id']['videoId'],
        'thumbnail' => $item['snippet']['thumbnails']['medium']['url']
    ];
}

echo json_encode(['videos' => $videos]);
?>
