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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #f5f5f5;
            --text-color: #333;
            --accent-color: #e74c3c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--secondary-color);
            color: var(--text-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-color);
            margin-left: 20px;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        main {
            margin-top: 40px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 32px;
            color: var(--primary-color);
        }

        .dashboard-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #3a7bc8;
        }

        .btn-secondary {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .assignments {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .subject-header {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        tr {
            background-color: #f9f9f9;
            transition: transform 0.3s ease;
        }

        tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .status {
            font-weight: 500;
        }

        .status-pending {
            color: var(--accent-color);
        }

        .status-completed {
            color: #27ae60;
        }

        .assignment-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .assignment-button:hover {
            background-color: #3a7bc8;
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">ClassMate</div>
            <div class="nav-links">
                <a href="index.html"><i class="fas fa-home"></i> Home</a>
                <a href="#"><i class="fas fa-book"></i> Courses</a>
                <a href="#"><i class="fas fa-user"></i> Profile</a>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['student_name'] ?? 'Student'); ?>!</h1>
            <div class="dashboard-actions">
                <button class="btn btn-primary">Assignments</button>
                <button class="btn btn-secondary">View Score</button>
            </div>
        </div>

        <section class="assignments">
            <?php
            if (isset($noAssignments)) {
                echo "<p>No assignments found</p>";
            } else {
                foreach ($assignments as $subject => $subjectAssignments) {
                    echo "<h2 class='subject-header'>" . htmlspecialchars($subject) . "</h2>";
                    echo "<table>";
                    echo "<tr><th>Assignment</th><th>Timer</th><th>Assigned On</th><th>Status</th></tr>";
                    
                    foreach ($subjectAssignments as $assignment) {
                        // Format the date for display
                        $assignedOn = date("Y-m-d H:i:s", strtotime($assignment["c_date"]));
                        
                        try {
                            // Check if the assignment has been submitted by the student
                            $checkSql = "SELECT status FROM marks WHERE title = :title AND stu_name = :student_name";
                            $checkStmt = $conn->prepare($checkSql);
                            $checkStmt->bindParam(':title', $assignment["title"], PDO::PARAM_STR);
                            $checkStmt->bindParam(':student_name', $_SESSION['student_name'], PDO::PARAM_STR);
                            $checkStmt->execute();
                            $submittedResult = $checkStmt->fetch(PDO::FETCH_ASSOC);
                            
                            $submitted = $submittedResult && $submittedResult['status'] == 'completed';

                            if (!$submitted) {
                                echo "<tr>
                                    <td>
                                        <button class='assignment-button' onclick='viewAssignment(\"" . htmlspecialchars($assignment["title"]) . "\")'>
                                            " . htmlspecialchars($assignment["title"]) . "
                                        </button>
                                    </td>
                                    <td>" . htmlspecialchars($assignment["timer"]) . " mins</td>
                                    <td>" . htmlspecialchars($assignedOn) . "</td>
                                    <td class='status status-pending'>Pending</td>
                                </tr>";
                            } else {
                                echo "<tr>
                                    <td>" . htmlspecialchars($assignment["title"]) . "</td>
                                    <td>" . htmlspecialchars($assignment["timer"]) . " mins</td>
                                    <td>" . htmlspecialchars($assignedOn) . "</td>
                                    <td class='status status-completed'>Completed</td>
                                </tr>";
                            }
                        } catch(PDOException $e) {
                            echo "<tr><td colspan='4'>Error checking assignment status: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                    }
                    echo "</table>";
                }
            }
            ?>
        </section>
    </main>

    <script>
    function viewAssignment(title) {
        window.location = "assignments.php?title=" + encodeURIComponent(title);
    }
    </script>
</body>
</html>