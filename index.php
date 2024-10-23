<?php
// Periksa apakah session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database 'unite'
$host = 'localhost';
$dbname = 'unite'; // nama database 'unite'
$username = 'root'; // username MySQL
$password = ''; // password MySQL (kosong di XAMPP)

try {
    // Koneksi ke database 'unite'
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Logout handler
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Cek apakah user sudah login
$isLoggedIn = isset($_SESSION['user_id']);
$profileImage = 'default.png'; // Gambar default

if ($isLoggedIn) {
    // Ambil data user dari database
    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user && !empty($user['profile_image'])) {
        $profileImage = $user['profile_image'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="main.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unite</title>
    <style>
        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .profile-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 10px;
        }
        .profile-dropdown.visible {
            display: block;
        }
    </style>
</head>
<body>
<header class="header" style="border: 5px black; border-bottom: 0.1px solid black;">
    <h1>Unite</h1>
    <nav class="navigation_">
        <a href="" style="color: black;">Home</a>
        <a href="" style="color: black;">Events</a>
        <a href="" style="color: black;">About Us</a>
    </nav>
    <div class="navigation_-buttons">
        <?php if ($isLoggedIn): ?>
            <div id="profile" class="profile" style="position: relative; cursor: pointer;">
                <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Icon" class="profile-icon">
                <span><?php echo htmlspecialchars($user['name']); ?></span>
                <div id="profileDropdown" class="profile-dropdown">
                    <p>Member Silver</p>
                    <li><a href="edit_profile.php">Edit Profile</a></li>
                    <li><a href="/event-register">Event Register</a></li>
                    <form method="POST">
                        <button name="logout" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" class="button">Login</a>
            <a href="register.php" class="button2">Register</a>
        <?php endif; ?>
    </div>
</header>

<div class="second" style="height:90vh;">
    <div class="text-content">
        <h1>Music Events <span style="color: #cc308a; font-family: Sans-Bold;">Unite</span> in one place</h1>
        <p style="padding-bottom: 10px;">Your ultimate destination for the best music events.</p>
        <a href="" class="button" style="margin-left:5px;">Browse Events</a>
    </div>
    <img src="headset.png" class="right-image">
</div>

<div class="container">
    <div class="scroll">
        <div class="RightToLeft">
            <h1>Unite! Unite! Unite! Unite! Unite! Unite! Unite! Unite! Unite! Unite! Unite! </h1>
        </div>
    </div>
</div>

<section class="hero">
    <div class="left-cards">
        <div class="card1">            
            <img src="1.jpeg" alt="Business Mindset Planning">
        </div>
        <div class="card2">
            <img src="2.jpeg" alt="Key Success of Career">
            
        </div>
        <div class="card3">
            <img src="3.jpeg" alt="Management">
        </div>
 
    </div>
    <div class="hero-text">
        <h1 style="font-family: Sans-Bold">Grab Ur Concert Tickets On <span style="color: #cc308a; font-family: Sans-Bold">Unite!</span></h1>
        <p>Welcome to Unite! Your ultimate destination for unforgettable music experiences. At Unite, we believe in bringing people together through the power of music. Our platform offers a seamless way to discover and purchase tickets for the hottest concerts and events in your area. With a diverse lineup of artists and genres, there’s something for everyone. Whether you’re a die-hard music fan or just looking for a fun night out, Unite has you covered. </p>
        <a href="" class="button2" style="margin-left:18px;">Learn More!</a>
    </div>
</section>


<div class="container">
    <div class="scroll">
        <div class="LeftToRight">
            <h1>Unite! Unite! Unite! Unite! Unite! Unite! Unite! Unite! Unite! Unite! Unite! </h1>
        </div>
    </div>
</div>

<div class="events">
    <div class="event-text">
        <h1>Recent <br>Events</h1>
    </div>
    <div class="recent_image">
    <div class="event_item">
        <img src="nadin.jpeg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Nadine Amizah Concert</h1>
    </div>
    
    <div class="event_item">
        <img src="juicyluicy.png" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Juicy Lucy Concert</h1>
    </div>

    <div class="event_item">
        <img src="bernadya.jpeg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Bernadya Concert</h1>
    </div>
    <div class="event_item2">
        <img src="nadin.jpeg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Rizky Febian & Mahalini Concert</h1>
    </div>
    <div class="event_item2">
        <img src="nadin.jpeg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Tulus Concert</h1>
    </div>
    <div class="event_item2">
        <img src="nadin.jpeg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Hivi! Concert</h1>
    </div>>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profile = document.getElementById('profile');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profile) {
            profile.addEventListener('click', function() {
                profileDropdown.classList.toggle('visible');
            });
        }
    });
</script>

</body>
</html>
