<?php
session_start();

include 'db.php';

// Retrieve student name from session
$student_name = $_SESSION['student_name'] ?? '';

// Fetch data from marks table for the specific student and join with exam table
$sql = "
    SELECT e.subject, m.title, m.correct, m.wrong, m.marks, m.time_difference 
    FROM marks m
    JOIN exam e ON m.title = e.title
    WHERE m.stu_name = ?
    ORDER BY e.subject
";
$stmt = $conn->prepare($sql);
$stmt->execute([$student_name]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize an associative array to store grouped results
$grouped_marks = [];

// Group results by subject
foreach ($result as $row) {
    $grouped_marks[$row['subject']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marks</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    
                    
                </div>

                <?php
                // Display marks data grouped by subject
                foreach ($grouped_marks as $subject => $marks) {
                    echo '<div class="mb-4">';
                    echo '<h3 class="h5 text-primary border-bottom pb-2 mb-3">Subject: ' . htmlspecialchars($subject) . '</h3>';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-hover">';
                    echo '<thead class="table-primary">
                            <tr>
                                <th>Title</th>
                                <th>Correct</th>
                                <th>Wrong</th>
                                <th>Marks</th>
                                <th>Time</th>
                            </tr>
                          </thead>';
                    echo '<tbody>';
                    foreach ($marks as $mark) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($mark['title']) . '</td>';
                        echo '<td><span class="text-success">' . htmlspecialchars($mark['correct']) . '</span></td>';
                        echo '<td><span class="text-danger">' . htmlspecialchars($mark['wrong']) . '</span></td>';
                        echo '<td><strong>' . htmlspecialchars($mark['marks']) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($mark['time_difference']) . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close statement and connection
$stmt = null;
$conn = null;
?>