let timerDuration; // Duration in seconds
        let remainingTime;
        let timerInterval;
        let currentQuestionIndex = 0; // Track current question

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

        function showQuestion(index) {
			const questions = document.querySelectorAll('.assignment-details');
			questions.forEach((question, i) => {
				question.style.display = (i === index) ? 'block' : 'none';
				if (i === index) {
					const qnValue = question.getAttribute('data-qn'); // Assuming you set data-qn in the HTML
					document.getElementById('qn').value = qnValue;
				}
			});

			// Show or hide navigation buttons
			document.getElementById('prevBtn').style.display = (index === 0) ? 'none' : 'inline-block';
			document.getElementById('nextBtn').style.display = (index === questions.length - 1) ? 'none' : 'inline-block';
			document.getElementById('submitBtn').style.display = (index === questions.length - 1) ? 'inline-block' : 'none';
		}

		// Call this function to set the qn value when an option is selected
		function setQnValue(qn) {
			document.getElementById('qn').value = qn;
		}


        function nextQuestion() {
            currentQuestionIndex++;
            showQuestion(currentQuestionIndex);
        }

        function prevQuestion() {
            currentQuestionIndex--;
            showQuestion(currentQuestionIndex);
        }

        function submitForm() {
            // Capture end time when the submit button is clicked
            const endTime = new Date();
            document.getElementById('end_time').value = endTime.toTimeString().split(' ')[0];
            document.getElementById("examForm").submit();
        }
        // Modify the goBack() function
        function goBack() {
            clearInterval(timerInterval); // Pause the timer
            localStorage.setItem('remainingTime', remainingTime); // Save the remaining time in local storage
            
            // Capture quit time
            const quitTime = new Date();
            document.getElementById('quit_time').value = quitTime.toTimeString().split(' ')[0];
            
            document.getElementById('quitConfirmationModal').style.display = 'block'; // Show the quit confirmation modal
            }
            
            // New function to handle quit reason submission
            function submitQuitReason(reason) {
                const startTime = new Date("1970-01-01T" + document.getElementById('start_time').value + "Z");
                const quitTime = new Date("1970-01-01T" + document.getElementById('quit_time').value + "Z");
                const quitTiming = (quitTime - startTime) / 1000; // Quit timing in seconds

                const studentName = "<?php echo $_SESSION['student_name'] ?? ''; ?>";
                const quitReason = reason === 'Other' ? document.getElementById('otherReason').value : reason;
                const assignmentTitle = document.getElementById('assignment_title').value;
                const subject = document.getElementById('subject').value;
                const questionNumber = document.getElementById('qn').value; // Get the question number

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'submit_quit_reason.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log("Quit reason submitted:", xhr.responseText);
                        window.location.href = "student.php"; // Navigate to the student page
                    }
                };
                xhr.send(`name=${encodeURIComponent(studentName)}&reason=${encodeURIComponent(quitReason)}&timing=${quitTiming}&assignment_title=${encodeURIComponent(assignmentTitle)}&subject=${encodeURIComponent(subject)}&question_number=${encodeURIComponent(questionNumber)}`);
                document.getElementById('quitConfirmationModal').style.display = 'none'; // Hide the modal
            }



        var modal = document.getElementById('quitConfirmationModal');
        var span = document.getElementsByClassName("close")[0];

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

                function restartTimer() {
                    clearInterval(timerInterval); // Clear any existing timer
                    document.getElementById('examForm').reset(); // Reset the form to clear selected options
                    localStorage.removeItem('remainingTime'); // Remove the saved time from local storage
                    remainingTime = timerDuration; // Reset remaining time to the original duration
                    startTimer(remainingTime); // Start the timer again
                    showQuestion(0); // Reset to the first question
                    currentQuestionIndex = 0; // Reset the question index
                    }
        // Example JavaScript function to set the question number before submitting the form
        function setQuestionNumber(questionNumber) {
            document.getElementById('question_number').value = questionNumber;
        }

        // Function to set the question number
        function setQuestionNumber(questionNumber) {
            document.getElementById('question_number').value = questionNumber;
        }

        // Call this function to increment the question number as needed
        let currentQuestionNumber = 1;

        // Example of moving to the next question
        function goToNextQuestion() {
            currentQuestionNumber++;
            setQuestionNumber(currentQuestionNumber);
            // Logic to display the next question
        }

        // Example of moving to the previous question
        function goToPreviousQuestion() {
            if (currentQuestionNumber > 1) {
                currentQuestionNumber--;
                setQuestionNumber(currentQuestionNumber);
            }
            // Logic to display the previous question
        }

        function showQuestion(index) {
            const questions = document.querySelectorAll('.assignment-details');
            const totalQuestions = questions.length;

            questions.forEach((question, i) => {
                question.style.display = (i === index) ? 'block' : 'none';
                if (i === index) {
                    const qnValue = question.getAttribute('data-qn'); // Assuming you set data-qn in the HTML
                    document.getElementById('qn').value = qnValue;
                }
            });

            // Update the progress bar
            const progressPercentage = ((index + 1) / totalQuestions) * 100;
            document.getElementById('progressBar').style.width = progressPercentage + '%';
            document.getElementById('progressText').textContent = `Question ${index + 1} of ${totalQuestions}`;

            // Show or hide navigation buttons
            document.getElementById('prevBtn').style.display = (index === 0) ? 'none' : 'inline-block';
            document.getElementById('nextBtn').style.display = (index === questions.length - 1) ? 'none' : 'inline-block';
            document.getElementById('submitBtn').style.display = (index === questions.length - 1) ? 'inline-block' : 'none';
        }

        function showQuestion(index) {
            const questions = document.querySelectorAll('.assignment-details');
            const totalQuestions = questions.length;

            questions.forEach((question, i) => {
                question.style.display = (i === index) ? 'block' : 'none';
                if (i === index) {
                    const qnValue = question.getAttribute('data-qn'); // Assuming you set data-qn in the HTML
                    document.getElementById('qn').value = qnValue;
                }
            });

            // Update the progress bar
            const progressPercentage = ((index + 1) / totalQuestions) * 100;
            document.getElementById('progressBar').style.width = progressPercentage + '%';
            document.getElementById('progressText').textContent = `Question ${index + 1} of ${totalQuestions}`;

            // Show or hide navigation buttons
            document.getElementById('prevBtn').style.display = (index === 0) ? 'none' : 'inline-block';
            document.getElementById('nextBtn').style.display = (index === questions.length - 1) ? 'none' : 'inline-block';
            document.getElementById('submitBtn').style.display = (index === questions.length - 1) ? 'inline-block' : 'none';

            // Show motivational comment every 3 questions
            showMotivationalComment(index, totalQuestions);
        }

const motivationalComments = ["Good!", "Bravo!", "Well done!", "Keep it up!", "Excellent!", "You're doing great!"];

function showMotivationalComment(questionIndex, totalQuestions) {
    if ((questionIndex + 1) % 3 === 0 && questionIndex + 1 < totalQuestions) {
        const randomComment = motivationalComments[Math.floor(Math.random() * motivationalComments.length)];
        
        // Create the comment container
        const commentContainer = document.createElement('div');
        commentContainer.className = 'motivational-comment';
        commentContainer.innerHTML = `<p>${randomComment}</p>`;

        // Append the comment to the body
        document.body.appendChild(commentContainer);

        // Add animation and auto-remove after 3 seconds
        setTimeout(() => {
            commentContainer.style.opacity = '0';
            setTimeout(() => commentContainer.remove(), 500);
        }, 3000);
    }
}

