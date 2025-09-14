<?php
$host = 'localhost';
$dbname = 'college_lost_and_found';
$username = 'root'; // Default username 
$password = ''; // Default pass

$message = '';

try {
    // Testing db
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $itemName = $_POST['itemName'] ?? '';
        $description = $_POST['description'] ?? '';
        $hiddenDetails = $_POST['hiddenDetails'] ?? '';
        $contactNumber = $_POST['contactNumber'] ?? '';

        // File upload handling
        if (isset($_FILES['imageUpload'])) {
            $file = $_FILES['imageUpload'];
            $fileTmpName = $file['tmp_name'];

            $fileData = file_get_contents($fileTmpName);  
            try {
                $stmt = $conn->prepare("INSERT INTO uploads (itemName, description, hiddenDetails, contactNumber, imageFile) VALUES (:itemName, :description, :hiddenDetails, :contactNumber, :imageFile)");
                $stmt->bindParam(':itemName', $itemName);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':hiddenDetails', $hiddenDetails);
                $stmt->bindParam(':contactNumber', $contactNumber);
                $stmt->bindParam(':imageFile', $fileData, PDO::PARAM_LOB);  

                if ($stmt->execute()) {
                    $message = "Item uploaded successfully!";
                } else {
                    $message = "Error executing SQL query: " . print_r($stmt->errorInfo(), true);
                }
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        } else {
            $message = "No file uploaded";
        }
    }
} catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Items on Campus</title>
    <link rel="stylesheet" href="upload.css">
</head>
<body>

<div class="container">
    <h1>List The Item You Found</h1>
    
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    
    <form id="addItemForm" action="" method="post" enctype="multipart/form-data">
        <label for="itemName">Item Name:</label>
        <input type="text" id="itemName" name="itemName" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="3" required placeholder="A brief description of the item"></textarea>
        
        <label for="hiddenDetails">Hidden Details:</label>
        <textarea id="hiddenDetails" name="hiddenDetails" rows="3" required placeholder="Enter at least two or more hidden descriptions you noticed"></textarea>
        
        <label for="contactNumber">Contact Number:</label>
        <input type="text" id="contactNumber" name="contactNumber" required>
        
        <label for="imageUpload">Upload Image:</label>
        <input type="file" id="imageUpload" name="imageUpload" accept="image/*" required multiple>
        
        <div id="imagePreviewContainer"></div>
        
        <button type="submit" id="submitButton">Submit</button>
    </form>
    
    <div id="lostItems"></div>
</div>

<script src="upload.js"></script>

</body>
</html>
