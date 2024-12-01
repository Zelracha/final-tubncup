<?php
include 'bkconnection.php'; // Include the database connection

$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    // Retrieve POST data
    $ownerName = $_POST["ownerName"];
    $petName = $_POST["petName"];
    $breed = $_POST["breed"];
    $mobileNum = $_POST["mobileNum"];
    $reserveDate = $_POST["reserveDate"];
    $reserveTime = $_POST["reserveTime"];
    $groomer = $_POST["groomer"];
    $note = $_POST["note"];

    // Prepare the SQL query
    $stmt = $conn->prepare("INSERT INTO petgrooming_data 
        (ownerName, petName, breed, mobileNum, reserveDate, reserveTime, groomer, note) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    $stmt->bind_param("ssssssss", $ownerName, $petName, $breed, $mobileNum, $reserveDate, $reserveTime, $groomer, $note);

    if ($stmt->execute()) {
        $isSuccess = true;
        $_SESSION['isSuccess'] = true; // Set session variable to show success pop-up
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <title>Pet Grooming</title>
    <link rel="icon" href="./icons/paw.png" sizes="16x16" type="image/png">
    <link rel="stylesheet" href="booking.css">
</head>
<body>

    <header>
        <img src="icons/tnclogo.PNG" class="logo" alt="">
        <nav>
            <ul>
                <li><a href="../Landing/landing.html">Home</a></li>
                <li><a href="../Booking/booking.php"><b>Reserve a Schedule</b></a></li>
                <li><a href="../Cafe/cafeordering.html">Cafe Order</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="booking-form">
            <div class="tab">
                <img src="icons/paw.png" alt="Paw Icon">
            </div>
            <h2>BOOKING FORM</h2>
            <hr style="border: 2px solid #E4C267; margin: 20px 0;">

            <!-- Booking Form -->
            <form action="#" method="post">
                <label for="ownerName">NAME OF OWNER:</label>
                <input type="text" id="ownerName" name="ownerName" required>

                <label for="petName">PET NAME:</label>
                <input type="text" id="petName" name="petName" required>

                <label for="breed">BREED:</label>
                <input type="text" id="breed" name="breed" required>

                <label for="mobileNum">MOBILE NUMBER:</label>
                <input type="tel" id="mobileNum" name="mobileNum" required>

                <label for="reserveDate">RESERVE DATE:</label>
                <input type="date" id="reserveDate" name="reserveDate" required onchange="adjustTimeOptions()">

                <label for="reserveTime">RESERVE TIME:</label>
                <select id="reserveTime" name="reserveTime" required>
                    <option value="">Select a time</option>
                    <option value="">9:00AM</option>

                </select>

                <label for="groomer">GROOMER PREFERENCE:</label>
                <select id="groomer" name="groomer">
                    <option value="any">Anyone</option>
                    <option value="Aries">Aries</option>
                    <option value="Dennis">Dennis</option>
                    <option value="Swabe">Swabe</option>
                    <option value="Mark">Mark</option>
                </select>

                <label for="note">NOTE:</label>
                <textarea id="note" name="note" rows="3"></textarea>

                <div class="button-container">
                    <button type="submit" class="submit-btn" name="submit">SUBMIT FORM</button>
                </div>
            </form>
        </div>

        <!-- Hidden Input to Indicate Success -->
        <input type="hidden" id="isSuccess" value="<?php echo $isSuccess ? 'true' : 'false'; ?>" />

        <!-- Popup Success Message -->
        <?php if ($isSuccess): ?>
            <div id="popup-container" class="popup-container active">
                <div class="popup-box">
                    <p>You have successfully booked an appointment for your pet!</p>
                    <p>Please anticipate a call from us for confirmation.</p>
                    <button class="close-btn" onclick="closePopup()">I understand</button>
                </div>
            </div>
            <?php unset($_SESSION['isSuccess']); ?> <!-- Unset session variable after showing the popup -->
        <?php endif; ?>
s
        <footer>
        <h4>Pet Owners and Non-Pet Owners are welcome!</h4>
        <div class="socials">
            <p>Contact Us! <br> 0917 310 3119</p>
            <a href="https://maps.app.goo.gl/JrBTdXpnmwcuUUSd8"><img src="icons/pin.png"></a>
            <a href="https://www.facebook.com/tubncup"><img src="icons/facebook.png"></a>
            <a href="https://www.instagram.com/tubncup/"><img src="icons/instagram.png"></a>
            <a href="https://www.tiktok.com/@tubncup"><img src="icons/tiktok.png"></a>
        </div>

    </footer>
    <div class="foot">
        <p>SERVING SINCE @2021</p>
    </div>
    </div>

    <script src="booking.js"></script>
</body>
</html>