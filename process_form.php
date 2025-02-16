<?php
//Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

//redirect after 5 seconds
header("Refresh: 5; url=index.html");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["choice"])) {
        $choice = $_POST["choice"];
        echo "You selected: " . htmlspecialchars($choice);
        echo "\nRedirecting back to main site in 5 seconds.";
    } else {
        echo "No choice selected.";
        echo "\nRedirecting back to main site in 5 seconds.<br>";
    }
} else {
    echo "Invalid request.";
    echo "\nRedirecting back to main site in 5 seconds.<br>";
}
?>
