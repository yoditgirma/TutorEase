<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutorease";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

if ($_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    $image = file_get_contents($_FILES['profile_picture']['tmp_name']);
    $image = $conn->real_escape_string($image);

    $sql = "UPDATE users SET profile_picture = '$image' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        header('Location: account.php');
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    echo "No file uploaded or upload error.";
}

$conn->close();
?>