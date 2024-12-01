<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe-form";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Get the POST data
$orderId = $_POST['orderId'] ?? '';
$action = $_POST['action'] ?? '';

if (!$orderId || !$action) {
    die(json_encode(['success' => false, 'message' => 'Missing required parameters']));
}

// Start transaction
$conn->begin_transaction();

try {
    // Update the order status in customer_form
    $sql = "UPDATE customer_form SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($action === 'complete') {
        $status = 'completed';
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        
        // Insert into completed_orders
        $sql2 = "INSERT INTO completed_orders (order_id, completed_by) VALUES (?, 'admin')";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $orderId);
        $stmt2->execute();
    } 
    else if ($action === 'cancel') {
        $status = 'cancelled';
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        
        // Insert into cancelled_orders
        $sql2 = "INSERT INTO cancelled_orders (order_id, cancelled_by) VALUES (?, 'admin')";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $orderId);
        $stmt2->execute();
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true, 'message' => "Order successfully $status"]);
} 
catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error updating order: ' . $e->getMessage()]);
}

$conn->close();
?>