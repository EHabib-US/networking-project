<?php
//Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

//redirect after 5 seconds
header("Refresh: 5; url=index.html");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (isset($_POST["choice"])) {
$choice = $_POST["choice"];
        echo "You selected the color: " . htmlspecialchars($choice) . ". Redirecting back to the main page in 5 seconds...";
} else {
echo "No choice selected. Redirecting back to the main page in 5 seconds...";
}
} else {
echo "Invalid request. Redirecting back to main site in 5 seconds...";
}

//stops script execution after setting the header to prevent errors
exit();
?>