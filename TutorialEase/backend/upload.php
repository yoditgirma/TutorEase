<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutorease";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $course_id = intval($_POST['course_id']);
    $title = $conn->real_escape_string($_POST['title']);
    
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['file']['tmp_name'];
        $file_content = file_get_contents($file_tmp_path);
        $detected_type = (strpos($file_content, '%PDF-') === 0) ? 'pdf' : 'html';
        
        if ($detected_type == 'pdf') {
            $stmt = $conn->prepare("INSERT INTO course_materials (course_id, title, pdf_data, file_type) VALUES (?, ?, ?, ?)");
            $null = NULL;
            $stmt->bind_param("isbs", $course_id, $title, $null, $detected_type);
            $stmt->send_long_data(2, $file_content);
        } else {
            $html_content = $file_content;
            $stmt = $conn->prepare("INSERT INTO course_materials (course_id, title, html_content, file_type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $course_id, $title, $html_content, $detected_type);
        }
        
        if ($stmt->execute()) {
            $success = "File uploaded successfully!";
        } else {
            $error = "Error uploading file: " . $conn->error;
        }
        
        $stmt->close();
    } else {
        $error = "Error: No file uploaded or upload error occurred.";
    }
}

$courses = $conn->query("SELECT id, name FROM courses ORDER BY name");
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Course Material</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px; box-sizing: border-box; }
        .success { color: green; margin: 10px 0; }
        .error { color: red; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Upload Course Material</h1>
    
    <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="course_id">Course:</label>
            <select name="course_id" id="course_id" required>
                <?php while($course = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required placeholder="E.g., 'Freshman English Grammar Textbook'">
        </div>
        
        <div class="form-group">
            <label for="file">File (PDF or HTML):</label>
            <input type="file" name="file" id="file" required>
        </div>
        
        <div class="form-group">
            <input type="submit" name="submit" value="Upload">
        </div>
    </form>
    
    <p><a href="view_books.php">View All Books</a></p>
</body>
</html>