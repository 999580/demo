<?php
// Database connection (replace with your credentials)
$servername = "your servername";
$username = "your username";
$password = "your password";
$dbname = "your dbname";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//User table
$userTableSql = "CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(200) NOT NULL,
    password VARCHAR(200) NOT NULL
)";

$conn->query($userTableSql);

// Uploads table
$uploadsTableSql = "CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    file_name VARCHAR(200) NOT NULL,
    file_path VARCHAR(200) NOT NULL,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
)";

$conn->query($uploadsTableSql);

//Logs table
$logsTableSql = "CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45) NOT NULL,
    file_name VARCHAR(200) NOT NULL,
    upload_status ENUM('success', 'rejected') NOT NULL,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    log_message TEXT,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
)";

$conn->query($logsTableSql);
//User authentication
function is_authenticated() {
    // Implement user authentication logic here
    return true; 
}
//File type validation
function validateFileType($fileExtension) {
    $allowedExtensions = ["jpg", "png", "pdf", "docx"];
    return in_array($fileExtension, $allowedExtensions);
}
//File size limit
$maxFileSize = 5 * 1024 * 1024; // 5MB
if ($_FILES['file']['size'] > $maxFileSize) {
    // Reject upload if file size exceeds the limit
    die("File size exceeds the limit");
}
//File name sanitization
function sanitizeAndGenerateFileName($originalFileName) {
    $sanitizedFileName = pathinfo($originalFileName, PATHINFO_FILENAME);
    $uniqueFileName = $sanitizedFileName . '_' . uniqid() . '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
    return $uniqueFileName;
}
//File upload directory
function handleFileUpload($conn, $userId) {
    
    logToFile($conn, $userId, $_SERVER['REMOTE_ADDR'], $fileName, $uploadStatus, $logMessage);
}

//Logging
function logToFile($conn, $userId, $ipAddress, $fileName, $uploadStatus, $logMessage) {
    $logInsertSql = "INSERT INTO logs (user_id, ip_address, file_name, upload_status, log_message) 
                     VALUES ('$userId', '$ipAddress', '$fileName', '$uploadStatus', '$logMessage')";
    $conn->query($logInsertSql);
}
$conn->close();
//Security Headers
header("Content-Security-Policy: default-src 'self';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
?>
Testing:
1 Test the system with various file types, sizes, and scenarios.
2 Verify that logs are correctly recorded in the database.
3 Ensure that security headers are present in the HTTP response.
4 Confirm that unauthorized users cannot upload files.
