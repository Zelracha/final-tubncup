<?php
// login.php
include 'db_config.php';
session_start(); // Start the session at the top

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the query to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM admin_accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Verify password using password_verify()
        if (password_verify($password, $row['password'])) {
            // Start the session and redirect on success
            $_SESSION['admin_logged_in'] = true;
            echo "success"; // Send success response back to JavaScript
        } else {
            echo "Incorrect password."; // Send error message back
        }
    } else {
        echo "No user found with that username."; // Send error message back
    }
}
?>
