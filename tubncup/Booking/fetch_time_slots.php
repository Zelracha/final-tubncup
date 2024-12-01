<?php
include 'bkconnection.php';

if (isset($_GET['reserveDate'])) {
    $reserveDate = $_GET['reserveDate'];

    $query = "SELECT reserveTime, COUNT(*) as bookingCount 
              FROM petgrooming_data 
              WHERE reserveDate = ? 
              GROUP BY reserveTime";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $reserveDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookedSlots = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['bookingCount'] >= 2) {
            $bookedSlots[] = $row['reserveTime'];
        }
    }

    echo json_encode(['bookedSlots' => $bookedSlots]);
    exit;
}
?>