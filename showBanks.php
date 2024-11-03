<?php
include("./connection.php"); 
// Query to get bank data
$sql = "SELECT name, branch, contact_number, email, services, working_hours, latitude, longitude FROM banks;";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$banks = array();

if ($result->num_rows > 0) {
    // Fetch data into array
    while ($row = $result->fetch_assoc()) {
        $banks[] = $row;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($banks);

// Close the connection
$conn->close();
?>
