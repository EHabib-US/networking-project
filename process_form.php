<?php
//Include the database configuration file via require_once
require_once dirname(__FILE__) . '/var/www/config/db_config.php'; 

//Create connection and SHOW any errors
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$choice = $_POST["choice"];
//Validate that the color is one of the expected values
$valid_colors = ['Red', 'Blue', 'Green'];
if (!in_array($choice, $valid_colors)) {
    die("Invalid color selection");
}

//Check if daily limit of 5 entries has been hit or not
$today = date("Y-m-d");
$sql = "SELECT COUNT(*) as count FROM user_colors WHERE DATE(added_on) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$daily_count = $row['count'];
$stmt->close();

//Set the message based on daily count
if ($daily_count >= 5) {
    $message = "Daily limit reached (5 entries per day). Come back tomorrow! :) ";
} else {
    //Prepare and execute the query to insert the color
    $stmt = $conn->prepare("INSERT INTO user_colors (color) VALUES (?)");
    $stmt->bind_param("s", $choice);
    
    //Execute the query
    if ($stmt->execute()) {
        $message = "Your color choice ($choice) has been saved successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

//Close connection
$conn->close();

// Redirect after 5 seconds
header("Refresh: 5; url=index.html");
echo htmlspecialchars($message) . ". Redirecting back to the main page in 5 seconds...";

//Stop script execution to prevent errors
exit();
?>