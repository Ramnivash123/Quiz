<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <!-- Profile Button -->
    <div class="profile-btn">
        <a href="teacherr.php" class="btn btn-outline-primary">Profile</a>
    </div>

    <div class="container my-5">
        <h1 class="text-center text-primary mb-4">Assignments</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Assignment</th>
                        <th>Timer</th>
                        <th>Assigned On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    session_start();

                    // Establishing a connection to the database (replace these values with your database credentials)
                    include 'db.php';

                    $teacher_name = $_SESSION['teacher_name'] ?? '';

                    // Fetching assignments from the database, including the assigned date
                    $sql = "SELECT id, title, timer, c_date FROM exam WHERE teacher = :teacher";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(':teacher', $teacher_name, PDO::PARAM_STR);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($result === false) {
                        die("Error executing query: " . $conn->errorInfo()[2]);
                    }

                    if (count($result) > 0) {
                        // Output data of each row
                        foreach ($result as $row) {
                            // Format the assigned date for display
                            $assignedOn = date("Y-m-d H:i:s", strtotime($row["c_date"]));
                            echo "<tr>
                                    <td><button class='btn btn-primary btn-sm' onclick='viewAssignment(\"" . htmlspecialchars($row["title"]) . "\")'>" . htmlspecialchars($row["title"]) . "</button></td>
                                    <td>" . htmlspecialchars($row["timer"]) . " mins</td>
                                    <td>" . htmlspecialchars($assignedOn) . "</td>
                                    <td>
                                        <button class='btn btn-danger btn-sm' onclick='deleteAssignment(\"" . htmlspecialchars($row["id"]) . "\")'>Delete</button>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No assignments found</td></tr>";
                    }

                    $stmt->closeCursor(); // Close the cursor
                    $conn = null; // Close the connection
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewAssignment(title) {
            window.location = "view2.php?title=" + encodeURIComponent(title);
        }

        function deleteAssignment(id) {
            if (confirm("Are you sure you want to delete this assignment?")) {
                window.location = "delete_assignment.php?id=" + encodeURIComponent(id);
            }
        }
    </script>
</body>
</html>
