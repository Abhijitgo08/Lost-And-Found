<?php
// Database connection
$host = 'localhost';
$dbname = 'college_lost_and_found';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT itemName, description, imageFile, dateUploaded FROM uploads");
    $stmt->execute();

    $lostItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <div class="container">
        <header>
            <!-- Title -->
            <h1 id="home-title">Home</h1>
            <!-- Search box -->
            <input type="text" id="search-box" placeholder="Search..." onkeypress="searchItems(event)">
            <button id="add-found-btn" onclick="window.location.href='upload.php'">Report Found</button>
        </header>
        <main>
            <section id="lost-section">
                <h2>Lost Items</h2>
                <!-- Search information -->
                <div id="search-info"></div>
                <ul id="lost-items">
                    <!-- PHP code to fetch and display items -->
                    <?php
                    foreach ($lostItems as $item) {
                        echo '<li>';
                        echo '<div class="item-container">';
                        echo '<div class="item">';
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($item['imageFile']) . '" alt="' . htmlspecialchars($item['itemName']) . '">';
                        echo '</div>';
                        echo '<div class="details">';
                        echo '<div class="description">';
                        echo '<h3>' . htmlspecialchars($item['itemName']) . '</h3>';
                        echo '<p><strong>Description</strong>: ' . htmlspecialchars($item['description']) . '</p>';
                        echo '</div>';
                        echo '<div class="upload-date">';
                        echo '<p><strong>Date Uploaded</strong>: ' . htmlspecialchars($item['dateUploaded']) . '</p>'; // Display dateUploaded
                        echo '</div>';
                        echo '</div>';
                        echo '<button class="claim-btn" onclick="claimItem(\'' . $item['itemName'] . '\')">Claim Your Item</button>'; // Claim button
                        echo '</div>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </section>
            <!-- Popup container -->
            <div id="claimPopup" class="popup-container"></div>
        </main>
    </div>
    <script src="home.js"></script>
    <script src="searchbox.js"></script>
    <script>
        function claimItem(itemName) {
            var claimDetail = prompt("Enter Hidden Detail of Your Item:");

            if (claimDetail !== null) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'claim-button.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                xhr.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        var claimPopup = document.getElementById("claimPopup");
                        claimPopup.innerHTML = this.responseText;
                        claimPopup.style.display = "block";
                    }
                };

                xhr.send('itemName=' + encodeURIComponent(itemName) + '&claimDetail=' + encodeURIComponent(claimDetail));
            }
        }

        function closePopup() {
            var claimPopup = document.getElementById("claimPopup");
            claimPopup.style.display = "none";
            claimPopup.innerHTML = ""; 
        }
    </script>
</body>
</html>
