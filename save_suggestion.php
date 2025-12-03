<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST["suggestion_type"];
    $article = $_POST["article_id"];
    $desc = $_POST["description"];

    $folder = "suggestions/";
    if (!is_dir($folder)) { mkdir($folder, 0777, true); }

    $file = $folder . time() . ".txt";
    file_put_contents($file,
        "Type: $type\nArticle: $article\nDescription:\n$desc"
    );
}

header("Location: main.php?msg=saved");
exit;
?>
