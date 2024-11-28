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

// Process take ticket form submission
if(isset($_POST['take_ticket'])) {
    // Insert ticket into database
    $sql = "INSERT INTO tickets (ticket_number) VALUES (NULL)";
    mysqli_query($conn, $sql);

    // Get ticket number from database
    $ticket_number = mysqli_insert_id($conn);

    // Store ticket number in session
    $_SESSION['ticket_number'] = $ticket_number;

    // Redirect to ticket display page
    header('Location: display.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Queue System</title>
    <style>
        /* CSS for ticket form */
    </style>
</head>
<body>
    <form method="post">
        <h1>Take Ticket</h1>
        <p>Your ticket number is:</p>
        <p><?php echo isset($_SESSION['ticket_number']) ? $_SESSION['ticket_number'] : ''; ?></p>
        <button type="submit" name="take_ticket">Take Ticket</button>
    </form>
</body>
</html>
