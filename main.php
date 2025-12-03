<?php
session_start();
if (!isset($_SESSION["kb"])) {
    $_SESSION["kb"] = [
        1 => ["title" => "How to reset password", "details" => "To reset your password, go to Settings.", "category" => "Account"],
        2 => ["title" => "Change email address", "details" => "Go to Settings → Email and update your address.", "category" => "Account"],
        3 => ["title" => "How to install software", "details" => "Download the installer from the portal and run it.", "category" => "Installation"],
        4 => ["title" => "Install printer driver", "details" => "Download printer driver from official site and install.", "category" => "Installation"],
        5 => ["title" => "System troubleshooting guide", "details" => "Step 1: Check cables. Step 2: Restart system.", "category" => "Troubleshooting"],
    ];
}
$kb = $_SESSION["kb"];   // ALWAYS read data from session

// SMART SEARCH
$searchQuery = isset($_GET["q"]) ? trim(strtolower($_GET["q"])) : "";
$searchResults = [];
$matchesCategories = [];

if ($searchQuery !== "") {

    foreach ($kb as $id => $article) {
        $haystack = strtolower($article["title"] . " " . $article["details"] . " " . $article["category"]);

        if (strpos($haystack, $searchQuery) !== false) {

            // Mark category to expand
            $matchesCategories[$article["category"]] = true;

            // Collect search results
            $searchResults[$id] = $article;
        }
    }

    // Auto-expand matched categories
    $expandedCategories = array_keys($matchesCategories);
}


// Protect page
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit;
}

// Dummy KB data
$kb = [
    1 => ["title" => "How to reset password", "details" => "To reset your password, go to Settings.", "category" => "Account"],
    2 => ["title" => "Change email address", "details" => "Go to Settings → Email and update your address.", "category" => "Account"],
    3 => ["title" => "How to install software", "details" => "Download the installer from the portal and run it.", "category" => "Installation"],
    4 => ["title" => "Install printer driver", "details" => "Download printer driver from official site and install.", "category" => "Installation"],
    5 => ["title" => "System troubleshooting guide", "details" => "Step 1: Check cables. Step 2: Restart system.", "category" => "Troubleshooting"],
];

$kbByCategory = [];
foreach ($kb as $id => $article) {
    $kbByCategory[$article["category"]][$id] = $article;
}
$expandedCategories = [];
if (!empty($_GET['expanded'])) {
    $expandedCategories = explode(',', $_GET['expanded']);
}


// Get selected KB article
$selectedId = $_GET["id"] ?? null;
$selectedArticle = $selectedId && isset($kb[$selectedId]) ? $kb[$selectedId] : null;
$relatedArticles = [];

if ($selectedArticle) {
    $currentCategory = $selectedArticle["category"];

    foreach ($kb as $id => $item) {
        if ($id != $selectedId && $item["category"] === $currentCategory) {
            $relatedArticles[$id] = $item;
        }
    }
}

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
        /* PROFILE MENU DROPDOWN */
.profile-container {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-left: auto;
    margin-right: 20px;
    cursor: pointer;
}

.profile-circle {
    width: 45px;
    height: 45px;
    background: #fff;
    color: #1d3557;
    border-radius: 50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:20px;
    font-weight:bold;
    box-shadow:0 0 6px rgba(0,0,0,0.25);
    transition: 0.2s;
}

.profile-circle:hover {
    transform: scale(1.05);
}

.profile-name {
    margin-top: 5px;
    font-size: 13px;
    color: white;
}

/* Dropdown Box */
.profile-dropdown {
    position: absolute;
    top: 70px;
    right: 0;
    background: white;
    width: 160px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: fadeIn 0.2s ease-in-out;
}

.profile-dropdown a {
    padding: 12px;
    text-decoration: none;
    color: #1d3557;
    font-size: 14px;
    transition: 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.profile-dropdown a:hover {
    background: #e7f1ff;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; margin: 0; }
    
    .topbar a:hover, 
.topbar button:hover, 
.topbar span:hover {
    background: rgba(255,255,255,0.15);
    border-radius: 8px;
}


    /* layout container that holds left, center, right */
    .layout {
        display: flex;
        height: calc(100vh - 50px); /* leave room for topbar */
        overflow: hidden;
    }

    /* Modern Sidebar */
.sidebar {
    width: 260px;
    background: #ffffff;
    padding: 15px;
    border-right: 1px solid #e2e2e2;
    overflow-y: auto;
    font-family: Arial, sans-serif;
}

.sidebar h3 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #1d3557;
}

/* Category container */
.category {
    background: #f8f9fa;
    border: 1px solid #e3e6ea;
    border-radius: 10px;
    margin-bottom: 12px;
    padding: 10px;
    transition: 0.3s;
}

.category:hover {
    background: #eef1f5;
    cursor: pointer;
}

/* Category Header */
.category-header {
    display: flex;
    align-items: center;
    font-weight: bold;
    font-size: 15px;
    color: #1d3557;
    padding: 5px 0;
}

.category-header .icon {
    font-size: 14px;
    margin-right: 10px;
    color: #457b9d;
}

/* Article links */
.category-articles {
    margin-top: 8px;
    padding-left: 10px;
    display: none;
}

.category-articles a {
    display: block;
    padding: 6px 10px;
    margin: 4px 0;
    font-size: 14px;
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #e2e6ea;
    text-decoration: none;
    color: #333;
    transition: 0.2s;
}

.category-articles a:hover {
    background: #e7f1ff;
    border-color: #a8c8ff;
}

/* Selected Article Highlight */
.selected-link {
    background: #dceeff !important;
    border: 1px solid #74a8ff !important;
    font-weight: bold;
    color: #1d3557 !important;
}


    /* MAIN content (article + attachments) */
    .content {
        flex: 1;                /* take remaining width */
        padding: 20px;
        overflow-y: auto;
        min-width: 0;           /* important to allow flex items to shrink properly */
        text-align: left;       /* ensure text is left-aligned */
    }

    /* RIGHT feedback sidebar */
    .feedback-sidebar {
        width: 280px;
        padding: 20px;
        border-left: 1px solid #ccc;
        background: #fafafa;
        overflow-y: auto;
    }

    a {
        text-decoration: none;
        display: block;
        padding: 6px 0;
        color: #000;
    }
    a:hover { text-decoration: underline; }

    .logout { float: right; color: white; }
   .pp{
     color: #fff;
   }
    .btn {
        padding: 6px 12px;
        margin-right: 6px;
        border: 1px solid #888;
        border-radius: 5px;
       background:#1d3557;
        cursor: pointer;
    }
    .btn:hover { background: #ddd; }
	.search-box {
		width: 400px;
		padding: 5px;
		font-size: 14px;
	}
	.category-header {
		padding: 4px 0;
		cursor: pointer;
		font-weight: bold;
		display: flex;
		align-items: center;
	}

	.category-header .icon {
		display: inline-block;
		width: 20px;
	}

	.category-articles {
		padding-left: 20px;
	}

	.category-articles a {
		display: block;
		padding: 2px 0;
		text-decoration: none;
		color: #000;
	}

	.category-articles a:hover {
		text-decoration: underline;
	}
	
	.star-rating {
	  direction: rtl; /* show stars right-to-left for easier hover */
	  font-size: 24px;
	  display: inline-flex;
	}

	.star-rating input[type="radio"] {
	  display: none;
	}

	.star-rating label {
	  color: #ccc;
	  cursor: pointer;
	  transition: color 0.2s;
	  padding: 0 2px;
	}

	/* Hover & selected state */
	.star-rating label:hover,
	.star-rating label:hover ~ label,
	.star-rating input[type="radio"]:checked ~ label {
	  color: gold;
	}
	.right-related {
		width: 250px;
		border: 1px solid #ccc;
		padding: 12px;
		height: calc(100vh - 100px);
		overflow-y: auto;
		background: #fafafa;
		margin-left: 10px;
		border-radius: 5px;	
		margin-left: 20px;   /* space from article content */
		margin-top: 20px;    /* space from the top bar */
		margin-right: 20px; 
	}

	.right-related h3 {
		margin-top: 0;
		text-align: center;
	}

	.related-item {
		padding: 5px 0;
		border-bottom: 1px solid #eee;
	}

	.related-item a {
		text-decoration: none;
		color: #0073e6;
	}

	.related-item a:hover {
		text-decoration: underline;
	}
	/* Modal background */
	.modal {
		display: none;
		position: fixed;
		z-index: 1000;
		padding-top: 80px;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0,0,0,0.5);
	}

	/* Modal content box */
	.modal-content {
		background-color: #fff;
		margin: auto;
		padding: 20px;
		width: 400px;
		border-radius: 8px;
		border: 1px solid #ccc;
	}

	/* Close button (X) */
	.close {
		float: right;
		font-size: 22px;
		cursor: pointer;
	}
    .sugg{
        margin 20px;
    }
	.selected-link {
		color: #0073e6 !important;
		font-weight: bold;
		text-decoration: underline;
	}


</style>

</head>
<body>

<script>
    function toggleProfileMenu() {
    const menu = document.getElementById("profileMenu");
    menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
}

// Close dropdown when clicking outside
document.addEventListener("click", function(e) {
    const menu = document.getElementById("profileMenu");
    const profile = document.querySelector(".profile-container");

    if (!profile.contains(e.target)) {
        menu.style.display = "none";
    }
});

function showAttachmentBox() {
    document.getElementById("attachmentBox").style.display = "block";
}
function toggleCategory(header) {
    const articlesDiv = header.nextElementSibling;
    const icon = header.querySelector(".icon");
    const categoryName = header.textContent.trim().replace("▶","").replace("▼","");

    if (articlesDiv.style.display === "none") {
        articlesDiv.style.display = "block";
        icon.textContent = "▼";
    } else {
        articlesDiv.style.display = "none";
        icon.textContent = "▶";
    }
}

function expandAll() {
    document.querySelectorAll(".category-articles").forEach(div => div.style.display = "block");
    document.querySelectorAll(".category-header .icon").forEach(icon => icon.textContent = "▼");
}

function collapseAll() {
    document.querySelectorAll(".category-articles").forEach(div => div.style.display = "none");
    document.querySelectorAll(".category-header .icon").forEach(icon => icon.textContent = "▶");
}
function openSuggestionModal() {
    document.getElementById("suggestionModal").style.display = "block";
}

function closeSuggestionModal() {
    document.getElementById("suggestionModal").style.display = "none";
}

// Close popup when clicking outside box
window.onclick = function(event) {
    let modal = document.getElementById("suggestionModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>


<div class="topbar" 
     style="display:flex; align-items:center; gap:15px; background:#1d3557; padding:12px;">

    <!-- Left Welcome Text -->
    <div style="color:white; font-size:20px; font-weight:bold; margin-right:100px;">
      Knowledge Base
    </div>

    <!-- SEARCH BAR -->
    <form method="GET" action="main.php" 
          style="display:flex; align-items:center; gap:10px; margin:0;">

        <input type="text" name="q" placeholder="Search articles..." required
               style="
                    width:350px;
                    padding:12px 50px;
                    border-radius:40px;
                    border:1px solid #ccc;
                    font-size:15px;
               ">

        <!-- Search Icon -->
        <button type="submit" 
            style="
                background:none;
                border:none;
                cursor:pointer;
                font-size:22px;
                color:white;
                padding:8px;
                transition:0.3s;
            "
            title='Search'
        >
            🔍
        </button>

    </form>

    <!-- Add Article Icon -->
    <a href="create_article.php"
        style="
            font-size:24px;
            text-decoration:none;
            color:white;
            padding:5px 10px;
            border-radius:8px;
            transition:0.3s;
        "
        title="Add New Article"
    >
        ➕
    </a>

    <!-- Suggestion Icon -->
    <span onclick="openSuggestionModal()"
        style="
            font-size:24px;
            cursor:pointer;
            color:white;
            padding:5px 10px;
            border-radius:8px;
            transition:0.3s;
        "
        title="Add Suggestion"
    >
        💡
    </span>

    <!-- Logout Icon -->
    <!-- Logout Icon -->
    <!-- USER PROFILE -->
   <!-- USER PROFILE WITH DROPDOWN -->
<div class="profile-container">
    <div class="profile-circle" onclick="toggleProfileMenu()">
        AD
    </div>
    <div class="profile-name">Admin</div>

    <!-- DROPDOWN -->
    <div id="profileMenu" class="profile-dropdown">
        <a href="profile.php">👤 Profile</a>
        <a href="settings.php">⚙️ Settings</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

    <!-- BACK ARROW ICON -->
   

</div>



<div style="display:flex;">

    <!-- LEFT SIDEBAR -->	
	<div class="sidebar">
    <h3>📚 KB Categories</h3>

    <?php foreach ($kbByCategory as $category => $articles): ?>
        <?php $isExpanded = in_array($category, $expandedCategories); ?>

        <div class="category">
            <div class="category-header" onclick="toggleCategory(this)">
                <span class="icon"><?php echo $isExpanded ? "▼" : "▶"; ?></span>
                <?php echo $category; ?>
            </div>

            <div class="category-articles" style="display:<?php echo $isExpanded ? "block" : "none"; ?>;">
                <?php
                    $expandedParam = implode(',', array_merge($expandedCategories, [$category]));
                ?>
                <?php foreach ($articles as $id => $article): ?>
                  <?php
$isSelected = ($selectedId == $id) ? "selected-link" : "";
$isSearchMatch = ($searchQuery && isset($searchResults[$id])) ? "selected-link" : "";
?>

                    
                    <a class="<?php echo "$isSelected $isSearchMatch"; ?>"
   href="main.php?id=<?php echo $id; ?>&expanded=<?php echo urlencode($expandedParam); ?>">
   📄 <?php echo $article["title"]; ?>
</a>

                    
                <?php endforeach; ?>
            </div>
        </div>

    <?php endforeach; ?>
</div>



    <!-- MAIN ARTICLE CONTENT -->
    <div class="content" style="flex:1;">
        <?php if ($searchQuery !== "" && empty($selectedArticle)): ?>

    <h2>Search results for: "<?php echo htmlspecialchars($_GET['q']); ?>"</h2>
    <br>

    <?php if (empty($searchResults)): ?>
        <p>No articles found.</p>
    <?php else: ?>

        <?php foreach ($searchResults as $id => $result): ?>
            <div style="margin-bottom:10px; padding:10px; border-bottom:1px solid #ddd;">
                <a href="main.php?id=<?php echo $id; ?>">
                    <strong><?php echo $result["title"]; ?></strong>
                </a>
                <br>
                <span style="font-size:12px; color:#666;">
                    Category: <?php echo $result["category"]; ?>
                </span>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

<?php endif; ?>

        <?php if ($selectedArticle): ?>

            <!-- Buttons -->
            <div style="margin-bottom: 15px;">
                  <button 
            style="
                background:none;
                border:none;
                cursor:pointer;
                font-size:22px;
                color:white;
                padding:8px;
                transition:0.3s;
            "
            title='Search'
        >
            ✏️
        </button>
              
                  <button 
            style="
                background:none;
                border:none;
                cursor:pointer;
                font-size:22px;
                color:white;
                padding:8px;
                transition:0.3s;
            "
            title='Search'
        >
            🗑️
        </button>
          <button 
          onclick="showAttachmentBox()"
            style="
                background:none;
                border:none;
                cursor:pointer;
                font-size:22px;
                color:white;
                padding:8px;
                transition:0.3s;
            "
            title='Search'
        >
            📎
               
              
                
            </div>

            <h2><?php echo $selectedArticle["title"]; ?></h2>
            <p><?php echo nl2br($selectedArticle["details"]); ?></p>

            <!-- UPLOAD BOX -->
            <div id="attachmentBox" style="display:none; margin-top:20px; padding:10px; border:1px solid #ccc; width:350px; border-radius:6px;">
                <h3>Add Attachment</h3>

                <?php if (!empty($uploadMessage)) echo "<p style='color:green;'>$uploadMessage</p>"; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="article_id" value="<?php echo $selectedId; ?>">
                    <input type="file" name="attachment" required>
                    <br><br>
                    <button class="btn"><p class="pp">Upload </p></button>
                </form>
            </div>

            <!-- ATTACHMENTS LIST -->
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
			
			<h3 style="margin-top:30px;">Rate this Article</h3>

			<form method="POST" action="">
				<div class="star-rating">
					<input type="radio" id="star5" name="rating" value="5"><label for="star5" title="5 stars">★</label>
					<input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 stars">★</label>
					<input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 stars">★</label>
					<input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 stars">★</label>
					<input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 star">★</label>
				</div>
				<br>
				<button type="submit" name="submit_rating" class="btn"><p class="pp">Submit Rating </p></button>
			</form>


        <?php else: ?>
            <h2>Welcome</h2>
            <p>Select a KB article from the left to view details.</p>
        <?php endif; ?>
    </div>

    <!-- RIGHT SIDEBAR (Feedback panel) -->
<!-- RIGHT PANEL - RELATED ARTICLES -->
<div class="right-related">
    <h3>Related Articles</h3>
    <?php
    if (!empty($relatedArticles)) {
        foreach ($relatedArticles as $raId => $raItem) {
            echo "<div class='related-item'>
                    <a href='main.php?id=$raId'>{$raItem['title']}</a>
                  </div>";
        }
    } else {
        echo "<p>No related articles.</p>";
    }
    ?>
</div>

</div>




</div>

<!-- Suggestion Modal -->
<div id="suggestionModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeSuggestionModal()">&times;</span>
        <h2 >Add Suggestion</h2>

        <form method="POST" action="save_suggestion.php">
            
            <label>Suggestion Type:</label><br>
            <br>
            <select name="suggestion_type" required>
                <option value="">-- Select --</option>
                <option value="Improvement">Missing Article</option>
                <option value="Correction">Article Improvement</option>
                <option value="New Article">Issue Fix</option>
            </select><br><br>

            <label >Article:</label><br><br>
            <select name="article_id" required>
                <option value="">-- Select Article --</option>
                <?php foreach ($kb as $id => $item): ?>
                    <option value="<?php echo $id; ?>">
                        <?php echo $item['title']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label>Description:</label><br><br>
            <textarea name="description" rows="5" style="width:100%;" required></textarea><br><br>

            <button class="btn" type="submit"> <p class="pp">Submit Suggestion</p></button>
        </form>

    </div>
</div>



</body>
</html>
