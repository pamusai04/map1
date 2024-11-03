<?php 
include("./home.php");
include("./connection.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
    $location = filter_input(INPUT_POST, "location", FILTER_SANITIZE_SPECIAL_CHARS);
    $contact_number = filter_input(INPUT_POST, "contact_number", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, "address", FILTER_SANITIZE_SPECIAL_CHARS);
    $latitude = filter_input(INPUT_POST, "latitude", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $longitude = filter_input(INPUT_POST, "longitude", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Initialize an array to store error messages
    $errors = [];

    // Validate required fields
    if (empty($name)) {
        $errors[] = "Please enter the college name.";
    }
    if (empty($location)) {
        $errors[] = "Please enter the location.";
    }
    if (empty($contact_number)) {
        $errors[] = "Please enter the contact number.";
    }
    if (empty($email)) {
        $errors[] = "Please enter the email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (empty($latitude)) {
        $errors[] = "Please enter the latitude.";
    } elseif (!is_numeric($latitude) || $latitude < -90 || $latitude > 90) {
        $errors[] = "Latitude should be a number between -90 and 90.";
    }
    if (empty($longitude)) {
        $errors[] = "Please enter the longitude.";
    } elseif (!is_numeric($longitude) || $longitude < -180 || $longitude > 180) {
        $errors[] = "Longitude should be a number between -180 and 180.";
    }

    // If no errors, insert into the database
    if (empty($errors)) {
        $sql_q = "SELECT * FROM colleges WHERE name = '$name' AND latitude = '$latitude' AND longitude = '$longitude'";
        $result = mysqli_query($conn, $sql_q);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="message">College location already exists!</div>';
        } else {
            $sql = "INSERT INTO colleges (name, location, contact_number, email, address, latitude, longitude) 
                    VALUES ('$name', '$location', '$contact_number', '$email', '$address', '$latitude', '$longitude')";
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

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Location Details Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
</head>
<body>

    <div id="map"></div> <!-- Map container -->

    <!-- Display the connection message outside the form at the top -->
    <div class="message" id="message"><?php echo $message; ?></div>

    <div class="parent">
        <div class="container">
            <h2 class="small-heading">Enter College Location Details</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="form_group">
                    <label for="name">College Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form_group">
                    <label for="location">Location</label>
                    <input type="text" name="location" class="form-control" required>
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
                    <label for="latitude">Latitude</label>
                    <input type="text" name="latitude" class="form-control" id="lat" required>
                </div>
                <div class="form_group">
                    <label for="longitude">Longitude</label>
                    <input type="text" name="longitude" class="form-control" id="lng" required>
                </div>
                <div class="form_group">
                    <label for="address">Address</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
                <input type="submit" name="add_data" value="Add Data" class="btn">
            </form>

            <div class="back">
                <input type="button" value="Go to Home " id="goToIndex" class="btn">
            </div>
        </div>
    </div>



    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
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

