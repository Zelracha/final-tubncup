<?php
include 'bkconnection.php'; // Ensure the correct database connection is established

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the booking ID from the POST data
    $id = $_POST['id'];

    if (empty($id)) {
        echo "ID is empty!";
        exit();
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Select booking from petgrooming_data table
        $query = "SELECT * FROM petgrooming_data WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Insert data into completed_booking
            $insert_query = "INSERT INTO completed_booking (cReserveDate, cReserveTime, cOwnerName, cPetName, cBreed, cMobileNum, cGroomer, cNote)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ssssssss", $row['reserveDate'], $row['reserveTime'], $row['ownerName'],
                                     $row['petName'], $row['breed'], $row['mobileNum'], $row['groomer'], $row['note']);
            $insert_stmt->execute();

            // Delete the record from petgrooming_data after inserting into completed_booking
            $delete_query = "DELETE FROM petgrooming_data WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("i", $id);
            $delete_stmt->execute();

            // Commit the transaction
            $conn->commit();

            // Redirect back to the same page to reload the updated bookings
            header("Location: customerbooking.php");
            exit();
        } else {
            echo "Booking not found!";
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN BOOKING PAGE</title>
    <link rel="stylesheet" href="AdminBooking.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet"></head>
<body>
<header>
    <img src="icons/tnclogo.PNG" class="logo" alt="">
    <nav>
        <ul>
        <li><a href="../Admin-Booking/customerbooking.php"><b>BOOKINGS</b></a></li>
        <li><a href="../Admin-Cafe/customerorders.php">ORDERS</a></li>
        </ul>
    </nav>
</header>

<main><br><br>
 <h2>BOOKINGS</h2>
 <table class="table">
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Reserved Date</th>
            <th>Reserved Time</th>
            <th>Owner Name</th>
            <th>Pet Name</th>
            <th>Breed</th>
            <th>Phone Number</th>
            <th>Groomer</th>
            <th>Note</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch bookings from petgrooming_data table
        $query = "SELECT id, ownerName, petName, breed, mobileNum, reserveDate, reserveTime, groomer, note
                  FROM petgrooming_data
                  ORDER BY reserveDate ASC";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr id='row_" . htmlspecialchars($row['id']) . "'>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['reserveDate']) . "</td>
                        <td>" . htmlspecialchars($row['reserveTime']) . "</td>
                        <td>" . htmlspecialchars($row['ownerName']) . "</td>
                        <td>" . htmlspecialchars($row['petName']) . "</td>
                        <td>" . htmlspecialchars($row['breed']) . "</td>
                        <td>" . htmlspecialchars($row['mobileNum']) . "</td>  
                        <td>" . htmlspecialchars($row['groomer']) . "</td>
                        <td>" . htmlspecialchars($row['note']) . "</td>
                        <td>
                            <form action='customerbooking.php' method='POST' onsubmit='hideRow(" . htmlspecialchars($row['id']) . ")'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                <button type='submit' class='completed-btn'>Completed</button>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No bookings found</td></tr>";
        }

        $conn->close();
        ?>
    </tbody>
 </table>
</main>
<script>
    function hideRow(id) {
        // Wait for the form submission to be completed (mark as completed)
        document.getElementById('row_' + id).style.display = 'none';
    }
</script>

</body>
</html>
