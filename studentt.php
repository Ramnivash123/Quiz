<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments Dashboard</title>
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

        .btn-container {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .btn-custom {
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
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
        <div class="d-flex align-items-center justify-content-between">
            <div class="welcome-message">
                <?php
                session_start();
                if (isset($_SESSION['student_name'])) {
                    $student_name = htmlspecialchars($_SESSION['student_name']);
                    echo "Welcome, $student_name!";
                } else {
                    header("Location: signin.php"); // Redirect if the session is not set
                    exit();
                }
                ?>
            </div>
            <a href="index.html" class="logout-btn">Logout</a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h1 class="display-4">Assignments Dashboard</h1>
        <p class="lead">Access your assignments and track your scores easily.</p>
        <div class="btn-container">
            <a href="student.php" class="btn btn-primary btn-lg btn-custom shadow">View Assignments</a>
            <a href="marks.php" class="btn btn-success btn-lg btn-custom shadow">View Scores</a>
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
