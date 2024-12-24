<?php
session_start();
include 'db.php';

// Fetch the teacher's name from the session
$teacher_name = $_SESSION['teacher_name'] ?? null;

if (!$teacher_name) {
    die("Teacher name not found in session.");
}

try {
    // Fetch data for the highest marks per subject-title combination
    $sql = "SELECT e.subject, m.title, m.stu_name, m.marks, m.date
        FROM marks m
        INNER JOIN exam e ON m.title = e.title
        WHERE e.teacher = :teacher
        ORDER BY e.subject, m.title, m.stu_name";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':teacher' => $teacher_name]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) {
        die("No data found.");
    }

    $response = [];
    $subjects = [];

    // Organize data by subject
    foreach ($data as $row) {
        $subject = $row['subject'];
        if (!isset($subjects[$subject])) {
            $subjects[$subject] = [];
        }
        $subjects[$subject][] = [
            'title' => $row['title'],
            'stu_name' => $row['stu_name'],
            'marks' => $row['marks'],
            'date' => $row['date']
        ];
    }

    // Build response for JSON
    foreach ($subjects as $subject => $users) {
        $response[] = [
            'subject' => $subject,
            'users' => $users
        ];

    }

    $totalPages = 1; // Adjust based on your pagination logic (if required)

} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Close the connection
$conn = null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .pagination {
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
        /* Style for the Analysis button */
        .btn-analysis {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn-analysis:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h2>Leaderboard</h2>

    <table id="leaderboardTable">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Title</th>
                <th>Student Name</th>
                <th>Marks</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data rows will be inserted here -->
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination" id="pagination"></div>

    <!-- Analysis Button -->
    <button class="btn-analysis" onclick="redirectToAnalysis()">Analysis</button>

    <script>
        // The PHP response is already formatted as a JSON structure.
        const response = <?php echo json_encode($response); ?>;
        const totalPages = <?php echo $totalPages; ?>;

        // Function to populate the table with data
        function populateTable(data) {
            const tableBody = document.querySelector('#leaderboardTable tbody');
            tableBody.innerHTML = ''; // Clear any existing rows

            data.forEach(subjectData => {
                const subject = subjectData.subject;
                subjectData.users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${subject}</td>
                        <td>${user.title}</td>
                        <td>${user.stu_name}</td>
                        <td>${user.marks}</td>
                        <td>${user.date}</td>
                    `;
                    tableBody.appendChild(row);
                });
            });
        }

        // Function to create pagination buttons
        function createPagination(totalPages) {
            const paginationDiv = document.getElementById('pagination');
            paginationDiv.innerHTML = ''; // Clear existing pagination

            for (let i = 1; i <= totalPages; i++) {
                const pageLink = document.createElement('a');
                pageLink.href = '#';
                pageLink.textContent = i;
                pageLink.onclick = function () {
                    alert('You clicked page ' + i);
                    // You can add functionality to fetch data for this page
                    // and update the table accordingly.
                };
                paginationDiv.appendChild(pageLink);
            }
        }

        // Populate the table with the fetched data
        populateTable(response);

        // Create pagination controls
        createPagination(totalPages);

        // Redirect to analysis2.php
        function redirectToAnalysis() {
            window.location.href = 'analysis2.php';
        }
    </script>

</body>
</html>
