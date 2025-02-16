<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure 'choice' is set
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["choice"])) {
        $choice = $_POST["choice"];
        echo "You selected: " . htmlspecialchars($choice);
    } else {
        echo "No choice selected.";
    }
} else {
    echo "Invalid request.";
}
?>
