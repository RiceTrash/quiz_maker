<?php
session_start();
$questions = json_decode($_SESSION['questions'], true);
$correctAnswers = $_SESSION['correct_answers'];
$answers = $_POST;

// Calculate score
$score = 0;
$mistakes = 0;
foreach ($questions as $index => $question) {
    if (isset($answers["question_$index"]) && $answers["question_$index"] == $correctAnswers[$index]) {
        $score++;
    } else {
        $mistakes++;
    }
}

$totalQuestions = count($questions);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .exit-button {
            text-decoration: none;
            color: #6c757d;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .title {
            text-align: center;
            flex-grow: 1;
            font-size: 20px;
            margin: 0;
        }

        .results-card {
            background-color: #e6f0ff;
            border-radius: 8px;
            padding: 20px 20px 20px 40px;
            margin-bottom: 20px;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stats {
            display: flex;
            gap: 10px;
        }

        .stat-pill {
            padding: 6px 12px;
            border-radius: 20px;
            color: white;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .correct {
            background-color: #28a745;
        }

        .incorrect {
            background-color: #dc3545;
        }

        .correct-text {
            color: #28a745;
           
            text-align: right;
        }

        .incorrect-text {
            color: #dc3545;
            
            text-align: right;
        }

        .hide-results {
            padding-left: 630px;
            padding-top: 10px;
            color: #0d47a1;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .questions-section {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
        }

        .filter-dropdown {
            margin-bottom: 20px;
        }

        .question-card {
            border-bottom: 1px solid #dee2e6;
            padding: 20px 0;
        }

        .question-text {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .status.correct {
            color: #28a745;
        }

        .status.incorrect {
            color: #dc3545;
        }

        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .option {
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            text-align: center;
            background-color: white;
        }

        .option.correct {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .option.incorrect {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .mascot {
            position: fixed;
            right: 20px;
            bottom: 20px;
            width: 120px;
            height: auto;
        }
        .total-questions {
            font-size: 20px;
            font-weight: 700;
        }
        .quiz-results {
            font-size: 20px;
            font-weight: 700;
        }
        .question{
            width: 650px;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- <a href="exit.php" class="exit-button">← Exit Quiz</a> -->
        <h1 class="title">Quiz Completed!</h1>
    </div>

    <div class="results-card">
        <div class="results-header">
            <div class="quiz-results">Quiz Results</div>
            <div class="total-questions">Total Questions: <?php echo $totalQuestions; ?></div>
        </div>
        
        <div class="results-footer">
            <div class="stats">
                <div class="stat-pill correct">
                    ✓ Correct <?php echo $score; ?>
                </div>
                <div class="stat-pill incorrect">
                    ✕ Mistakes <?php echo $mistakes; ?>
                </div>
                </div>
            </div>
            <a href="#" class="hide-results">Hide Results →</a>
        </div>

    <div class="questions-section">
        <select class="filter-dropdown">
            <option>All Questions</option>
            <option>Correct Only</option>
            <option>Incorrect Only</option>
        </select>

        <?php foreach ($questions as $index => $question): 
            $isCorrect = isset($answers["question_$index"]) && $answers["question_$index"] == $correctAnswers[$index];
            $userAnswer = isset($answers["question_$index"]) ? $answers["question_$index"] : null;
        ?>
            <div class="question-card">
                <div class="question-text">
                    <div class="question"><?php echo $question['mcq']; ?></div>
                    <div class="status <?php echo $isCorrect ? 'correct-text' : 'incorrect-text'; ?>">
                        <?php echo $isCorrect ? '✓ Correct' : '✕ Incorrect'; ?>
                    </div>
                </div>
                <div class="options-grid">
                    <?php foreach ($question['options'] as $key => $option): 
                        $class = '';
                        if ($key == $correctAnswers[$index]) {
                            $class = 'correct';
                        } elseif ($key == $userAnswer && !$isCorrect) {
                            $class = 'incorrect';
                        }
                    ?>
                        <div class="option <?php echo $class; ?>">
                            <?php echo $option; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- <img src="mascot.png" alt="" class="mascot"> -->
</body>
</html>