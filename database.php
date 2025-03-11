<?php

$servername = "localhost"; 
$username = "root";       
$password = "";           
$dbname = "mysql_guest_db";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function addGuest($conn, $first_name, $last_name, $email, $photo, $document) {
    $stmt = $conn->prepare("INSERT INTO guest_info (first_name, last_name, email, photo, document) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $photo, $document);
    $stmt->execute();
    $stmt->close();
}

?>
