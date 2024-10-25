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
    <script src="https://unpkg.com/feather-icons"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unite</title>
    <style>
        
    </style>
</head>
<body>
<header class="header" style="border: 5px black; border-bottom: 0.1px solid black;">
    <h1>Unite</h1>
    <nav class="navigation_">
        <a href="index.php" style="color: black;">Home</a>
        <a href="events.php" style="color: black;">Events</a>
    </nav>
    <div class="navigation_-buttons">
        <?php if ($isLoggedIn): ?>
            <div id="profile" class="profile" style="position: relative; cursor: pointer;">
            <i data-feather="user"></i>
                <span style="font-family: SemiBold; font-size:25px;">Hi, <?php echo htmlspecialchars($user['name']); ?></span>
                <div id="profileDropdown" class="profile-dropdown">
                    <?php if ($user['role'] === 'admin'): ?>
                       <a href="admindashboard.php" style="text-decoration:none; font-family:SemiBold; color:black; padding-bottom:10px; ">Admin Dashboard</a>
                    <?php endif; ?>
                    <form method="POST">
                        <a href="edit_profile.php" style="text-decoration:none; font-family:SemiBold; color:black; padding-bottom:10px; ">Edit Profile</a>
                        <a href="profile.php" style="text-decoration:none; font-family:SemiBold; color:black; padding-bottom:10px; ">Event Registrations</a>
                        <button name="logout" class="button" style="margin-top:10px;">Logout</button>
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
        <a href="events.php" class="button" style="margin-left:5px;">Browse Events</a>
    </div>
    <img src="photos/headset.png" class="right-image">
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
            <img src="photos/1.jpeg" alt="Business Mindset Planning">
        </div>
        <div class="card2">
            <img src="photos/2.jpeg" alt="Key Success of Career">
            
        </div>
        <div class="card3">
            <img src="photos/3.jpeg" alt="Management">
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
        <img src="photos/nadin.jpeg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Nadine Amizah Concert</h1>
    </div>
    
    <div class="event_item">
        <img src="photos/juicyluicy.png" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Juicy Lucy Concert</h1>
    </div>

    <div class="event_item">
        <img src="photos/bernadya.jpeg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Bernadya Concert</h1>
    </div>
    <div class="event_item2">
        <img src="photos/rizky.jpeg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Rizky Febian & Mahalini Concert</h1>
    </div>
    <div class="event_item2">
        <img src="photos/ulus.jpeg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Tulus Concert</h1>
    </div>
    <div class="event_item2">
        <img src="photos/HIVi!.jpg" alt="nadine">
        <h2 class="event-description">28 Oktober 2024</h2>
        <h1 class="event_name">Hivi! Concert</h1>
    </div>
</div>



<script>
    // Re-initialize feather icons for footer
    feather.replace();
</script>


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
<script>
      feather.replace();
</script>

</body>
</html>
