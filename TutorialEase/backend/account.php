<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../frontend/html/login.html');
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
$sql = "SELECT fname, lname, email, profile_picture FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fname = $row['fname'];
    $lname = $row['lname'];
    $email = $row['email'];
    $profile_picture = $row['profile_picture'];
} else {
    echo "No user found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account and Courses</title>
    <link rel="stylesheet" href="../frontend/css/account.css" />
    <link rel="stylesheet" href="../frontend/css/course-home.css" />
    <link rel="stylesheet" href="../frontend/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="../frontend/js/index.js" defer></script>
</head>

<body>
    <nav>
        <div class="logo">
            <h2>TUTOR EASE</h2>
        </div>
        <div class="menu">
            <a href="" class="" id="pp">profile </a>
        </div>
    </nav>

    <div class="section-1">
        <div class="side">
            <a href="account.php">Profile</a>
            <a href="../frontend/html/dashboard.html">Continue Course</a>
            <a href="#foot">Contact</a>
            <a href="logout.php">Log Out</a>
        </div>
        <div class="main">
            <div class="edit">
                <div class="picture">
                    <div class="pic">
                        <?php if ($profile_picture): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($profile_picture); ?>"
                                alt="Profile Picture" />
                        <?php else: ?>
                            <img src="../frontend/assets/logos/profile.jpg" alt="Default Profile Picture" />
                        <?php endif; ?>
                    </div>
                    <div class="update">
                        <form action="update_pic.php" method="post" enctype="multipart/form-data" id="updateForm">
                            <label for="profile_picture">Select File</label>
                            <input type="file" id="profile_picture" name="profile_picture" required>
                            <div class="buttons" style="display: flex; gap: 10px;">
                                <input type="submit" value="Update">
                                <button type="button" id="deleteBtn" style=
                                "background-color: rgba(68, 68, 88, 0.377); 
                                width: 100px; 
                                height: 30px; 
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                color: #000000; 
                                font-size: 16px; 
                                border: none; 
                                border-radius: 5px; 
                                cursor: pointer; 
                                transition: background-color 0.3s ease, transform 0.3s ease; 
                                margin: 0 2px; ">
                                Delete</button>
                            </div>
                        </form>
                    </div>

                    <form action="delete_pic.php" method="post" id="deleteForm" style="display: none;">
                        <input type="hidden" name="delete" value="true">
                    </form>

                    <script>
                        document.getElementById('deleteBtn').addEventListener('click', function() {
                            if (confirm('Are you sure you want to delete your profile picture?')) {
                                document.getElementById('deleteForm').submit();
                            }
                        });
                    </script>
                </div>
                <div class="form-container">
                    <div class="account middle">
                        <form action="update_profile.php" method="post">
                            <label for="fname">First Name</label><br />
                            <input type="text" name="fname" value="<?php echo htmlspecialchars($fname); ?>"
                                placeholder="First Name" /><br />
                            <label for="lname">Last Name</label><br />
                            <input type="text" name="lname" value="<?php echo htmlspecialchars($lname); ?>"
                                placeholder="Last Name" /><br />
                            <label for="email">E-mail</label><br />
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                                placeholder="E-mail" required /><br />

                            <div class="pass">
                                <label for="old-password">Old Password</label><br />
                                <input type="password" id="old-password" name="old-password"
                                    placeholder="Old Password" /><br />
                                <label for="new-password">New Password</label><br />
                                <input type="password" id="new-password" name="new-password"
                                    placeholder="New Password" /><br />
                            </div>
                            <input type="submit" value="Update" />
                            <a href="dashboard.html">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
            <div class="information">
                <div class="info">
                    <p>Name</p>
                    <p>Last Name</p>
                    <p>Email</p>
                </div>
                <div class="data">
                    <p><?php echo htmlspecialchars($fname); ?></p>
                    <p><?php echo htmlspecialchars($lname); ?></p>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>
        </div>
    </div>

    <section class="foot">
        <footer class="footer">
            <div class="main-foot1">
                <div class="foot-part head">
                    <h2>TUTOR EASE</h2>
                </div>
                <div class="foot-part head">
                    <a href="#home">JOIN US</a>
                </div>
            </div>
            <div class="main-foot2">
                <div class="foot-part">
                    <h2>Pages</h2>
                </div>
                <div class="foot-part">
                </div>
                <div class="foot-part">
                    <h2>Contact us</h2>
                </div>
                <div class="foot-part">
                    <h2>Connect with us</h2>
                </div>
            </div>
            <div class="main-foot3">
                <div class="foot-part links">
                    <a href="#about">About Us</a>
                    <a href="#services">Services</a>
                    <a href="#contact">Contact</a>
                    <a href="#privacy">Privacy Policy</a>
                    <a href="#terms">Terms of Service</a>
                </div>
                <div class="foot-part links">
                    <a href="#blog">Blog</a>
                    <a href="#portfolio">Portfolio</a>
                    <a href="#team">Our Team</a>
                    <a href="#careers">Careers</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="foot-part links">
                    <a href="#testimonials">Testimonials</a>
                    <a href="#resources">Resources</a>
                    <a href="#events">Events</a>
                    <a href="#support">Support</a>
                    <a href="#community">Community</a>
                </div>
                <div class="foot-part links icons">
                    <a href="#newsletter" title="Newsletter Signup">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#sitemap" title="Sitemap">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#advertising" title="Advertising">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#partners" title="Partners">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#feedback" title="Feedback">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            <div class="main-foot4">
                <p>&copy; 2025 Tutor-Ease. All rights reserved.</p>
                <p>
                    <a href="#privacy">Privacy Policy</a> |
                    <a href="#terms">Terms of Service</a> |
                    <a href="#disclaimer">Disclaimer</a>
                </p>
            </div>
        </footer>
    </section>
</body>

</html>