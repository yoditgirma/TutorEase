<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: account.php');
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

if (isset($_POST['delete']) && $_POST['delete'] === 'true') {
    $sql = "UPDATE users SET profile_picture = NULL WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        header('Location: account.php');
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();

?>