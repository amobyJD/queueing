<?php
$conn= new mysqli('192.168.88.215','jda','passw0rd456','depedldn_cert') or die("Could not connect to mysql".mysqli_error($con));
// Query to fetch data
$sql = "SELECT CONCAT(e.firstname, ' ', LEFT(e.middlename, 1), '. ', e.lastname) AS name, t.training_title, t.venue, DATE(t.datefrom) AS datefrom, DATE(t.dateto) AS dateto FROM `trainings` AS t INNER JOIN cert_office AS c ON c.certOffice_id = t.office INNER JOIN depedldn.tbl_employee AS e ON e.hris_code = t.program_holder WHERE t.datefrom <= CURDATE();";

$result = $conn->query($sql);

$data = array();
// Fetch each row of data and store it in $data array
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Close the database connection
$conn->close();

// Return data as a JSON object
echo json_encode($data);
?>
