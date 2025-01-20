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
        .profile-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="profile-btn">
        <a href="teacherr.php" class="btn btn-outline-primary">Profile</a>
    </div>
    <div class="container">
        <h2>Leaderboard</h2>

        <!-- Filter Section -->
        <div class="mb-4">
            <label for="subjectSelect" class="form-label">Select Subject:</label>
            <select id="subjectSelect" class="form-select">
                <option value="">-- Select Subject --</option>
                <!-- Subjects will be populated dynamically -->
            </select>

            <label for="titleSelect" class="form-label mt-3">Select Title:</label>
            <select id="titleSelect" class="form-select">
                <option value="">-- Select Title --</option>
                <!-- Titles will be populated dynamically -->
            </select>
        </div>

        <!-- Table Section -->
        <table id="leaderboardTable" class="table table-striped" style="display: none;">
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
        <div class="pagination" id="pagination" style="display: none;"></div>

        <!-- Analysis Button -->
        <button class="btn-analysis btn btn-success" onclick="redirectToAnalysis()">Analysis</button>
    </div>

    <script>
        const response = <?php echo json_encode($response); ?>;
        const totalPages = <?php echo $totalPages; ?>;

        const subjectSelect = document.getElementById('subjectSelect');
        const titleSelect = document.getElementById('titleSelect');
        const leaderboardTable = document.getElementById('leaderboardTable');
        const paginationDiv = document.getElementById('pagination');

        // Populate Subject Dropdown
        const subjects = Array.from(new Set(response.map(item => item.subject)));
        subjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject;
            option.textContent = subject;
            subjectSelect.appendChild(option);
        });

        // Populate Title Dropdown based on Subject Selection
        subjectSelect.addEventListener('change', () => {
            titleSelect.innerHTML = '<option value="">-- Select Title --</option>';
            const selectedSubject = subjectSelect.value;
            if (selectedSubject) {
                const titles = Array.from(new Set(
                    response.find(item => item.subject === selectedSubject)?.users.map(user => user.title) || []
                ));
                titles.forEach(title => {
                    const option = document.createElement('option');
                    option.value = title;
                    option.textContent = title;
                    titleSelect.appendChild(option);
                });
            }
            updateTable();
        });

        // Update Table based on Subject and Title Selection
        titleSelect.addEventListener('change', updateTable);

        function updateTable() {
            const selectedSubject = subjectSelect.value;
            const selectedTitle = titleSelect.value;

            if (selectedSubject && selectedTitle) {
                leaderboardTable.style.display = 'table';
                paginationDiv.style.display = 'flex';

                const filteredData = response
                    .filter(item => item.subject === selectedSubject)
                    .flatMap(item => item.users.filter(user => user.title === selectedTitle));

                const tableBody = leaderboardTable.querySelector('tbody');
                tableBody.innerHTML = '';

                filteredData.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${selectedSubject}</td>
                        <td>${user.title}</td>
                        <td>${user.stu_name}</td>
                        <td>${user.marks}</td>
                        <td>${user.date}</td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                leaderboardTable.style.display = 'none';
                paginationDiv.style.display = 'none';
            }
        }

        function redirectToAnalysis() {
            window.location.href = 'analysis2.php';
        }
    </script>
</body>
</html>

