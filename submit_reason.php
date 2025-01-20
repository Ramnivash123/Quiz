<?php
include 'db.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reason'])) {
        $reason = $_POST['reason'];

        // Insert the reason into the database
        $sql = "INSERT INTO reasons (reason) VALUES (:reason)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':reason', $reason);
        $stmt->execute();

        // Redirect to student.php
        header("Location: student.php");
        exit();
    }
}
?>