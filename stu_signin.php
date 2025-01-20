<?php
session_start();

// Include database connection
include 'db.php';

// Initialize error variable
$error = '';

// Handle login attempt
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate email input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        try {
            // Prepare PDO statement
            $stmt = $conn->prepare("SELECT id, na, pass FROM stu_signup WHERE em = :email");    
            // Bind parameters
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            // Fetch the result
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                // Verify password
                if (password_verify($password, $row['pass'])) {
                    // Successful login
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    // Store user details in session
                    $_SESSION['student_id'] = $row['id'];
                    $_SESSION['student_name'] = $row['na'];
                    
                    // Redirect to student dashboard
                    header("Location: studentt.php");
                    exit();
                } else {
                    $error = "Invalid email or password";
                }
            } else {
                $error = "Invalid email or password";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }

        h2 {
            color: #1a73e8;
            font-size: 24px;
            margin-bottom: 24px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            color: #5f6368;
            font-size: 14px;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="password"] {
            background-color: #f1f3f4;
            border: none;
            border-radius: 4px;
            color: #3c4043;
            font-size: 16px;
            padding: 12px;
            margin-bottom: 16px;
            transition: background-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            background-color: #e8f0fe;
            outline: none;
        }

        input[type="submit"] {
            background-color: #1a73e8;
            border: none;
            border-radius: 4px;
            color: #ffffff;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            padding: 12px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #1765cc;
        }

        .error {
            color: #d93025;
            font-size: 14px;
            margin-top: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Sign In</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Sign In">

            <?php
            // Display error message if exists
            if (!empty($error)) {
                echo '<div class="error">' . htmlspecialchars($error) . '</div>';
            }
            ?>
        </form>
    </div>
</body>
</html>