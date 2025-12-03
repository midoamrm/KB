<?php
session_start();

// Protect page
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit;
}

// Dummy KB data
$kb = [
    1 => ["title" => "How to reset password", "details" => "To reset your password, go to Settings and follow the instructions."],
    2 => ["title" => "How to install software", "details" => "Download the installer from the portal and run it."],
    3 => ["title" => "System troubleshooting guide", "details" => "Step 1: Check cables.\nStep 2: Restart system.\nStep 3: Contact IT if needed."],
];

// Get selected KB article
$selectedId = $_GET["id"] ?? null;
$selectedArticle = $selectedId && isset($kb[$selectedId]) ? $kb[$selectedId] : null;

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["attachment"])) {
    $id = $_POST["article_id"];

    $targetPath = "attachments/" . $id . "/";

    // Create directory if not exists
    if (!is_dir($targetPath)) {
        mkdir($targetPath, 0777, true);
    }

    $fileName = basename($_FILES["attachment"]["name"]);
    $uploadFile = $targetPath . $fileName;

    if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $uploadFile)) {
        $uploadMessage = "File uploaded successfully!";
    } else {
        $uploadMessage = "Error uploading file!";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>KB Main Page</title>
    <style>
        body { font-family: Arial; margin: 0; }
        .topbar {
            background: #333;
            color: white;
            padding: 10px;
        }
        .sidebar {
            width: 250px;
            height: calc(100vh - 50px);
            border-right: 1px solid #ccc;
            float: left;
            overflow-y: auto;
            padding: 10px;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        a {
            text-decoration: none;
            display: block;
            padding: 6px 0;
            color: #000;
        }
        a:hover {
            text-decoration: underline;
        }
        .logout {
            float: right;
            color: white;
        }
		.btn {
			padding: 6px 12px;
			margin-right: 6px;
			border: 1px solid #888;
			border-radius: 5px;
			background: #f0f0f0;
			cursor: pointer;
		}
		.btn:hover {
			background: #ddd;
		}
    </style>
</head>
<body>

<script>
function showAttachmentBox() {
    document.getElementById("attachmentBox").style.display = "block";
}
</script>


<div class="topbar">
    <form method="GET" action="main.php" style="display:inline-block;">
        <input type="text" name="q" placeholder="Search KB..." required>
        <button type="submit">Search</button>
    </form>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="sidebar">
    <h3>KB Titles</h3>
    <?php foreach ($kb as $id => $article): ?>
        <a href="main.php?id=<?php echo $id; ?>"><?php echo $article["title"]; ?></a>
    <?php endforeach; ?>
</div>

<div class="content">
    <?php if ($selectedArticle): ?>

		

		<!-- Button Bar -->
		<div style="margin-bottom: 15px;">
			<button class="btn">✏️ Edit</button>
			<button class="btn">🗑️ Delete</button>
			<button class="btn" onclick="showAttachmentBox()">📎 Add Attachment</button>
			<button class="btn">💬 Add Feedback</button>
		</div>
		<h2><?php echo $selectedArticle["title"]; ?></h2>
		
		<p><?php echo nl2br($selectedArticle["details"]); ?></p>

    <!-- Upload Box -->
    <div id="attachmentBox" style="display:none; margin-top:20px; padding:10px; border:1px solid #ccc; width:350px; border-radius:6px;">
        <h3>Add Attachment</h3>

        <?php if (!empty($uploadMessage)) echo "<p style='color:green;'>$uploadMessage</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="article_id" value="<?php echo $selectedId; ?>">
            <input type="file" name="attachment" required>
            <br><br>
            <button class="btn">Upload</button>
        </form>
    </div>

    <!-- List attachments -->
<h3 style="margin-top:30px;">Attachments</h3>

<?php
$attachmentFolder = "attachments/" . $selectedId . "/";

if (is_dir($attachmentFolder)) {

    $files = array_diff(scandir($attachmentFolder), ['.', '..']);

    if (count($files) === 0) {
        echo "<p>No attachments yet.</p>";
    } else {
        echo "<div style='margin-top:10px; display:flex; flex-wrap:wrap;'>";

        foreach ($files as $file) {
            echo "
                <div style='margin-right:15px; display:flex; align-items:center;'>
                    <span style='font-size:18px; margin-right:4px;'>📎</span>
                    <a href='$attachmentFolder$file' target='_blank' style='text-decoration:none;'>
                        $file
                    </a>
                </div>
            ";
        }

        echo "</div>";
    }

} else {
    echo "<p>No attachments yet.</p>";
}
?>


    <?php else: ?>
        <h2>Welcome</h2>
        <p>Select a KB article from the left to view details.</p>
    <?php endif; ?>
</div>

</body>
</html>
