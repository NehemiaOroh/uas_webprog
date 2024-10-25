<?php
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

// Check if the session is started and handle login
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['logout'])) {
    session_start();
    session_destroy();
    header("Location: index.php"); // Redirect after logout
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

// Fetch events from the database
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="main.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <style>
        .get-ticket {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #cc308a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        /* Dropdown Styles */
        .profile-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 10px;
            z-index: 1000; /* Ensures dropdown appears above other elements */
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
                        <a href="" style="text-decoration:none; font-family:SemiBold; color:black; padding-bottom:10px; ">Edit Profile</a>
                        <a href="" style="text-decoration:none; font-family:SemiBold; color:black; padding-bottom:10px; ">Event Registrations</a>
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

<div class="video-wrapper">
    <video muted autoplay loop src="photos/coldplay.mp4"></video>
    <div class="text-overlay">
        <h1>Events</h1>
        <p>Grab your tickets here!</p>
    </div>
</div>

<div class="available">
    <h1>Available <br>Events!</h1>

</div>

<div class="event-container">
    <?php if (empty($events)): ?>
        <p>No events available at the moment.</p>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
    <div class="event" data-event-id="<?php echo $event['event_id']; ?>">
        <img src="uploads/<?php echo htmlspecialchars($event['banner_image']); ?>" alt="<?php echo htmlspecialchars($event['event_name']); ?>">
        <h2 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h2>
        <p class="event-description" style="display:none;"><?php echo htmlspecialchars($event['event_description']); ?></p>
        <p class="event-time" style="display:none;"><?php echo htmlspecialchars($event['event_time']); ?></p>
        <div class="event-details">
            <p class="event-date" style="font-family: SemiBold;"><?php echo htmlspecialchars($event['event_date']); ?></p>
            <p class="event-participants">
                <span class="participant-info" style="font-family: SemiBold;">
                    <i data-feather="user"></i>
                    <?php echo htmlspecialchars($event['current_participants']); ?> / <?php echo htmlspecialchars($event['max_participants']); ?>
                </span>
            </p>
        </div>
        <div class="location">
            <span class="location-info">
                <i data-feather="map-pin"></i>
                <p style="font-family: SemiBold;"><?php echo htmlspecialchars($event['location']); ?></p >
            </span>
        </div>
        <button class="get-ticket">Get Ticket</button>
    </div>
<?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalEventName"></h2>
        <p id="modalEventDate"></p>
        <p id="modalEventTime"></p>
        <p id="modalEventLocation"></p>
        <p id="modalEventParticipants"></p>
        <p id="modalEventDescription"></p>
        <input type="hidden" id="modalEventId" value=""> <!-- Hidden input for event ID -->
        <button class="button2" style="margin-top:20px">Buy Ticket</button>

    </div>
</div>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profile = document.getElementById('profile');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profile) {
            profile.addEventListener('click', function() {
                profileDropdown.classList.toggle('visible');
            });

            // Close dropdown if clicked outside
            window.addEventListener('click', function(event) {
                if (!profile.contains(event.target)) {
                    profileDropdown.classList.remove('visible');
                }
            });
        }
    });
</script>
<script>
      feather.replace();
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('eventModal');
    const closeModal = document.getElementsByClassName('close')[0];

    // Event listeners for Get Ticket buttons
    document.querySelectorAll('.get-ticket').forEach(button => {
        button.addEventListener('click', function() {
            const eventElement = this.closest('.event');
            const eventId = eventElement.dataset.eventId; // Get event ID from data attribute
            const eventName = eventElement.querySelector('.event-title').innerText;
            const eventDate = eventElement.querySelector('.event-date').innerText;
            const eventTime = eventElement.querySelector('.event-time').innerText; 
            const eventLocation = eventElement.querySelector('.location-info p').innerText;
            const maxParticipants = eventElement.querySelector('.event-participants .participant-info').innerText;
            const eventDescription = eventElement.querySelector('.event-description').innerText; 

            document.getElementById('modalEventName').innerText = eventName;
            document.getElementById('modalEventDate').innerText = `Date: ${eventDate}`;
            document.getElementById('modalEventTime').innerText = `Time: ${eventTime}`; 
            document.getElementById('modalEventLocation').innerText = `Location: ${eventLocation}`;
            document.getElementById('modalEventParticipants').innerText = `Max Participants: ${maxParticipants}`;
            document.getElementById('modalEventDescription').innerText = `Description: ${eventDescription}`; 

            // Set the event ID in the hidden input
            document.getElementById('modalEventId').value = eventId;

            modal.style.display = "block"; // Show the modal
        });
    });

    // Add event listener for Buy Ticket button
    document.querySelector('.button2').addEventListener('click', function() {
        const eventId = document.getElementById('modalEventId').value;
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'buy_ticket.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                alert(response.message); // Show the response message
                if (response.status === 'success') {
                    modal.style.display = "none"; // Close the modal on success
                }
            } else {
                alert('Failed to purchase ticket. Please try again.');
            }
        };
        xhr.send(`event_id=${eventId}`);
    });


    // Close the modal
    closeModal.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal if clicked outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
</script>

<script> 
    // Add event listener for Cancel Registrations button inside the modal open function
button.addEventListener('click', function() {
    const eventId = document.getElementById('modalEventId').value;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'op.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            alert(response.message); // Show the response message
            if (response.status === 'success') {
                modal.style.display = "none"; // Close the modal on success
                location.reload(); // Reload the page to update registrations
            }
        } else {
            alert('Failed to cancel registration. Please try again.');
        }
    };
    xhr.send(`event_id=${eventId}`);
});

// Ensure this code is only run after the modal has been displayed
const cancelButton = document.querySelector('.button1'); // Select the correct button for cancel
if (cancelButton) {
    cancelButton.addEventListener('click', function() {
        const eventId = document.getElementById('modalEventId').value;
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'cancel_registrations.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                alert(response.message); // Show the response message
                if (response.status === 'success') {
                    modal.style.display = "none"; // Close the modal on success
                    location.reload(); // Reload the page to update registrations
                }
            } else {
                alert('Failed to cancel registration. Please try again.');
            }
        };
        xhr.send(`event_id=${eventId}`);
    });
}



</script>

</body>
</html>
