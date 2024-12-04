<?php

require __DIR__ . '/vendor/autoload.php';
require 'openAI.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prompt = $_POST['prompt'];
    $raw_response = getOpenAIResponse($prompt);
    $summary_response = getOpenAISummary($raw_response);
    
    // Store summary in session
    session_start();
    $_SESSION['summary'] = $summary_response;
    $_SESSION['raw_response'] = $raw_response;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>OpenAI Response</title>
</head>
<body>
    <h1>Raw Response:</h1>
    <p><?php echo $_SESSION['raw_response']; ?></p>
    <h1>Summary:</h1>
    <p><?php echo $_SESSION['summary']; ?></p>
    <form action="quiz.php" method="post">
        <input type="submit" value="Generate Quiz">
    </form>
</body>
</html>
