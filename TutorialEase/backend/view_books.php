<?php

$conn = new mysqli("localhost", "root", "", "tutorease");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$selected_category = $_GET['category'] ?? '';
$selected_course = $_GET['course'] ?? '';
$search_query = $_GET['search'] ?? '';


$sql = "SELECT cm.id, cm.title, cm.file_type, cm.upload_date, 
               c.name AS course_name, cat.name AS category_name
        FROM course_materials cm
        JOIN courses c ON cm.course_id = c.id
        JOIN categories cat ON c.category_id = cat.id
        WHERE 1=1";

$params = [];
$types = '';

if (!empty($selected_category)) {
    $sql .= " AND cat.name = ?";
    $params[] = $selected_category;
    $types .= 's';
}

if (!empty($selected_course)) {
    $sql .= " AND c.name = ?";
    $params[] = $selected_course;
    $types .= 's';
}

if (!empty($search_query)) {
    $sql .= " AND (cm.title LIKE ? OR c.name LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $types .= 'ss';
}

$sql .= " ORDER BY cat.name, c.name, cm.upload_date DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$categories = $conn->query("SELECT name FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Materials</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="../frontend/css/view_books.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <h1>Course Materials Library</h1>
        <p>Access all available learning resources</p>
    </div>
    
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <!-- Filter Section -->
        <form method="get" action="view_books.php" class="filter-container">
            <div class="filter-group">
                <label for="category">Category</label>
                <select id="category" name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['name'] ?>" <?= $cat['name'] == $selected_category ? 'selected' : '' ?>>
                            <?= $cat['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="course">Course</label>
                <select id="course" name="course" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    <?php
                    $courses = $conn->query("SELECT DISTINCT c.name 
                                            FROM courses c
                                            JOIN categories cat ON c.category_id = cat.id
                                            " . (!empty($selected_category) ? "WHERE cat.name = '$selected_category'" : "") . "
                                            ORDER BY c.name");
                    while($course = $courses->fetch_assoc()): ?>
                        <option value="<?= $course['name'] ?>" <?= $course['name'] == $selected_course ? 'selected' : '' ?>>
                            <?= $course['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="search-box">
                <label for="search">Search</label>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" id="search" name="search" placeholder="Search materials..." value="<?= htmlspecialchars($search_query) ?>">
                    <button type="submit">Search</button>
                </div>
            </div>
        </form>
        
        <!-- Materials Display -->
        <?php
        $materials_by_category = [];
        while ($row = $result->fetch_assoc()) {
            $materials_by_category[$row['category_name']][] = $row;
        }
        
        if (empty($selected_category)) {
            $all_categories = $conn->query("SELECT name FROM categories");
            while ($cat = $all_categories->fetch_assoc()) {
                if (!isset($materials_by_category[$cat['name']])) {
                    $materials_by_category[$cat['name']] = [];
                }
            }
        }
        
        foreach ($materials_by_category as $category => $materials): 
            if (empty($materials) && (!empty($selected_category) || !empty($selected_course) || !empty($search_query))) continue;
        ?>
            <div class="category-section">
                <h2 class="category-header <?= strtolower($category) == 'freshman' ? 'freshman-header' : 'preeng-header' ?>">
                    <?= $category ?> Materials
                </h2>
                
                <div class="materials-grid">
                    <?php if (!empty($materials)): ?>
                        <?php foreach ($materials as $material): ?>
                            <div class="material-card">
                                <div class="card-header">
                                    <h3><?= htmlspecialchars($material['title']) ?></h3>
                                </div>
                                <div class="card-body">
                                    <div class="course-name"><?= $material['course_name'] ?></div>
                                    <div class="card-meta">
                                        <span class="file-type"><?= strtoupper($material['file_type']) ?></span>
                                        <span><?= date('M d, Y', strtotime($material['upload_date'])) ?></span>
                                    </div>
                                    <a href="fetch_file.php?id=<?= $material['id'] ?>" class="btn" target="_blank">
                                        View <?= $material['file_type'] == 'pdf' ? 'PDF' : 'Content' ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>No materials found <?= !empty($search_query) ? 'matching your search' : 'for this category' ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.getElementById('category').addEventListener('change', function() {
            const category = this.value;
            const courseSelect = document.getElementById('course');
            
            if (category) {
                fetch(`get_courses.php?category=${encodeURIComponent(category)}`)
                    .then(response => response.json())
                    .then(courses => {
                        courseSelect.innerHTML = '<option value="">All Courses</option>';
                        courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.name;
                            option.textContent = course.name;
                            courseSelect.appendChild(option);
                        });
                    });
            } else {
                fetch(`get_courses.php`)
                    .then(response => response.json())
                    .then(courses => {
                        courseSelect.innerHTML = '<option value="">All Courses</option>';
                        courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.name;
                            option.textContent = course.name;
                            courseSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
</body>
</html>