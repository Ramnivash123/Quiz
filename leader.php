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
    <title>Class Mate - E-learning at your home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background-color: #f4f8fb;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 2.2rem;
            font-weight: bold;
        }

        #leaderboardTable {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        #leaderboardTable thead {
            background-color: #007bff;
            color: #fff;
        }

        #leaderboardTable th, #leaderboardTable td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f1f1f1;
            font-size: 1rem;
        }

        #leaderboardTable tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #leaderboardTable tr:hover {
            background-color: #f5f5f5;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
        }

        .pagination a:hover {
            background-color: #0056b3;
        }

        .pagination a.active {
            background-color: #0056b3;
            cursor: not-allowed;
        }

        .btn-analysis {
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            margin: 20px auto;
        }

        .btn-analysis:hover {
            background-color: #218838;
        }

        .home-logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 50px;
            transition: transform 0.2s ease;
        }

        .home-logo:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="container">
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
    </div>

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
