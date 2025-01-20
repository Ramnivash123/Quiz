<?php
session_start();
include 'db.php';

if (isset($_GET['title'])) {
    $title = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_STRING);
    if (!$title) {
        die("Invalid title parameter");
    }

    try {
        $sql = "SELECT * FROM assignments WHERE title = :title";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':title' => $title]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        die("An error occurred while fetching assignment details.");
    }

    $student_name = $_SESSION['student_name'] ?? '';
    if (empty($student_name)) {
        die("Student name is not set. Please log in.");
    }

    if (count($result) > 0) {
        $correctCount = $wrongCount = 0;
        // Retrieve start and end times from POST
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';

        // Handle time-only inputs by appending the current date
        if (strpos($startTime, ':') !== false && strpos($startTime, '-') === false) {
            $startTime = date('Y-m-d') . ' ' . $startTime;
        }

        if (strpos($endTime, ':') !== false && strpos($endTime, '-') === false) {
            $endTime = date('Y-m-d') . ' ' . $endTime;
        }

        // Validate the format
        $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $startTime);
        $endDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $endTime);

        if (!$startDateTime) {
            die("Invalid start time format after adjustment: " . htmlspecialchars($startTime));
        }

        if (!$endDateTime) {
            die("Invalid end time format after adjustment: " . htmlspecialchars($endTime));
        }

        // Calculate time difference in seconds
        $timeDifference = $endDateTime->getTimestamp() - $startDateTime->getTimestamp();


        foreach ($result as $row) {
            $assignmentId = $row['id'];
            if (!isset($_POST['option'][$assignmentId])) {
                die("No option selected for question ID: " . $assignmentId);
            }

            $selectedOption = $_POST['option'][$assignmentId];
            if ($selectedOption == $row['answer']) {
                $correctCount++;
            } else {
                $wrongCount++;
            }
        }

        $totalQuestions = $correctCount + $wrongCount;
        $marks = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;

        try {
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
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            die("An error occurred while saving marks.");
        }
    } else {
        die("No assignment details found for the given title.");
    }
} else {
    die("No title specified.");
}
?>
