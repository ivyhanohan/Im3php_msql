<?php
include 'database.php';

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM guest_info WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script> window.location='index.php';</script>";
    } else {
        echo "<script>alert('Error deleting guest.'); window.location='index.php';</script>";
    }
}

$conn->close();
?>


