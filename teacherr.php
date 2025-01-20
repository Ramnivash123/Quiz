<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .header {
            background-color: #343a40;
            color: #ffffff;
            padding: 15px 20px;
            position: relative;
        }

        .welcome-message {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .logout-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 16px;
            color: #ffffff;
            background: none;
            border: 2px solid #ffffff;
            padding: 5px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #ffffff;
            color: #343a40;
            text-decoration: none;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }

        .dashboard-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 8px;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card-body a {
            text-decoration: none;
        }

        .footer {
            background-color: #343a40;
            color: #ffffff;
            text-align: center;
            padding: 10px 20px;
            margin-top: auto;
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <?php
        session_start();
        if (!isset($_SESSION['teacher_name'])) {
            header("Location: signin.php");
            exit();
        }
        $teacher_name = htmlspecialchars($_SESSION['teacher_name']);
        ?>
        <div class="d-flex align-items-center justify-content-between">
            <div class="welcome-message">
                Welcome, <?php echo $teacher_name; ?>!
            </div>
            <a href="index.html" class="logout-btn">Logout</a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h1 class="display-4 text-primary mb-4">Teacher Dashboard</h1>
        <p class="lead text-muted">Manage your classroom activities and monitor student progress</p>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 dashboard-card shadow">
                    <div class="card-body text-center p-4">
                        <a href="teacher.php" class="text-decoration-none">
                            <div class="display-5 text-primary mb-3">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <h5 class="card-title text-dark">Create</h5>
                            <p class="card-text text-muted">Create new assignments and tests</p>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 dashboard-card shadow">
                    <div class="card-body text-center p-4">
                        <a href="view.php" class="text-decoration-none">
                            <div class="display-5 text-primary mb-3">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h5 class="card-title text-dark">View</h5>
                            <p class="card-text text-muted">Review student submissions</p>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 dashboard-card shadow">
                    <div class="card-body text-center p-4">
                        <a href="leader.php" class="text-decoration-none">
                            <div class="display-5 text-primary mb-3">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h5 class="card-title text-dark">Leaderboard</h5>
                            <p class="card-text text-muted">Track student performance</p>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 dashboard-card shadow">
                    <div class="card-body text-center p-4">
                        <a href="analysis3.php" class="text-decoration-none">
                            <div class="display-5 text-primary mb-3">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h5 class="card-title text-dark">Feedback Analysis</h5>
                            <p class="card-text text-muted">Review student feedback</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Your School Name. All Rights Reserved. |
            <a href="contact.html">Contact Us</a>
        </p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
