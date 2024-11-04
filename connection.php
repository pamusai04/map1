<?php

// Database connection parameters
$db_server = "08wwf.h.filess.io";  // Hostname
$db_user = "map_sheepstore";       // Database username
$db_pass = "96c0af19416fd82aaea106064115b748751f24a4"; // Database password
$db_name = "map_sheepstore";       // Database name
$db_port = 3307;                    // Port number

$conn = ""; 
// Initialize message variable
$message = "";

try {
    // Enable exception reporting for mysqli
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    // Connect to the database
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name, $db_port);
    
    // If connection is successful
    $message = "You are connected to the database"; // Store success message
} catch (mysqli_sql_exception $e) {
    // Handle connection errors
    $message = "Connection failed: " . $e->getMessage(); // Store failure message
    die($message); // Terminate the script if connection fails
}


?>