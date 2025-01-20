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

        /* Progress Container Styles */
        #progressContainer {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 15px;
            border: 1px solid #ddd;
            margin: 20px 0;
            height: 30px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Progress Bar Styles */
        #progressBar {
            height: 100%;
            background: linear-gradient(90deg, #4caf50, #81c784);
            width: 0%;
            border-radius: 15px 0 0 15px; /* Rounded corners for the left side */
            transition: width 0.4s ease;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        /* Progress Text Styles */
        #progressText {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 16px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            color: #ffffff;
            z-index: 1;
        }

        .motivational-comment {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(50px); /* Start slightly below its final position */
            background-color: rgba(0, 123, 255, 0.9); /* Slightly darker for better readability */
            color: white;
            padding: 15px 25px; /* Adjust padding for better aesthetics */
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            opacity: 0; /* Initially hidden */
            transition: all 0.5s ease; /* Smooth transition for all properties */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add shadow for a pop-out effect */
            z-index: 1000; /* Ensure it appears above other elements */
        }

        .motivational-comment.show {
            transform: translateX(-50%) translateY(0); /* Slide into position */
            opacity: 1; /* Fade in */
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
        
<script>
    let timerDuration; // Duration in seconds
    let remainingTime;
    let timerInterval;
    let currentQuestionIndex = 0; // Track current question

    // Start Timer Function
    function startTimer(duration) {
        let timer = duration, minutes, seconds;
        const display = document.querySelector('#timer');
        timerInterval = setInterval(() => {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;
            remainingTime = timer;

            if (--timer < 0) {
                clearInterval(timerInterval);
                document.getElementById("examForm").submit();
            }
        }, 1000);
    }

    // Window Onload Function
    window.onload = function () {
        const startTime = new Date();
        const endTime = new Date(startTime.getTime() + timerDuration * 1000);

        document.getElementById('start_time').value = startTime.toTimeString().split(' ')[0];
        document.getElementById('date').value = startTime.toISOString().split('T')[0];

        // Check if there is a saved remaining time in local storage
        const savedRemainingTime = localStorage.getItem('remainingTime');
        if (savedRemainingTime) {
            remainingTime = parseInt(savedRemainingTime, 10);
        } else {
            remainingTime = timerDuration;
        }

        startTimer(remainingTime);
        showQuestion(currentQuestionIndex); // Show the first question initially
    };

    // Show Question Function
    function showQuestion(index) {
        const questions = document.querySelectorAll('.assignment-details');
        const totalQuestions = questions.length;

        questions.forEach((question, i) => {
            question.style.display = (i === index) ? 'block' : 'none';
            if (i === index) {
                const qnValue = question.getAttribute('data-qn'); 
                document.getElementById('qn').value = qnValue;
            }
        });

        const progressPercentage = ((index + 1) / totalQuestions) * 100;
        document.getElementById('progressBar').style.width = progressPercentage + '%';
        document.getElementById('progressText').textContent = `Question ${index + 1} of ${totalQuestions}`;

        document.getElementById('prevBtn').style.display = (index === 0) ? 'none' : 'inline-block';
        document.getElementById('nextBtn').style.display = (index === questions.length - 1) ? 'none' : 'inline-block';
        document.getElementById('submitBtn').style.display = (index === questions.length - 1) ? 'inline-block' : 'none';

        // Show random motivational quotes after every 3 questions for 2 seconds
        if ((index + 1) % 3 === 0) {
            const quotes = ["Good job!", "Keep going!", "You're doing great!", "Superb!"];
            const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];

            const motivationElement = document.getElementById('motivation');
            motivationElement.textContent = randomQuote; // Display the random quote
            motivationElement.classList.add('show'); // Add the 'show' class to trigger animation

            // Remove the 'show' class after 2 seconds
            setTimeout(() => {
                motivationElement.classList.remove('show');
            }, 2000); // 2000ms = 2 seconds
        }


        
    }


    // Function to set the qn value when an option is selected
    function setQnValue(qn) {
        document.getElementById('qn').value = qn;
    }

    // Move to next question
    function nextQuestion() {
        currentQuestionIndex++;
        showQuestion(currentQuestionIndex);
    }

    // Move to previous question
    function prevQuestion() {
        currentQuestionIndex--;
        showQuestion(currentQuestionIndex);
    }

    // Submit the form
    function submitForm() {
        const endTime = new Date();
        document.getElementById('end_time').value = endTime.toTimeString().split(' ')[0];
        document.getElementById("examForm").submit();
    }

    
    // Restart Timer
    function restartTimer() {
        clearInterval(timerInterval); // Clear any existing timer
        document.getElementById('examForm').reset(); // Reset the form to clear selected options
        localStorage.removeItem('remainingTime'); // Remove the saved time from local storage
        remainingTime = timerDuration; // Reset remaining time to the original duration
        startTimer(remainingTime); // Start the timer again
        showQuestion(0); // Reset to the first question
        currentQuestionIndex = 0; // Reset the question index
    }

    function goBack() {
        document.getElementById('reasonModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('reasonModal').style.display = 'none';
    }

    window.onclick = function(event) {
    const modal = document.getElementById('reasonModal');
    if (event.target == modal) {
        closeModal();
    }
}

</script>


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

<div id="motivation" class="motivational-comment"></div>


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

<div id="reasonModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Please select a reason for going back:</h2>
        <form id="reasonForm" action="submit_reason.php" method="post">
            <input type="radio" name="reason" value="boring" id="reason1">
            <label for="reason1">Boring</label><br>
            <input type="radio" name="reason" value="more_questions" id="reason2">
            <label for="reason2">More Questions</label><br>
            <input type="radio" name="reason" value="difficult" id="reason3">
            <label for="reason3">Difficult</label><br>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

</body>
</html>