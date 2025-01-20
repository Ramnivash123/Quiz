<?php
session_start();
include 'db.php';

// Fetch the teacher's name from the session
$teacher_name = $_SESSION['teacher_name'] ?? null;

if (!$teacher_name) {
    die("Teacher name not found in session.");
}

try {
    // Fetch unique subject-title combinations for dropdown
    $dropdownSql = "SELECT DISTINCT e.subject, m.title
        FROM marks m
        INNER JOIN exam e ON m.title = e.title
        WHERE e.teacher = :teacher
        ORDER BY e.subject, m.title";

    $dropdownStmt = $conn->prepare($dropdownSql);
    $dropdownStmt->execute([':teacher' => $teacher_name]);

    $dropdownData = $dropdownStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for all students' marks
    $dataSql = "SELECT e.subject, m.title, m.stu_name, m.marks 
        FROM marks m
        INNER JOIN exam e ON m.title = e.title
        WHERE e.teacher = :teacher
        ORDER BY e.subject, m.title, m.stu_name";

    $dataStmt = $conn->prepare($dataSql);
    $dataStmt->execute([':teacher' => $teacher_name]);

    $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) {
        die("No data found.");
    }

    $chartData = [];

    foreach ($data as $row) {
        $subjectTitle = $row['subject'] . ' - ' . $row['title'];
        if (!isset($chartData[$subjectTitle])) {
            $chartData[$subjectTitle] = [];
        }
        $chartData[$subjectTitle][] = [
            'stu_name' => $row['stu_name'],
            'marks' => $row['marks']
        ];
    }
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Close the connection
$conn = null;

// Encode chartData for JavaScript
$chartDataJson = json_encode($chartData);
$dropdownDataJson = json_encode($dropdownData);
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analysis Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --text-color: #333;
            --bg-color: #f3f4f6;
            --white: #ffffff;
            --gray-100: #f7fafc;
            --gray-200: #edf2f7;
            --gray-300: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: var(--white);
            padding: 2rem;
        }

        .sidebar h1 {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        .sidebar ul {
            list-style-type: none;
        }

        .sidebar li {
            margin-bottom: 1rem;
        }

        .sidebar a {
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: opacity 0.3s ease;
        }

        .sidebar a:hover {
            opacity: 0.8;
        }

        .sidebar i {
            margin-right: 0.5rem;
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h2 {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .card h3 {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .chart-container {
            height: 400px;
        }

        #table-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        #table-button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 1rem;
            }

            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h1>Analysis Dashboard</h1>
            <ul>
                <li><a href="#"><i class="fas fa-chart-bar"></i> Overview</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Students</a></li>
                <li><a href="#"><i class="fas fa-book"></i> Subjects</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h2>Student Marks Analysis</h2>
            </div>

            <!-- Dropdowns for Subject and Title -->
            <div class="card">
                <h3>Filter by Subject and Title</h3>
                <select id="subject-dropdown">
                    <option value="" disabled selected>Select Subject</option>
                </select>
                <select id="title-dropdown" disabled>
                    <option value="" disabled selected>Select Title</option>
                </select>
            </div>

            <!-- Chart Container -->
            <div id="chart-container" class="chart-container">
                <canvas id="chart"></canvas>
            </div>
        </div>

        <script>
            const dropdownData = <?php echo $dropdownDataJson; ?>;
            const chartData = <?php echo $chartDataJson; ?>;

            const subjectDropdown = document.getElementById('subject-dropdown');
            const titleDropdown = document.getElementById('title-dropdown');
            const chartContainer = document.getElementById('chart-container');
            const chartCanvas = document.getElementById('chart');

            let chart;

            // Populate subject dropdown
            const subjects = [...new Set(dropdownData.map(item => item.subject))];
            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject;
                option.textContent = subject;
                subjectDropdown.appendChild(option);
            });

            // Populate title dropdown based on subject selection
            subjectDropdown.addEventListener('change', () => {
                titleDropdown.innerHTML = '<option value="" disabled selected>Select Title</option>';
                titleDropdown.disabled = false;

                const selectedSubject = subjectDropdown.value;
                const titles = dropdownData
                    .filter(item => item.subject === selectedSubject)
                    .map(item => item.title);

                titles.forEach(title => {
                    const option = document.createElement('option');
                    option.value = title;
                    option.textContent = title;
                    titleDropdown.appendChild(option);
                });
            });

            // Display chart based on subject and title selection
            titleDropdown.addEventListener('change', () => {
                const selectedSubject = subjectDropdown.value;
                const selectedTitle = titleDropdown.value;
                const subjectTitle = `${selectedSubject} - ${selectedTitle}`;

                const chartDataForSelection = chartData[subjectTitle];
                if (chartDataForSelection) {
                    const labels = chartDataForSelection.map(item => item.stu_name);
                    const data = chartDataForSelection.map(item => item.marks);

                    if (chart) chart.destroy();

                    const ctx = chartCanvas.getContext('2d');
                    chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: `Marks (${subjectTitle})`,
                                data: data,
                                backgroundColor: labels.map(
                                    () => `hsl(${Math.random() * 360}, 70%, 75%)`
                                ),
                                borderColor: 'rgba(0, 0, 0, 0.1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (tooltipItem) {
                                            return `${tooltipItem.raw} marks`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true
                                },
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        </script>


</body>
</html>