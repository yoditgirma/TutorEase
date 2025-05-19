<?php

header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "tutor");

$category = $_GET['category'] ?? '';
$where = $category ? "WHERE cat.name = '" . $conn->real_escape_string($category) . "'" : "";

$result = $conn->query("SELECT c.name FROM courses c JOIN categories cat ON c.category_id = cat.id $where ORDER BY c.name");
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);
?>
