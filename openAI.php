<?php

require __DIR__ . '/vendor/autoload.php'; // remove this line if you use a PHP Framework.

use Orhanerday\OpenAi\OpenAi;

// Initialize OpenAI with your API key
$open_ai_key = getenv('OPENAI_API_KEY');
$open_ai = new OpenAi('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');

// Function to get OpenAI response
function getOpenAIResponse($prompt) {
    global $open_ai;
    $chat = $open_ai->chat([
        'model' => 'gpt-4o-mini',
        'messages' => [
            [
                "role" => "system",
                "content" => "You are a helpful assistant."
            ],
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        'temperature' => 1.0,
        'max_tokens' => 4000,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
    ]);

    $response = json_decode($chat);
    return $response->choices[0]->message->content;
}

// Function to get OpenAI summary
function getOpenAISummary($text) {
    global $open_ai;
    $chat = $open_ai->chat([
        'model' => 'gpt-4o-mini',
        'messages' => [
            [
                "role" => "system",
                "content" => "You are a helpful assistant."
            ],
            [
                "role" => "user",
                "content" => "Summarize the following text and organize it well: $text"
            ]
        ],
        'temperature' => 1.0,
        'max_tokens' => 4000,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
    ]);

    $response = json_decode($chat);
    return nl2br($response->choices[0]->message->content);
}

// Function to generate multiple choice questions
function getOpenAIQuestions($summary) {
    global $open_ai;
    $chat = $open_ai->chat([
        'model' => 'gpt-4o-mini',
        'messages' => [
            [
                "role" => "system",
                "content" => "You are a helpful assistant."
            ],
            [
                "role" => "user",
                "content" => "Create 2 multiple choice questions from the following summary. Provide the correct answer for each question. Also organize it well: $summary"
            ]
        ],
        'temperature' => 1.0,
        'max_tokens' => 4000,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
    ]);

    $response = json_decode($chat);
    if (isset($response->choices[0]->message->content)) {
        $questionsAndAnswers = $response->choices[0]->message->content;
        // Extract questions and correct answers
        preg_match_all('/(.*\n[A-D]\).*\n[A-D]\).*\n[A-D]\).*\n[A-D]\).*\nCorrect Answer: (.*))/U', $questionsAndAnswers, $matches, PREG_SET_ORDER);
        $questions = [];
        $correctAnswers = [];
        foreach ($matches as $match) {
            $questions[] = $match[1];
            $correctAnswers[] = trim($match[2]);
        }
        $_SESSION['correct_answers'] = $correctAnswers;
        return implode("\n", $questions);
    } else {
        return "Error: Unable to generate questions from OpenAI.";
    }
}

// Function to fetch questions from OpenAI
function fetchQuestions($text_content, $quiz_level) {
    global $open_ai;
    $response_json = json_encode([
        "mcqs" => [
            [
                "mcq" => "multiple choice question",
                "options" => [
                    "a" => "choice here",
                    "b" => "choice here",
                    "c" => "choice here",
                    "d" => "choice here",
                ],
                "correct" => "correct choice option",
            ],
            [
                "mcq" => "multiple choice question",
                "options" => [
                    "a" => "choice here",
                    "b" => "choice here",
                    "c" => "choice here",
                    "d" => "choice here",
                ],
                "correct" => "correct choice option",
            ],
            [
                "mcq" => "multiple choice question1",
                "options" => [
                    "a" => "choice here1",
                    "b" => "choice here2",
                    "c" => "choice here3",
                    "d" => "choice here4",
                ],
                "correct" => "correct choice option",
            ]
        ]
    ]);

    $prompt_template = "
    Text: $text_content
    You are an expert in generating MCQ type quiz on the basis of provided content.
    Given the above text, create a quiz of 3 multiple choice questions keeping difficulty level as $quiz_level.
    Make sure the questions are not repeated and check all the questions to be conforming the text as well.
    Make sure to format your response like RESPONSE_JSON below and use it as a guide.
    Ensure to make an array of 3 MCQs referring the following response json.
    Here is the RESPONSE_JSON:
    $response_json
    ";

    $chat = $open_ai->chat([
        'model' => 'gpt-4o-mini',
        'messages' => [
            [
                "role" => "user",
                "content" => $prompt_template
            ]
        ],
        'temperature' => 0.3,
        'max_tokens' => 1000,
        'top_p' => 1,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
    ]);

    $response = json_decode($chat);
    $extracted_response = $response->choices[0]->message->content;

    $mcqs = json_decode($extracted_response, true)['mcqs'] ?? null;

    if (is_array($mcqs)) {
        return $mcqs;
    } else {
        return [];
    }
}

?>