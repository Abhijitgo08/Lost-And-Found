<?php
function levenshteinDistance($str1, $str2) {
    $len1 = strlen($str1);
    $len2 = strlen($str2);
    
    $dp = array_fill(0, $len1 + 1, array_fill(0, $len2 + 1, 0));
    
    for ($i = 0; $i <= $len1; $i++) {
        $dp[$i][0] = $i;
    }
    
    for ($j = 0; $j <= $len2; $j++) {
        $dp[0][$j] = $j;
    }
    
    for ($i = 1; $i <= $len1; $i++) {
        for ($j = 1; $j <= $len2; $j++) {
            $cost = ($str1[$i - 1] != $str2[$j - 1]) ? 1 : 0;
            
            $dp[$i][$j] = min(
                $dp[$i - 1][$j] + 1,
                $dp[$i][$j - 1] + 1,
                $dp[$i - 1][$j - 1] + $cost
            );
        }
    }
    
    return $dp[$len1][$len2];
}

function fuzzyRatio($str1, $str2) {
    $distance = levenshteinDistance($str1, $str2);
    
    $maxLen = max(strlen($str1), strlen($str2));
    
    if ($maxLen == 0) {
        return 100.0;
    }
    
    return ((($maxLen - $distance) / $maxLen) * 100.0);
}

$host = 'localhost';
$dbname = 'college_lost_and_found';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $itemName = $_POST['itemName'] ?? '';
    
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $itemName)) {
        throw new Exception("Invalid input");
    }

    $stmt = $conn->prepare("SELECT hiddenDetails, contactNumber FROM uploads WHERE itemName = :itemName");
    $stmt->bindParam(':itemName', $itemName);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $hiddenDetail = $result['hiddenDetails'];
        $contactNumber = $result['contactNumber'];

        $claimDetail = $_POST['claimDetail'] ?? '';

        $fuzzyRatio = fuzzyRatio($hiddenDetail, $claimDetail);

        $threshold = 45;

        if ($fuzzyRatio >= $threshold) {
            echo "<div class='popup-content'>";
            echo "<p>Correct description!</p>";
            echo "<p>Collect your items by contacting the user on this contact no.: " . $contactNumber . "</p>";
            echo "<button onclick='closePopup()' class='orange-btn'>Close</button>";
            echo "</div>";
        } else {
            echo "<div class='popup-content'>";
            echo "<p>Your hidden description doesn't match.</p>";
            echo "<button onclick='closePopup()' class='orange-btn'>Close</button>";
            echo "</div>";
        }
    } else {
        echo "<div class='popup-content'>";
        echo "<p>Item not found!</p>";
        echo "<button onclick='closePopup()' class='orange-btn'>Close</button>";
        echo "</div>";
    }

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo "<div class='popup-content'>";
    echo "<p>An error occurred while processing your request.</p>";
    echo "<button onclick='closePopup()' class='orange-btn'>Close</button>";
    echo "</div>";
} catch (Exception $e) {
    error_log("Validation Error: " . $e->getMessage());
    echo "<div class='popup-content'>";
    echo "<p>Invalid input.</p>";
    echo "<button onclick='closePopup()' class='orange-btn'>Close</button>";
    echo "</div>";
}
?>
