<?php

//redirect after 5 seconds
header("Refresh: 8; url=index.html");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (isset($_POST["choice"])) {
$choice = $_POST["choice"];
        echo "You selected: " . htmlspecialchars($choice) . " Redirecting back to the main page in 8 seconds...";
        echo "You selected the color: " . htmlspecialchars($choice) . ". Redirecting back to the main page in 8 seconds...";
} else {
echo "No choice selected. Redirecting back to the main page in 8 seconds...";
}
} else {
echo "Invalid request. Redirecting back to main site in 8 seconds...";
}

//stops script execution after setting the header to prevent errors
exit();
?>