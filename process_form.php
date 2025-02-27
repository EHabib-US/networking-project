<?php
ob_start();
require_once '/var/www/config/db_config.php';
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$message = "";
$daily_count = 0;
$db_entries = [];
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["choice"])) {
    $choice = $_POST["choice"];
    
    //Color validator
    $valid_colors = ['Red', 'Blue', 'Green'];
    if (!in_array($choice, $valid_colors)) {
        $message = "Invalid color selection";
        $error = true;
    } else {
        //Daily limit check
        $today = date("Y-m-d");
        $sql = "SELECT COUNT(*) as count FROM user_colors WHERE DATE(added_on) = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $message = "Error preparing statement: " . $conn->error;
            $error = true;
        } else {
            $stmt->bind_param("s", $today);
            if (!$stmt->execute()) {
                $message = "Error executing query: " . $stmt->error;
                $error = true;
            } else {
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $daily_count = $row['count'];
                $stmt->close();
                
                if ($daily_count >= 5) {
                    $message = "Daily limit reached (5 entries per day). Come back tomorrow! :)";
                } else {
                    $stmt = $conn->prepare("INSERT INTO user_colors (color) VALUES (?)");
                    if (!$stmt) {
                        $message = "Error preparing insert: " . $conn->error;
                        $error = true;
                    } else {
                        $stmt->bind_param("s", $choice);
                        if (!$stmt->execute()) {
                            $message = "Error: " . $stmt->error;
                            $error = true;
                        } else {
                            $message = "Your color choice ($choice) has been saved successfully!";
                            $daily_count++;
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }
} else {
    $message = "No form submission detected";
    $error = true;
}

//Retreieve db entires
$sql = "SELECT color, added_on FROM user_colors ORDER BY added_on DESC";
$result = $conn->query($sql);

if (!$result) {
    $message .= " Error retrieving database entries: " . $conn->error;
    $error = true;
} else {
    while ($row = $result->fetch_assoc()) {
        $db_entries[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Color Submission Result</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .color-box {
            width: 20px;
            height: 20px;
            display: inline-block;
            margin-right: 10px;
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    <h1>Form Submission Result</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    
    <h2>Database Entries</h2>
    <p>Today's submissions: <?php echo $daily_count; ?> of 5 allowed</p>
    
    <?php if (!empty($db_entries)): ?>
        <table>
            <tr>
                <th>Color</th>
                <th>Visual</th>
                <th>Submitted On</th>
            </tr>
            <?php foreach($db_entries as $row): 
                $colorCode = '';
                switch($row['color']) {
                    case 'Red': $colorCode = '#ff0000'; break;
                    case 'Blue': $colorCode = '#0000ff'; break;
                    case 'Green': $colorCode = '#00ff00'; break;
                    default: $colorCode = '#cccccc';
                }
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['color']); ?></td>
                    <td><span class="color-box" style="background-color: <?php echo $colorCode; ?>"></span></td>
                    <td><?php echo htmlspecialchars($row['added_on']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No entries found in the database.</p>
    <?php endif; ?>
    
    <p><a href="index.html">Return to the main page</a></p>
    
    <script>
        //redirect after 30 secs
        <?php if (!$error): ?>
        setTimeout(function() {
            window.location.href = 'index.html';
        }, 30000);
        <?php endif; ?>
    </script>
</body>
</html>
<?php
//end output buffering and flush
ob_end_flush();
?>