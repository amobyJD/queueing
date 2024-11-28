<?php

if(isset($_POST['activity'])){
    include("db_connect.php");
    // Your database query
    $currentDate = date("Y-m-d");
    $uploads = $conn2->query("SELECT * FROM activity WHERE when_at >= '$currentDate' ORDER BY when_at ASC");

    // Check if query executed successfully
    if (!$uploads) {
        echo "Error executing query: " . $conn2->error;
    }else{
        echo json_encode($uploads->fetch_all(MYSQLI_ASSOC));
    }
  
}
?>