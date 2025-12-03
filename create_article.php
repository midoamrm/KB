<?php
session_start();

// Protect page
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_article"])) {
    $title = trim($_POST["title"]);
    $body = trim($_POST["body"]);
    $status = $_POST["status"];
    $author = trim($_POST["author"]);
    $approver = trim($_POST["approver"]);

    if (!isset($_SESSION["kb"])) $_SESSION["kb"] = [];
    $id = count($_SESSION["kb"]) + 1;
    $_SESSION["kb"][$id] = [
        "title" => $title,
        "details" => $body,
        "status" => $status,
        "author" => $author,
        "approver" => $approver,
        "category" => $_POST["article_category"]
    ];

    $message = "Article saved successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Article</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 40px; 
            background: #f4f6f8; 
            color: #333;
        }

        h2 { 
            color: #1d3557; 
            margin-bottom: 20px; 
        }

        /* Modern Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg,#1d3557,#457b9d);
            color: white;
            border-radius: 50%;
            font-size: 22px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            margin-bottom: 25px;
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            background: linear-gradient(135deg,#457b9d,#1d3557);
        }

        /* Card-style form */
        form {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
            color: #1d3557;
        }

        input, select {
            width: 100%; 
            padding: 12px; 
            border: 1px solid #d1d5db; 
            border-radius: 8px; 
            font-size: 15px;
            transition: all 0.2s;
        }

        input:focus, select:focus {
            border-color: #1d3557;
            box-shadow: 0 0 0 2px rgba(29,53,87,0.2);
            outline: none;
        }

        /* Toolbar buttons for editor */
        .toolbar button {
            background: #f1f5f9; 
            border: 1px solid #d1d5db; 
            padding: 6px 10px; 
            margin-right: 5px; 
            cursor: pointer; 
            border-radius: 5px; 
            font-size: 15px;
            transition: 0.2s;
        }
        .toolbar button:hover {
            background: #e2e8f0;
        }

        #editor {
            min-height: 200px;
            border: 1px solid #d1d5db;
            padding: 12px;
            border-radius: 8px;
            background: #f9fafb;
            margin-bottom: 20px;
            overflow-y: auto;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            background: #1d3557;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #457b9d;
        }

        p.message {
            color: green;
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Modern Back Button -->
    <a href="main.php" class="back-btn" title="Back">←</a>

    <h2>Create New Article</h2>

    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <form method="POST" onsubmit="submitEditor()">
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Body:</label>
        <div class="toolbar">
            <button type="button" onclick="format('bold')"><b>B</b></button>
            <button type="button" onclick="format('italic')"><i>I</i></button>
            <button type="button" onclick="format('underline')"><u>U</u></button>
            <button type="button" onclick="format('insertUnorderedList')">• Dot List</button>
            <button type="button" onclick="format('insertOrderedList')">1. Number List</button>
            <button type="button" onclick="format('createLink', prompt('Enter URL:','http://'))">Insert Link</button>
        </div>
        <div id="editor" contenteditable="true"></div>
        <input type="hidden" name="body" id="body">

        <div class="form-grid">
            <div>
                <label>Status:</label>
                <select name="status" required>
                    <option value="draft">Draft</option>
                    <option value="reviewed">Reviewed</option>
                </select>
            </div>

            <div>
                <label>Article Type:</label>
                <select name="article_type" required>
                    <option value="How-To">How-To</option>
                    <option value="Troubleshooting">Troubleshooting</option>
                    <option value="FAQ">FAQ</option>
                    <option value="Policy">Policy</option>
                    <option value="Guide">Guide</option>
                </select>
            </div>

            <div>
                <label>Article Category:</label>
                <select name="article_category" required>
                    <option value="Account">Account</option>
                    <option value="Installation">Installation</option>
                    <option value="Troubleshooting">Troubleshooting</option>
                    <option value="General">General</option>
                </select>
            </div>

          
        </div>
          <div>
                <label>Author:</label>
                <input type="text" name="author" required>
            </div>

            <div>
                <label>Approver:</label>
                <input type="text" name="approver" required>
            </div>

        <button type="submit" name="save_article" class="btn">Create  Article</button>
    </form>

    <script>
        function format(command, value = null) {
            document.execCommand(command, false, value);
        }

        function submitEditor() {
            document.getElementById('body').value = document.getElementById('editor').innerHTML;
        }
    </script>
</body>
</html>
