<?php 
include("./home.php");
include("./connection.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
    $branch = filter_input(INPUT_POST, "branch", FILTER_SANITIZE_SPECIAL_CHARS);
    $contact_number = filter_input(INPUT_POST, "contact_number", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $latitude = filter_input(INPUT_POST, "latitude", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $longitude = filter_input(INPUT_POST, "longitude", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Initialize an array to store error messages
    $errors = [];

    // Validate bank name (should not be empty and should contain only letters and spaces)
    if (empty($name)) {
        $errors[] = "Please enter the bank name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors[] = "Bank name should contain only letters and spaces.";
    }

    // Validate branch (same as bank name)
    if (empty($branch)) {
        $errors[] = "Please enter the branch name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $branch)) {
        $errors[] = "Branch name should contain only letters and spaces.";
    }

    // Validate contact number (should contain only digits and be of a reasonable length)
    if (empty($contact_number)) {
        $errors[] = "Please enter the contact number.";
    } elseif (!preg_match("/^\d{10}$/", $contact_number)) {
        $errors[] = "Contact number should be a valid 10-digit number.";
    }

    // Validate email address
    if (empty($email)) {
        $errors[] = "Please enter the email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate latitude (should be a valid float number between -90 and 90)
    if (empty($latitude)) {
        $errors[] = "Please enter the latitude.";
    } elseif (!is_numeric($latitude) || $latitude < -90 || $latitude > 90) {
        $errors[] = "Latitude should be a number between -90 and 90.";
    }

    // Validate longitude (should be a valid float number between -180 and 180)
    if (empty($longitude)) {
        $errors[] = "Please enter the longitude.";
    } elseif (!is_numeric($longitude) || $longitude < -180 || $longitude > 180) {
        $errors[] = "Longitude should be a number between -180 and 180.";
    }

    // If there are no errors, proceed with the database insertion
    if (empty($errors)) {
        // Check if the bank location already exists
        $sql_q = "SELECT * FROM banks WHERE name = '$name' AND latitude = '$latitude' AND longitude = '$longitude'";
        $result = mysqli_query($conn, $sql_q);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="message">Bank location already exists!</div>';
        } else {
            // Insert new bank location into the database
            $sql = "INSERT INTO banks (name, branch, contact_number, email, latitude, longitude) 
                    VALUES ('$name', '$branch', '$contact_number', '$email', '$latitude', '$longitude')";
            try {
                mysqli_query($conn, $sql);
                echo '<script>window.location.href="index.php";</script>';
                exit();
            } catch (mysqli_sql_exception $e) {
                echo '<div class="message">Error: Unable to add location. Please try again.</div>';
            }
        }
    } else {
        // Display errors if any
        foreach ($errors as $error) {
            echo '<div class="message">' . $error . '</div>';
        }
    }
}

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Details Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    
</head>
<body>

    <div id="map"></div>
    <!-- Display the connection message outside the form at the top -->
    <div class="message" id="message"><?php echo $message; ?></div>

    <div class="parent">
        <div class="container">
            <h2  class="small-heading">Enter Bank Location Details</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="form_group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form_group">
                    <label for="branch">Branch/Location</label>
                    <input type="text" name="branch" class="form-control" required>
                </div>
                <div class="form_group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" required>
                </div>
                <div class="form_group">
                    <label for="email">Email</label>
                    <input type="text" name="email" class="form-control" required>
                </div>
                <div class="form_group">
                    <label for="services">Services</label>
                    <input type="text" name="services" class="form-control" required>
                </div>
                <div class="form_group">
                    <label for="working_hours">Working Hours</label>
                    <input type="text" name="working_hours" class="form-control" required>
                </div>
                <div class="form_group">
                    <label for="latitude">Latitude</label>
                    <input type="text" name="latitude" class="form-control" id="lat" required>
                </div>
                
                <div class="form_group">
                    <label for="longitude">Longitude</label>
                    <input type="text" name="longitude" class="form-control" id="lng" required>
                </div>
                <input type="submit" name="add_data" id="goToIndexz" value="Add Data" class="btn">
            </form>


            <div class="back">
                <input type="button" value="Go to Home " id="goToIndex" class="btn">
            </div>
        </div>

        
    </div>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <!-- <script src="index.js"></script> -->
    <script src="./scritp.js"></script>

    
    <script>
        // Button click navigation
        document.getElementById('goToIndex').onclick = function() {
            window.location.href = 'index.php'; // Navigate to index.php
        };
        

        // Check the message content and change color accordingly
        let message = document.getElementById("message");

        
        if (message.textContent === "You are connected to the database") {
            message.classList.remove("message"); 
            message.classList.add("greens"); 
        } else if (message.textContent === "Could not connect to the database.") {
            message.classList.remove("greens"); 
            message.classList.add("message"); 
        }

    </script>

</body>
</html>
