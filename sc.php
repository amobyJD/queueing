<?php
// Initialize session
session_start();

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "depedldn_queue";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get next ticket from database
$sql = "SELECT MIN(id) AS id FROM tickets WHERE called is NULL";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$next_ticket = $row['id'];

if(!$next_ticket) {
    $message = "No tickets in queue";
} else {
    // Update ticket to called status
    $sql = "UPDATE tickets SET called = 1 WHERE id = $next_ticket";
    mysqli_query($conn, $sql);

    $message = "Next customer: Ticket #$next_ticket";
}

// Clear session variable
unset($_SESSION['ticket_number']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Queue System</title>
    <style>
        /* CSS for display page */
    </style>
</head>
<body>
    <h1>Service Counter</h1>
    <h2><?php echo $message; ?></h2>
    <button id="next_btn">Next</button>
    <script>
        // JavaScript for manual calling
        var nextBtn = document.getElementById("next_btn");
        nextBtn.addEventListener("click", function() {
            window.location.reload();
        });
    </script>
</body>
</html>
