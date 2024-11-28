<?php 

$conn= new mysqli('192.168.88.215','jda','passw0rd456','depedldn_queue') or die("Could not connect to mysql".mysqli_error($con));
$conn2= new mysqli('192.168.88.215','jda','passw0rd456','upload_activity') or die("Could not connect to mysql".mysqli_error($con));