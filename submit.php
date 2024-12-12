<?php
// Start the session
session_start();

include 'db.php';

// Check if title parameter is set in the URL
if (isset($_GET['title'])) {
    // Get the title from the URL
    $title = $_GET['title'];

    // Fetch assignment details from the database
    $sql = "SELECT * FROM assignments WHERE title = :title";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':title' => $title]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retrieve student name from session
    $student_name = $_SESSION['student_name'] ?? '';  // Moved this line here

    if (count($result) > 0) {
        // Initialize counters for correct and wrong answers
        $correctCount = 0;
        $wrongCount = 0;

        // Get the form data
        $startTime = isset($_POST['start_time']) ? $_POST['start_time'] : '';
        $endTime = isset($_POST['end_time']) ? $_POST['end_time'] : '';

        // Convert times to DateTime objects
        $startDateTime = new DateTime($startTime);
        $endDateTime = new DateTime($endTime);
        $timeDifference = $endDateTime->getTimestamp() - $startDateTime->getTimestamp(); // Time difference in seconds

        // Iterate through each assignment
        foreach ($result as $row) {
            // Get the assignment ID
            $assignmentId = $row['id'];

            // Get the selected option for this assignment
            $selectedOption = $_POST['option'][$assignmentId];

            // Check if the selected option matches the correct answer
            if ($selectedOption == $row['answer']) {
                $correctCount++;
            } else {
                $wrongCount++;
            }
        }

        // Calculate the marks
        $totalQuestions = $correctCount + $wrongCount;
        $marks = ($correctCount / $totalQuestions) * 100;

        // Insert or update marks in the marks table, including stu_name and status as 'completed'
        $marksSql = "INSERT INTO marks (stu_name, title, correct, wrong, marks, start_time, end_time, time_difference, status)
                     VALUES (:stu_name, :title, :correct, :wrong, :marks, :start_time, :end_time, :time_difference, 'completed')
                     ON DUPLICATE KEY UPDATE correct = :correct, wrong = :wrong, marks = :marks,
                     start_time = :start_time, end_time = :end_time, time_difference = :time_difference, status = 'completed'";

        $stmt = $conn->prepare($marksSql);
        $stmt->execute([
            ':stu_name' => $student_name,
            ':title' => $title,
            ':correct' => $correctCount,
            ':wrong' => $wrongCount,
            ':marks' => $marks,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
            ':time_difference' => $timeDifference
        ]);

        header("Location: student.php");
        exit;
    } else {
        echo "No assignment details found for the given title";
    }
} else {
    echo "No title specified";
}
?>
