<?php
session_start();

// Only accept POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: main.php");
    exit;
}

$kb_id = $_POST["kb_id"];
$username = trim($_POST["username"]);
$feedback = trim($_POST["feedback"]);

// Basic validation
if ($kb_id == "" || $feedback == "") {
    header("Location: main.php?id=$kb_id&msg=empty");
    exit;
}

$folder = "feedback/" . $kb_id . "/";

// Create directory if not exists
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

// Generate filename
$filename = $folder . "feedback_" . date("Ymd_His") . ".txt";

// Format text
$content = "User: " . ($username ?: "Anonymous") . "\n";
$content .= "Date: " . date("Y-m-d H:i:s") . "\n\n";
$content .= $feedback;

// Save file
file_put_contents($filename, $content);

header("Location: main.php?id=$kb_id&msg=saved");
exit;
?>
