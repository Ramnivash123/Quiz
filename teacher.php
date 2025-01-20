<?php
session_start();

// Include Composer's autoloader
require 'vendor/autoload.php';

include 'db.php';

$teacher_name = $_SESSION['teacher_name'] ?? '';

function saveQuestionsToDatabase($questions, $exam_title, $subject, $timer, $teacher_name, $conn) {
    try {
        // Prepare statement for inserting exam title into the exam table
        $stmt_exam = $conn->prepare("INSERT INTO exam (title, subject, timer, teacher) VALUES (:title, :subject, :timer, :teacher)");
        $stmt_exam->execute([
            ':title' => $exam_title,
            ':subject' => $subject,
            ':timer' => $timer,
            ':teacher' => $teacher_name
        ]);

        // Get the inserted exam_id
        $exam_id = $conn->lastInsertId();

        // Prepare statement for inserting assignments
        $stmt_assign = $conn->prepare("
            INSERT INTO assignments (qn, question, opt1, opt2, opt3, opt4, answer, title) 
            VALUES (:qn, :question, :opt1, :opt2, :opt3, :opt4, :answer, :title)
        ");

        // Execute the statement for each question
        foreach ($questions as $question) {
            $stmt_assign->execute([
                ':qn' => $question['qn'],
                ':question' => $question['question'],
                ':opt1' => $question['opt1'],
                ':opt2' => $question['opt2'],
                ':opt3' => $question['opt3'],
                ':opt4' => $question['opt4'],
                ':answer' => $question['opt' . $question['answer']], // Store selected option text
                ':title' => $exam_title
            ]);            
        }

    } catch (PDOException $e) {
        // Handle exceptions
        die("Error: " . $e->getMessage());
    }
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save_exam'])) {
        // Handle saving the exam to the database
        if (isset($_POST['exam_title'], $_POST['subject'], $_POST['timer'])) {
            $exam_title = $_POST['exam_title'];
            $subject = $_POST['subject'];
            $timer = (int)$_POST['timer'];
            $questions = [];

            // Collect questions from the form
            foreach ($_POST['question'] as $index => $question_text) {
                $questions[] = [
                    'qn' => $index + 1,
                    'question' => $question_text,
                    'opt1' => $_POST['opt1'][$index],
                    'opt2' => $_POST['opt2'][$index],
                    'opt3' => $_POST['opt3'][$index],
                    'opt4' => $_POST['opt4'][$index],
                    'answer' => $_POST['answer'][$index] // Numeric value from form
                ];
            }            

            // Save to database
            saveQuestionsToDatabase($questions, $exam_title, $subject, $timer, $teacher_name, $conn);

            // Redirect to teacher.html page
            header("Location: teacher.html");
            exit();
        } else {
            $error = "Missing exam details or questions.";
        }
    }
}

// Close database connection at the end
$conn = null; // Optional: This will close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Create Exam Manually</title>
    <style>
        /* [Include your existing CSS styles here] */

        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --text-color: #333;
            --bg-color: #f3f4f6;
            --white: #ffffff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
            color: var(--text-color);
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="number"]:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        input[type="submit"], button {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: background-color 0.3s ease;
            display: block;
            margin: 30px auto 0;
        }

        input[type="submit"]:hover, button:hover {
            background-color: var(--primary-dark);
        }

        .question-block {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary-color);
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Exam Manually</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="exam_title">Exam Title:</label>
                <input type="text" id="exam_title" name="exam_title" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="timer">Timer (minutes):</label>
                <input type="number" id="timer" name="timer" required>
            </div>

            <div id="questions-container">
                <div class="question-block">
                    <label>Question 1:</label>
                    <input type="text" name="question[]" placeholder="Enter your question here" required>
                    <label>Options:</label>
                    <input type="text" name="opt1[]" placeholder="Option A" required>
                    <input type="text" name="opt2[]" placeholder="Option B" required>
                    <input type="text" name="opt3[]" placeholder="Option C" required>
                    <input type="text" name="opt4[]" placeholder="Option D" required>
                    <label>Correct Answer:</label>
                    <select name="answer[]" required>
                        <option value="">Select Correct Answer</option>
                        <option value="1">Option A</option>
                        <option value="2">Option B</option>
                        <option value="3">Option C</option>
                        <option value="4">Option D</option>
                    </select>
                </div>
            </div>

            <button type="button" id="add-question">Add Another Question</button>
            <input type="submit" name="save_exam" value="Save Exam ">
        </form>
    </div>

    <script>
        document.getElementById('add-question').addEventListener('click', function() {
            const container = document.getElementById('questions-container');
            const questionCount = container.children.length + 1;

            const questionBlock = document.createElement('div');
            questionBlock.className = 'question-block';
            questionBlock.innerHTML = `
                <label>Question ${questionCount}:</label>
                <input type="text" name="question[]" placeholder="Enter your question here" required>
                <label>Options:</label>
                <input type="text" name="opt1[]" placeholder="Option A" required>
                <input type="text" name="opt2[]" placeholder="Option B" required>
                <input type="text" name="opt3[]" placeholder="Option C" required>
                <input type="text" name="opt4[]" placeholder="Option D" required>
                <label>Correct Answer:</label>
                <select name="answer[]" required>
                    <option value="">Select Correct Answer</option>
                    <option value="1">Option A</option>
                    <option value="2">Option B</option>
                    <option value="3">Option C</option>
                    <option value="4">Option D</option>
                </select>
            `;
            container.appendChild(questionBlock);
        });
    </script>
</body>
</html>

<?php
// Close database connection at the end
$conn = null; // Optional: This will close the connection
?>