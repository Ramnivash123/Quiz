<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Test</title>
    <script src="function.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .timer {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .assignment-details {
            background-color: #fff;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        h2 {
            color: #007bff;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        p {
            margin-bottom: 15px;
        }

        .options {
            margin-top: 20px;
        }

        .option-label {
            display: block;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .option-label:hover {
            background-color: #e9ecef;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
        }

        #progressContainer {
            width: 100%;
            background-color: #f3f3f3;
            border: 1px solid #ccc;
            margin: 10px 0;
            height: 20px;
            position: relative;
        }

        #progressBar {
            height: 100%;
            background-color: #4caf50;
            width: 0%;
        }

        #progressText {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 14px;
            color: #fff;
        }

        .motivational-comment {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s ease, fadeOut 0.5s ease 3s;
            z-index: 1000;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

    </style>

</head>
<body>
    <div class="top-bar">
        <button type="button" class="btn btn-secondary" onclick="goBack()">Back</button>
        <div class="timer">
            Time Remaining: <span id="timer"></span>
            <button type="button" class="btn btn-primary" onclick="restartTimer()">Restart</button>
        </div>
    </div>

    <form id="examForm" action="submit.php?title=<?php echo urlencode($_GET['title']); ?>" method="post">
<?php
include 'db.php';

// Check if title parameter is set in the URL
if (isset($_GET['title'])) {
    // Get the title from the URL
    $title = $_GET['title'];

    // Fetch assignment details and timer from the database
    $sql = "SELECT a.*, e.timer, e.subject FROM assignments a JOIN exam e ON a.title = e.title WHERE a.title = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $title, PDO::PARAM_STR); // Bind the parameter
    $stmt->execute(); // Execute the statement

    // Fetch the results
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) { // Check if there are results
        // Output data of the assignment
        foreach ($result as $row) {
            echo "<div class='assignment-details' data-qn='" . $row['qn'] . "'>"; // Store qn as a data attribute
            echo "<h2>Question " . $row['qn'] . ": Assignment Details</h2>"; // Display qn number
            echo "<p><strong>Question:</strong> " . $row["question"] . "</p>";
            // Display options as radio buttons with numeric values
            echo "<p><strong>Options:</strong></p>";
            echo "<p><input type='radio' name='option[" . $row['id'] . "]' value='1' id='opt1_" . $row['id'] . "'><label class='option-label' for='opt1_" . $row['id'] . "'>" . $row["opt1"] . "</label></p>";
            echo "<p><input type='radio' name='option[" . $row['id'] . "]' value='2' id='opt2_" . $row['id'] . "'><label class='option-label' for='opt2_" . $row['id'] . "'>" . $row["opt2"] . "</label></p>";
            echo "<p><input type='radio' name='option[" . $row['id'] . "]' value='3' id='opt3_" . $row['id'] . "'><label class='option-label' for='opt3_" . $row['id'] . "'>" . $row["opt3"] . "</label></p>";
            echo "<p><input type='radio' name='option[" . $row['id'] . "]' value='4' id='opt4_" . $row['id'] . "'><label class='option-label' for='opt4_" . $row['id'] . "'>" . $row["opt4"] . "</label></p>";
            echo "</div>";

            // Set the timer duration for JavaScript
            echo "<script>timerDuration = " . ($row["timer"] * 60) . ";</script>"; // Timer in seconds

            // Store the subject in a hidden input field
            echo "<input type='hidden' id='subject' name='subject' value='" . $row['subject'] . "'>";
            echo "<input type='hidden' id='assignment_title' name='assignment_title' value='" . $row['title'] . "'>";
        }
    } else {
        echo "No assignment details found for the given title";
    }
} else {
    echo "No title specified";
}

// No need to explicitly close the connection
?>

<div id="progressContainer">
    <div id="progressBar"></div>
    <span id="progressText"></span>
</div>

<div class="button-group">
    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="prevQuestion()">Previous</button>
    <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextQuestion()">Next</button>
    <button type="button" class="btn btn-primary" id="submitBtn" onclick="submitForm()" style="display: none;">Submit</button>
</div>
<input type="hidden" id="date" name="date">
<input type="hidden" id="start_time" name="start_time">
<input type="hidden" id="end_time" name="end_time">
<input type="hidden" id="quit_time" name="quit_time">
<input type="hidden" id="question_number" name="question_number" value="1">
<input type="hidden" id="qn" name="qn" value="">
</form>

<!--Quitting Back button -->
<div id="quitConfirmationModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>🤷‍♂️ Why are you leaving this test?</h2>
            <button class="btn btn-secondary" onclick="submitQuitReason('Boring')">Boring</button>
            <button class="btn btn-secondary" onclick="submitQuitReason('More Questions')">More Questions</button>
            <button class="btn btn-secondary" onclick="submitQuitReason('Difficult')">Difficult</button>
            <button class="btn btn-secondary" onclick="submitQuitReason('Other')">Other</button>
            <div id="otherReasonContainer" style="display: none; margin-top: 10px;">
                <input type="text" id="otherReason" placeholder="Enter your reason" style="width: 100%; padding: 5px;">
                <button class="btn btn-primary" onclick="submitQuitReason($('#otherReason').val())" style="margin-top: 10px;">Submit</button>
            </div>
        </div>
    </div>
</body>
</html>
