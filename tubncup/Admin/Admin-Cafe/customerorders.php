<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe-form";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only select pending orders
$sql = "SELECT * FROM customer_form WHERE status = 'pending' ORDER BY order_date DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Invalid query: " . $conn->error);
}
?>  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="customerorders.css">
</head>
<body>
<header>
    <img src="icons/tnclogo.PNG" class="logo" alt="">
    <nav>
        <ul>
        <li><a href="../Admin-Booking/customerbooking.php">BOOKINGS</a></li>
        <li><a href="../Admin-Cafe/customerorders.php"><b>ORDERS</b></a></li>
        </ul>
    </nav>
</header>


<h1 style="text-align:center; font-size: 25px; margin-top:40px; letter-spacing: 6px;">ORDERS</h1>
    <br>
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Full Name</th>
                <th>Phone Number</th>
                <th>Pickup Time</th>
                <th>Mode of Payment</th>
                <th>Cart Items</th>
                <th>Order Date</th>
                <th>Total Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='order-".$row["id"]."'>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["full_name"] . "</td>
                        <td>" . $row["phone_number"] . "</td>
                        <td>" . $row["pickup_time"] . "</td>
                        <td>" . $row["payment_method"] . "</td>
                        <td>" . $row["cart_items"] . "</td>
                        <td>" . $row["order_date"] . "</td>
                        <td>₱" . number_format($row["total_price"], 2) . "</td>
                        <td>
                            <button class='action-btn complete-btn' onclick='updateOrderStatus(".$row["id"].", \"complete\")'><b>Complete Order</b></button>
                            <button class='action-btn cancel-btn' onclick='updateOrderStatus(".$row["id"].", \"cancel\")'><b>Cancel Order</b></button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='9' style='text-align: center;'>No pending orders</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
    function updateOrderStatus(orderId, action) {
        const row = document.getElementById('order-' + orderId);
        
        // Create form data
        const formData = new FormData();
        formData.append('orderId', orderId);
        formData.append('action', action);

        // Send AJAX request
        fetch('update_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (action === "complete") {
                    row.style.backgroundColor = "#d4edda";
                    alert('Order ' + orderId + ' marked as complete.');
                } else if (action === "cancel") {
                    row.style.backgroundColor = "#f8d7da";
                    alert('Order ' + orderId + ' cancelled.');
                }
                
                // Remove the row from the table after the action is done
                setTimeout(() => {
                    row.style.display = 'none';
                }, 1000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the order.');
        });
    }
    </script>

</body>
</html>
