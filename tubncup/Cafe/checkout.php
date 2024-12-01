<?php  
  // Enable error reporting for debugging  
  ini_set('display_errors', 1);    
  error_reporting(E_ALL);  
  
  // Database connection parameters for MySQL/MariaDB  
  $servername = "localhost"; 
  $username = "root";  
  $password = "";       
  $dbname = "cafe-form";   
  
  // Create connection  
  $conn = new mysqli($servername, $username, $password, $dbname);  
  
  if ($conn->connect_error) {  
     die("Connection failed: " . $conn->connect_error);  
  }  
  
  // Set character set to UTF-8 for proper encoding of special characters  
  $conn->set_charset("utf8mb4");  
  
  // Create table if it doesn't exist  
  $sql = "CREATE TABLE IF NOT EXISTS customer_form (  
    id INT(11) AUTO_INCREMENT PRIMARY KEY,  
    full_name VARCHAR(255) NOT NULL,  
    phone_number VARCHAR(20) NOT NULL,  
    pickup_time VARCHAR(10) NOT NULL,  
    payment_method VARCHAR(20) NOT NULL,  
    cart_items TEXT NOT NULL,  
    total_price DECIMAL(10, 2) NOT NULL,  
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
  )";  
  if ($conn->query($sql) === TRUE) {  
     echo "Table created successfully";  
  } else {  
     echo "Error creating table: " . $conn->error;  
  }  
  
  // Check if form is submitted via POST method  
  if ($_SERVER["REQUEST_METHOD"] == "POST") {  
     // Sanitize and validate input data  
     $fullname = $conn->real_escape_string($_POST['fullname']);  
     $phone_number = $conn->real_escape_string($_POST['number']);  
     $pickup_time = $conn->real_escape_string($_POST['timePickup']);  
     $payment_method = $conn->real_escape_string($_POST['payment']);  
     $cart_items_json = $_POST['cart_items'];  // Cart data (JSON)  
  
     // Decode JSON object to PHP array  
     $cart_items_array = json_decode($cart_items_json, true);  
  
     // Extract product names from cart items array  
     $product_names = array();  
     $total_price = 0;  
     foreach ($cart_items_array as $item) {  
        $product_names[] = $item['name'];  
        $total_price += $item['price'] * $item['quantity'];  
     }  
     $cart_items = implode(', ', $product_names); // Convert product names to comma-separated string  
  
     // Prepare SQL statement for inserting order data  
     $sql = "INSERT INTO customer_form (full_name, phone_number, pickup_time, payment_method, cart_items, total_price)  
          VALUES ('$fullname', '$phone_number', '$pickup_time', '$payment_method', '$cart_items', '$total_price')";  
  
     // Execute query and check if the data is inserted  
     if ($conn->query($sql) === TRUE) {  
        // If successful, redirect to the order confirmation page  
        header('Location: order-confirmation.html');  
        exit();  
     } else {  
        // If there's an error, show it  
        echo "Error: " . $sql . "<br>" . $conn->error;  
     }  
  
     // Close the connection  
     $conn->close();  
  } else {  
     // Handle case where the form is not submitted correctly  
     echo "Invalid request method.";  
  }  
  ?>