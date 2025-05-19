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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $old_password = $_POST['old-password'];
    $new_password = $_POST['new-password'];

    $sql = "UPDATE users SET fname = '$fname', lname = '$lname', email = '$email' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "Profile updated successfully.";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
    

    if (!empty($old_password) && !empty($new_password)) {
        $sql = "SELECT password FROM users WHERE id = $user_id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($old_password, $row['password'])) {
                $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
                $sql = "UPDATE users SET password = '$new_password_hashed' WHERE id = $user_id";
                if ($conn->query($sql) === TRUE) {
                    echo "Password updated successfully.";
                } else {
                    echo "Error updating password: " . $conn->error;
                }
            } else {
                echo "Incorrect old password.";
            }
        }
    }
}

$conn->close();
header('Location: account.php');
exit();
