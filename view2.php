<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignment</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <?php
        // Establishing a connection to the database (replace these values with your database credentials)
        include 'db.php';

        // Retrieve the title parameter from the URL
        if (isset($_GET['title'])) {
            $title = $_GET['title'];

            // Query the assignments table based on the title
            $sql = "SELECT id, question, opt1, opt2, opt3, opt4, answer FROM assignments WHERE title = :title";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "<h2 class='text-center text-primary'>Assignments for " . htmlspecialchars($title) . "</h2>";
                echo "<form action='update_assignment.php' method='post'>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered table-striped mt-4'>";
                echo "<thead class='table-primary'><tr><th>Question</th><th>Option 1</th><th>Option 2</th><th>Option 3</th><th>Option 4</th><th>Answer</th><th>Actions</th></tr></thead>";
                echo "<tbody>";

                // Output data of each row
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td><input type='text' class='form-control' name='question[]' value='" . htmlspecialchars($row["question"]) . "'></td>";
                    echo "<td><input type='text' class='form-control' name='opt1[]' value='" . htmlspecialchars($row["opt1"]) . "'></td>";
                    echo "<td><input type='text' class='form-control' name='opt2[]' value='" . htmlspecialchars($row["opt2"]) . "'></td>";
                    echo "<td><input type='text' class='form-control' name='opt3[]' value='" . htmlspecialchars($row["opt3"]) . "'></td>";
                    echo "<td><input type='text' class='form-control' name='opt4[]' value='" . htmlspecialchars($row["opt4"]) . "'></td>";
                    echo "<td><input type='text' class='form-control' name='answer[]' value='" . htmlspecialchars($row["answer"]) . "'></td>";
                    echo "<td><input type='hidden' name='id[]' value='" . htmlspecialchars($row["id"]) . "'>";
                    echo "<button type='submit' class='btn btn-success btn-sm'>Update</button></td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                echo "</form>";
            } else {
                echo "<div class='alert alert-warning text-center'>No assignments found for " . htmlspecialchars($title) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger text-center'>No title parameter specified.</div>";
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
