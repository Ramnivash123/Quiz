<?php
session_start();
include 'db.php';

$student_name = $_SESSION['student_name'] ?? '';
if (empty($student_name)) {
    die("Student name is not set. Please log in.");
}

// Check if options are submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedOptions = $_POST['option'] ?? [];
    $title = $_POST['assignment_title'] ?? 'Assignment';

    // Fetch assignment details from the database
    $sql = "SELECT * FROM assignments WHERE title = :title";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':title' => $title]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("No options submitted.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selected Options</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            color: #007bff;
        }
    </style>
</head>
<body>
    <h2>Your Selected Options for <?php echo htmlspecialchars($title); ?></h2>
    <div id="optionsContainer">
        <?php
        if (!empty($assignments)) {
            foreach ($assignments as $assignment) {
                $questionId = $assignment['id'];
                $selectedOption = isset($selectedOptions[$questionId]) ? htmlspecialchars($selectedOptions[$questionId]) : 'Not answered';
                echo "<p>Question " . htmlspecialchars($assignment['qn']) . ": Your Answer: " . $selectedOption . "</p>";
            }
        } else {
            echo "<p>No assignment details found.</p>";
        }
        ?>
    </div>
</body>
</html>