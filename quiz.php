<?php
session_start();
require 'openAI.php';

$summary = $_SESSION['summary'];
$quiz_level = 'medium';
$questions = fetchQuestions($summary, $quiz_level);

if (is_array($questions) && !empty($questions)) {
    $_SESSION['questions'] = json_encode($questions);
    $_SESSION['correct_answers'] = array_column($questions, 'correct');
} else {
    $_SESSION['questions'] = json_encode([]);
    $_SESSION['correct_answers'] = [];
    $questions = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
        }

        .quiz-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .exit-button {
            text-decoration: none;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .quiz-title {
            text-align: center;
            flex-grow: 1;
            margin: 0;
            font-size: 18px;
        }

        .question-area {
            background-color: #e6f0ff;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .points {
            color: #28a745;
            font-size: 14px;
        }

        .multiple-choice {
            color: #6c757d;
            font-size: 14px;
            font-weight: 600;
        }

        .question {
            font-weight: 700;
            border: 2px solid #66B3FF;
            background: white;
            padding: 30px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .options {
        display: flex;
        flex-direction: column;
        gap: 10px;
        
        }


        .option-label {
            display: block;
            font-weight: 700;
            padding: 12px 15px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            color: #212529;
            border-bottom: 3px solid #C3C3C3; /* Bottom border */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Adds shadow */
        }

        .option-label:hover {
            background: #f8f9fa;
        }

        input[type="radio"] {
            display: none;
        }

        input[type="radio"]:checked + .option-label {
            background: #f8f9fa;
            border-color: #0d47a1;
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 0 10px;
        }

        .nav-button {
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .nav-button.prev {
            background: #0d47a1;
            color: white;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-button.submit {
            background: #0d47a1;
            color: white;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .nav-button.next {
            background: #0d47a1;
            color: white;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-button:disabled {
            background: #C3C3C3;
            color: black;
            display: flex;
            align-items: center;
            gap: 5px;
            opacity: 0.3;
            cursor: not-allowed;
        }

        .question-counter {
            color: #6c757d;
            font-size: 14px;
        }

        .mascot {
            position: absolute;
            width: 120px;
            height: auto;
        }

        .mascot-left {
            left: -140px;
            top: 20px;
        }

        .mascot-right {
            right: -140px;
            bottom: 20px;
        }

        .progress-bar {
            width: 100%;
            background-color: #C2DCFF;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .progress {
            height: 20px;
            background-color: #66B3FF;
            width: 0;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <form action="score.php" method="post">
        <div class="quiz-container">
            <div class="header"></div>
                <!-- <a href="exit.php" class="exit-button">
                    ← Exit Quiz
                </a> -->
                <h1 class="quiz-title">First Quiz</h1>
            </div>

            <div class="progress-bar">
                <div class="progress" id="progress"></div>
            </div>
            
            <?php
            if (!empty($questions)) {
                foreach ($questions as $index => $question) {
                    echo '<div class="question-area" id="question_' . $index . '" style="display: ' . ($index === 0 ? 'block' : 'none') . ';">';
                    echo '<div class="question-header">';
                    echo '<div class="points">+1 points</div>';
                    echo '<div class="multiple-choice">Multiple Choice</div>';
                    echo '</div>';
                    echo '<div class="question">' . $question['mcq'] . '</div>';
                    echo '<div class="options">';
                    foreach ($question['options'] as $key => $option) {
                        echo '<div class="option">';
                        echo '<input type="radio" id="q' . $index . 'o' . $key . '" name="question_' . $index . '" value="' . $key . '">';
                        echo '<label class="option-label" for="q' . $index . 'o' . $key . '">' . $option . '</label>';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No questions available. Please try again later.</p>';
            }
            ?>

            <div class="navigation">
                <button type="button" class="nav-button prev" onclick="previousQuestion()" disabled>Prev</button>
                <span class="question-counter">1 / <?php echo count($questions); ?></span>
                <button type="button" class="nav-button next" onclick="nextQuestion()">Next →</button>
                <button type="submit" class="nav-button submit" style="display: none;">Submit</button>
            </div>

            <img src="mascot-left.png" alt="" class="mascot mascot-left">
            <img src="mascot-right.png" alt="" class="mascot mascot-right">
        </div>
    </form>

    <script>
        let currentQuestion = 0;
        const totalQuestions = <?php echo count($questions); ?>;

        function updateProgressBar() {
            const progress = (currentQuestion + 1) / totalQuestions * 100;
            document.getElementById('progress').style.width = progress + '%';
        }

        function showQuestion(index) {
            document.querySelector(`#question_${currentQuestion}`).style.display = 'none';
            document.querySelector(`#question_${index}`).style.display = 'block';
            currentQuestion = index;
            document.querySelector('.question-counter').textContent = `${currentQuestion + 1} / ${totalQuestions}`;
            document.querySelector('.nav-button.prev').disabled = currentQuestion === 0;
            document.querySelector('.nav-button.next').style.display = currentQuestion === totalQuestions - 1 ? 'none' : 'inline-block';
            document.querySelector('.nav-button.submit').style.display = currentQuestion === totalQuestions - 1 ? 'inline-block' : 'none';
            updateProgressBar();
        }

        function previousQuestion() {
            if (currentQuestion > 0) {
                showQuestion(currentQuestion - 1);
            }
        }

        function nextQuestion() {
            if (currentQuestion < totalQuestions - 1) {
                showQuestion(currentQuestion + 1);
            }
        }

        updateProgressBar();
    </script>
</body>
</html>