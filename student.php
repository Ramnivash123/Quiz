<?php
session_start();

// Include database connection
include 'db.php';

try {
    // Fetch assignments from the database including the assigned date
    $sql = "SELECT id, title, timer, subject, c_date FROM exam";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $assignments = [];
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($results)) {
        // Group assignments by subject
        foreach ($results as $row) {
            $assignments[$row["subject"]][] = $row;
        }
    } else {
        $noAssignments = true;
    }
} catch(PDOException $e) {
    die("Error executing query: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">ClassMate</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="studentt.php"><i class="fas fa-user"></i> Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Assignments</button>
                <a href="marks.php" class="btn btn-outline-primary">View Score</a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php
                if (isset($noAssignments)) {
                    echo '<p class="text-muted">No assignments found</p>';
                } else {
                    foreach ($assignments as $subject => $subjectAssignments) {
                        echo '<h2 class="h4 text-primary border-bottom pb-2 mb-3">' . htmlspecialchars($subject) . '</h2>';
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-hover">';
                        echo '<thead class="table-primary">
                                <tr>
                                    <th>Assignment</th>
                                    <th>Timer</th>
                                    <th>Assigned On</th>
                                    <th>Status</th>
                                </tr>
                              </thead>';
                        echo '<tbody>';
                        
                        foreach ($subjectAssignments as $assignment) {
                            $assignedOn = date("Y-m-d H:i:s", strtotime($assignment["c_date"]));
                            
                            try {
                                $checkSql = "SELECT status FROM marks WHERE title = :title AND stu_name = :student_name";
                                $checkStmt = $conn->prepare($checkSql);
                                $checkStmt->bindParam(':title', $assignment["title"], PDO::PARAM_STR);
                                $checkStmt->bindParam(':student_name', $_SESSION['student_name'], PDO::PARAM_STR);
                                $checkStmt->execute();
                                $submittedResult = $checkStmt->fetch(PDO::FETCH_ASSOC);
                                
                                $submitted = $submittedResult && $submittedResult['status'] == 'completed';

                                if (!$submitted) {
                                    echo '<tr>
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="viewAssignment(\'' . htmlspecialchars($assignment["title"]) . '\')">
                                                ' . htmlspecialchars($assignment["title"]) . '
                                            </button>
                                        </td>
                                        <td>' . htmlspecialchars($assignment["timer"]) . ' mins</td>
                                        <td>' . htmlspecialchars($assignedOn) . '</td>
                                        <td><span class="text-danger">Pending</span></td>
                                    </tr>';
                                } else {
                                    echo '<tr>
                                        <td>' . htmlspecialchars($assignment["title"]) . '</td>
                                        <td>' . htmlspecialchars($assignment["timer"]) . ' mins</td>
                                        <td>' . htmlspecialchars($assignedOn) . '</td>
                                        <td><span class="text-success">Completed</span></td>
                                    </tr>';
                                }
                            } catch(PDOException $e) {
                                echo '<tr><td colspan="4" class="text-danger">Error checking assignment status: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                            }
                        }
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function viewAssignment(title) {
        window.location = "assignments.php?title=" + encodeURIComponent(title);
    }
    </script>
</body>
</html>