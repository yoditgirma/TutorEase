<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutorease";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    
    $stmt = $conn->prepare("SELECT cm.pdf_data, cm.html_content, cm.file_type, cm.title FROM course_materials cm WHERE cm.id = ?");
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($pdf_data, $html_content, $file_type, $title);
        $stmt->fetch();

        if ($file_type == 'pdf') {
            if (empty($pdf_data)) {
                die("Error: PDF data is empty.");
            }
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . urlencode($title) . '.pdf"');
            header('Content-Length: ' . strlen($pdf_data));

            echo $pdf_data;
        } else {
            if (empty($html_content)) {
                die("Error: HTML content is empty.");
            }
            header('Content-Type: text/html');
            echo $html_content;
        }
    } else {
        die("Error: Document not found.");
    }

    $stmt->close();
    $conn->close();
} else {
    die("Error: No document ID provided.");
}
?>