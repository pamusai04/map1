<?php
include("./connection.php"); 

// Query to get hospital data
$sql = "SELECT name, location, contact_number, email, types_of_treatments, latitude, longitude, visiting_hours FROM hospitals;";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$hospitals = array();

if ($result->num_rows > 0) {
    // Fetch data into array
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($hospitals);

// Close the connection
$conn->close();
?>
