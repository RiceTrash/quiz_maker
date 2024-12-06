<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['OPENAI_API_KEY'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>OpenAI Prompt</title>
    <style>
        .form-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .form-group label {
            margin-bottom: 10px;
        }
        .form-group textarea {
            width: 100%;
            height: 300px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <form action="output.php" method="post">
        <div class="form-group">
            <label for="prompt">Enter your prompt:</label>
            <textarea id="prompt" name="prompt" required></textarea>
            <input type="submit" value="Submit">
        </div>
    </form>
</body>
</html>
